<?php

/**
 * Tests for ProcessTestFest.module
 */

use \ProcessWireRecipes\Recipe as Recipe;

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

            $r = new Recipe($recipeName, $someRecipe);

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
            
        
        $this->newTest('Recipe::construct, flaky recipe source');

            $recipeName = 'some-other-recipe';
            $someRecipe = file_get_contents("{$this->src}/{$recipeName}.txt");

            $r = new Recipe($recipeName, $someRecipe);

            $this->assertIdentical($r->version, '0.0.0');

            $this->assertArray($r->authors);
            $this->assertIdentical(count($r->authors), 0);

            $this->assertArray($r->tags);
            $this->assertIdentical(count($r->tags), 0);
    }
    
    function caseRecipeConstants() {
        $this->newTest('Recipe constants');
        
            // make sure constants have a uique value
            $constants = array(
                Recipe::STATUS_NOT_INSTALLED => Recipe::STATUS_NOT_INSTALLED,
                Recipe::STATUS_OLDER_VERSION_INSTALLED => Recipe::STATUS_OLDER_VERSION_INSTALLED,
                Recipe::STATUS_INSTALLED => Recipe::STATUS_INSTALLED,
                Recipe::STATUS_NEWER_VERSION_INSTALLED => Recipe::STATUS_NEWER_VERSION_INSTALLED,
                Recipe::TEMPLATE_NAME_RECIPE => Recipe::TEMPLATE_NAME_RECIPE,
                Recipe::TEMPLATE_NAME_RECIPE_TAG => Recipe::TEMPLATE_NAME_RECIPE_TAG,
                Recipe::TEMPLATE_NAME_RECIPE_AUTHOR => Recipe::TEMPLATE_NAME_RECIPE_AUTHOR,
            );
            $this->assertIdentical(count($constants), 7);
    }
}
