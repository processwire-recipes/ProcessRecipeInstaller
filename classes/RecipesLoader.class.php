<?php

namespace ProcessWireRecipes;

class RecipesLoader {

    const FILE_NAME_PATTERN = "/(^[a-z\-]+\.(?:txt|md)$)/";

    protected static $instance;

    protected $paths = array();
    public $recipes = array();

    private function __construct() {}

    public static function loadPath($paths) {
        $instance = (self::$instance) ? self::$instance : self::$instance = new self();
        $instance->addPaths($paths);
        $instance->getRecipes();
        return $instance;
    }

    protected function addPaths($paths) {
        if (is_string($paths)) {
            $this->addPath($paths);
        } else if (is_array($paths) && isset($paths[0])) {
            foreach ($paths as $path) {
                $this->addPath($path);
            }
        } else {
            throw new Exception('RecipesLoader::construct: \'$paths\' must either be an array of paths or a path string');
        }
        return $this;
    }

    protected function addPath($path) {
        if (!in_array($path, $this->paths)) {
            $this->paths[] = $path;
        }
    }


    public function getRecipes() {

        foreach ($this->paths as $path) {
            if (!file_exists($path)) continue;
            $files = scandir($path);
            foreach ($files as $file) {

                if (preg_match(self::FILE_NAME_PATTERN, $file)) {
                    $name = str_replace('.txt', '', $file);
                    $name = str_replace('.md', '', $name);
                    $str = file_get_contents("{$path}/{$file}");
                    $this->recipes[$name] = new Recipe($name, $str);
                }
            }
        }

        return $this->recipes;
    }
}
