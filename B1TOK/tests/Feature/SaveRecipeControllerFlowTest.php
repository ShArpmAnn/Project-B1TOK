<?php

namespace Tests\Feature;

use App\Models\SaveRecipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SaveRecipeControllerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_recipe(){
        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($user);

        $recipeData = [
            'title' => 'title',
            'callorage' => 2000,
            'proteins' => 89,
            'fats' => 39,
            'carbohydrates' => 150,
            'food' => 'ananas qiwi bananas',
        ];

        $response = $this->post(route('new_recipes'), $recipeData);
        $response->assertRedirect('/recipes');
        $response->assertSessionHas('success', 'Рецепт успешно сохранён');

        $this->assertDatabaseHas('save_recipes', [
            'title' => 'title',
            'callorage' => 2000,
            'proteins' => 89,
            'fats' => 39,
            'carbohydrates' => 150,
        ]);

        $recipe = SaveRecipe::where('title', 'title')->first();
        $this->assertEquals(['ananas', 'qiwi', 'bananas'], $recipe->food);
    }

    public function test_update_recipe(){
        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($user);

        $recipeData = [
            'title' => 'title',
            'callorage' => 2000,
            'proteins' => 89,
            'fats' => 39,
            'carbohydrates' => 150,
            'food' => 'ananas qiwi bananas',
        ];

        $response = $this->post(route('new_recipes'), $recipeData);
        $response->assertRedirect('/recipes');
        $response->assertSessionHas('success', 'Рецепт успешно сохранён');

        $recipe = SaveRecipe::where('title', 'title')->first();
        $recipeData = [
            'id' => $recipe->id,
            'title' => 'title_now',
            'callorage' => 2111,
            'proteins' => 213,
            'fats' => 3219,
            'carbohydrates' => 15210,
            'food' => 'ananas bananas',
        ];

        $response = $this->post(route('update_recipes'), $recipeData);
        $response->assertRedirect('/recipes');
        $response->assertSessionHas('success', 'Рецепт успешно обновлён');


        $this->assertDatabaseHas('save_recipes', [
            'title' => 'title_now',
            'callorage' => 2111,
            'proteins' => 213,
            'fats' => 3219,
            'carbohydrates' => 15210,
        ]);

        $recipe = SaveRecipe::where('title', 'title_now')->first();
        $this->assertEquals(['ananas', 'bananas'], $recipe->food);
    }

    public function test_delete_recipe()
    {
        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($user);


        $recipeData = [
            'title' => 'Рецепт для удаления',
            'callorage' => 2000,
            'proteins' => 89,
            'fats' => 39,
            'carbohydrates' => 150,
            'food' => 'ananas qiwi bananas',
        ];

        $response = $this->post(route('new_recipes'), $recipeData);
        $response->assertRedirect('/recipes');
        $response->assertSessionHas('success', 'Рецепт успешно сохранён');


        $recipe = SaveRecipe::where('title', 'Рецепт для удаления')->first();

        $deleteData = [
            'id' => $recipe->id,
        ];

        $response = $this->post(route('delete_recipes'), $deleteData);
        $response->assertRedirect('/recipes');
        $response->assertSessionHas('success', 'Рецепт успешно удален');


        $this->assertDatabaseMissing('save_recipes', [
            'id' => $recipe->id,
            'title' => 'Рецепт для удаления',
        ]);
    }
}
