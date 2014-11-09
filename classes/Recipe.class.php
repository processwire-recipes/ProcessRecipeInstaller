<?php

namespace ProcessWireRecipes;

use \Page;

class Recipe extends \WireData {

    const STATUS_NOT_INSTALLED = 0;
    const STATUS_OLDER_VERSION_INSTALLED = 1;
    const STATUS_INSTALLED = 2;
    const STATUS_NEWER_VERSION_INSTALLED = 3;

    const TEMPLATE_NAME_RECIPE = 'recipe';
    const TEMPLATE_NAME_RECIPE_TAG = 'recipe-tag';
    const TEMPLATE_NAME_RECIPE_AUTHOR = 'recipe-author';

    protected $raw = '';

    public $title = '';
    public $name = '';
    public $version = '';
    public $authors = array();
    public $problem = '';
    public $solution = '';
    public $tags = array();
    public $resources = '';

    protected $pri;

    protected static $pageSelectTemplates = array(
        'tags' => self::TEMPLATE_NAME_RECIPE_TAG,
        'authors' => self::TEMPLATE_NAME_RECIPE_AUTHOR,
    );

    protected static $recipeFieldNames = array(
        'title',
        'name',
        'version',
        'authors',
        'problem',
        'solution',
        'tags',
        'resources',
    );



    public function __construct($name, $raw) {


        $this->pri = $this->modules->get('ProcessRecipeInstaller');

        $this->name = $name;

        // remove the BOM
        $this->raw = str_replace("\xEF\xBB\xBF", '', $raw);

        // explode all fields by the line separator
        $fields = explode("\n----", $this->raw);

        // loop through all fields and add them to the content
        foreach($fields as $field) {
            $pos = strpos($field, ':');
            $key = str_replace(array('-', ' '), '_', strtolower(trim(substr($field, 0, $pos))));

            // Don't add fields with empty keys or keys that are not part of this class
            if(empty($key) || !in_array($key, self::$recipeFieldNames)) continue;

            $value = trim(substr($field, $pos+1));

            switch (true) {
                case self::isPageSelect($key):
                    $this->$key = $this->processArray($value);
                break;

                default:
                    $this->$key = $value;
                    break;
            }
        }

        if (!$this->version) {
            $this->version = '0.0.0';
        }
    }



    protected function processArray($string) {
        $strArr = explode(',', $string);
        $rtnArr = array();
        foreach ($strArr as $item) {
            $item = trim($item);
            if ($item) {
                $rtnArr[] = $item;
            }
        }
        return $rtnArr;
    }



    public function install() {

        $parent = $this->pages->get($this->pri->recipesParent);
        $p = $this->pages->get($this->pri->recipesParent . $this->name);

        if (!$p->id) {
            $p = new Page();
            $p->template = self::TEMPLATE_NAME_RECIPE;
            $p->parent = $parent;
        }

        foreach (self::$recipeFieldNames as $fieldName) {

            $value = $this->$fieldName;

            switch (true) {
                case self::isPageSelect($fieldName):
                    $templateName = self::$pageSelectTemplates[$fieldName];
                    $this->assignPageSelectValues($p, $fieldName, $value, $templateName);
                    break;

                default:
                    $p->$fieldName = $this->$fieldName;
                    break;
            }
        }
        $p->save();
    }



    public function uninstall() {
        $p = $this->pages->get($this->pri->recipesParent . $this->name);
        $this->pages->delete($p, $recursive = true);
    }



    protected function assignPageSelectValues($page, $fieldName, $values, $templateName) {

        $configName = "{$fieldName}Parent";
        $configValue = $this->pri->$configName;
        $singleValue = false;

        // put single value in array so foreach
        // can be applied with both single and multiple values
        if (is_string($values)) {
            $values = array($values);
            $singleValue = true;
        }

        $parent = $this->pages->get($configValue);

        foreach ($values as $value) {
            $valueName = $this->sanitizer->pageName($value);
            $valuePage = $this->pages->get($configValue . $valueName);
            if (!$valuePage->id) {
                $valuePage = new Page();
                $valuePage->template = $templateName;
                $valuePage->parent = $parent;
                $valuePage->name = $valueName;
                $valuePage->title = $value;
                $valuePage->save();
                $valuePage = $this->pages->get($configValue . $valueName);
            }

            if ($singleValue) {
                $page->$fieldName = $valuePage->id;
            } else {
                $page->$fieldName->add($valuePage->id);
            }

        }
    }



    public function getInstalled() {
        $page = $this->pages->get($this->pri->recipesParent . $this->name);
        return $page->id ? $page : null;
    }



    public function getStatus($version = null) {

        if ($page = $this->getInstalled()) {

            if ($page->version === $this->version) {
                return self::STATUS_INSTALLED;
            } else {
                if (version_compare($this->version, $page->version) > 0) {
                    return self::STATUS_OLDER_VERSION_INSTALLED;
                } else {
                    return self::STATUS_NEWER_VERSION_INSTALLED;
                }
            }

        } else {
            return self::STATUS_NOT_INSTALLED;
        }
    }



    public function getInstalledVersion() {
        $page = $this->pages->get($this->pri->recipesParent . $this->name);
        return isset($page->version) && $page->version ? $page->version : 0;
    }



    protected static function isPageSelect($fieldName) {
        return array_key_exists($fieldName, self::$pageSelectTemplates) ? true : false;
    }
}
