<?php

/**
 * Tests for ProcessTestFest.module
 */

class RecipeInstallerTests extends \TestFest\TestFestSuite {

    function init() {

        $dir = dirname(__FILE__);
        $this->src = "$dir/src";
        $this->classes = "$dir/../classes";
    }

    function caseRecipe() {

        require_once "{$this->classes}/Recipe.class.php";

        $this->newTest('Recipe::construct');

        $recipeName = 'some-recipe';
        $someRecipe = file_get_contents("{$this->src}/{$recipeName}.txt");

        $r = new \ProcessWireRecipes\Recipe($recipeName, $someRecipe);

        $this->assertIdentical($r->name, 'some-recipe');
        $this->assertIdentical($r->title, 'Some awesome recipe');

        $this->assertIdentical($r->version, '1.2.1');

        $this->assertArray($r->authors);
        $this->assertIdentical($r->authors[0], 'owzim');
        $this->assertIdentical(count($r->authors), 1);

        $this->assertArray($r->tags);
        $this->assertIdentical($r->tags[0], 'awesome');
        $this->assertIdentical(count($r->tags), 3);

        $this->assertIdentical($r->problem, 'MarkDown problem - Duis aute irure.');
        $this->assertIdentical($r->solution, 'MarkDown solution - Duis aute irure.');
        $this->assertIdentical($r->resources, 'MarkDown resources - Duis aute irure.');
    }
}
