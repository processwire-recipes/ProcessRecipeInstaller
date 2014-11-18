<?php

class ProcessRecipeInstallerConfig extends ModuleConfig {

    public function getDefaults() {
        return array(
            'recipesPath' => 'assets/recipes',
            'recipesRepo' => 'https://github.com/processwire-recipes/Recipes.git',
            'recipesParent' => '/recipes/',
            'tagsParent' => '/recipe-tags/',
            'authorsParent' => '/recipe-authors/',
        );
    }

    public function __construct() {
        $defaults = $this->getDefaults();
        $this->add(array(
            array(
                'name' => 'recipesPath',
                'label' => 'Recipes Path',
                'description' => 'relative to the site directory',
                'type' => 'text',
                'required' => true,
                'columnWidth' => 33,
                'value' => $defaults['recipesPath'],
            ),
            array(
                'name' => 'recipesRepo',
                'label' => 'Recipes Repo',
                'description' => 'URL to Git repository, where version controlled text files reside',
                'type' => 'text',
                'required' => true,
                'columnWidth' => 67,
                'value' => $defaults['recipesRepo'],
            ),
            array(
                'name' => 'recipesParent',
                'label' => 'Recipes parent page',
                'description' => 'where repices reside',
                'type' => 'text',
                'required' => true,
                'columnWidth' => 33,
                'value' => $defaults['recipesParent'],
            ),
            array(
                'name' => 'tagsParent',
                'label' => 'Tags parent page',
                'description' => 'where tags reside',
                'type' => 'text',
                'required' => true,
                'columnWidth' => 34,
                'value' => $defaults['tagsParent'],
            ),
            array(
                'name' => 'authorsParent',
                'label' => 'Authors parent page',
                'description' => 'where authors reside',
                'type' => 'text',
                'required' => true,
                'columnWidth' => 33,
                'value' => $defaults['authorsParent'],
            ),
        ));
    }
}
