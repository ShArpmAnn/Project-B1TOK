<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WeightControllerFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_weight(){

        $userData = [
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => 'SecurePassword123!@#',
            'password_confirmation' => 'SecurePassword123!@#',
        ];

        // Act
        $response = $this->post('/register', $userData);

        // Assert
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Регистрация успешна');

        $weightData = [
            'gender' => 'male',
            'start_weight' => 60,
            'height' => 175,
            'old' => 23,
            'end_weight' => 70,
            'activity' => 'medium',
            'choose' => 'increase',
            'temp' => 'fast',

        ];

        $response = $this->post(route('create_weight'), $weightData);

        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');

        // Проверяем создание цели в базе
        $this->assertDatabaseHas('weights', [
            'start_weight' => 60,
            'end_weight' => 70,
        ]);

        $this->assertDatabaseHas('weights', [
            'callorage' => 2955,
        ]);
    }

    public function test_weight_secondary(){

        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($user);

        $weightData = [
            'gender' => 'male',
            'start_weight' => 60,
            'height' => 175,
            'old' => 23,
            'end_weight' => 70,
            'activity' => 'medium',
            'choose' => 'increase',
            'temp' => 'fast',
        ];

        $response = $this->post(route('create_weight'), $weightData);

        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');

        $weightData = [
            'gender' => 'male',
            'start_weight' => 70,
            'height' => 176,
            'old' => 24,
            'end_weight' => 80,
            'activity' => 'big',
            'choose' => 'increase',
            'temp' => 'slow',
        ];

        $response = $this->post(route('create_weight'), $weightData);
        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');
        $this->assertDatabaseHas('weights', [
            'start_weight' => 70,
            'end_weight' => 80,
            'used_now' => 1,
        ]);

        $this->assertDatabaseHas('weights', [
            'start_weight' => 60,
            'end_weight' => 70,
            'used_now' => 0,
        ]);
    }

    public function test_weight_update_all(){

        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);
        $this->actingAs($user);

        $weightData = [
            'gender' => 'male',
            'start_weight' => 60,
            'height' => 175,
            'old' => 23,
            'end_weight' => 70,
            'activity' => 'medium',
            'choose' => 'increase',
            'temp' => 'fast',

        ];

        $response = $this->post(route('create_weight'), $weightData);

        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');

        $weightUpdateData = [
            'gender' => 'male',
            'start_weight' => 65,
            'height' => 170,
            'old' => 20,
            'end_weight' => 75,
            'activity' => 'medium',
            'choose' => 'drop',
            'temp' => 'slow',
            'now_weight' => 68,
        ];

        $response = $this->post(route('update_weight'), $weightUpdateData);
        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель успешно обновлена');

        $this->assertDatabaseHas('weights', [
            'start_weight' => 65,
            'end_weight' => 75,
            'callorage' => 2207,
            'now_weight' => 68,
        ]);
    }

    public function test_weight_update_now(){

        $user = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'ivandod28@gmail.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($user);

        $weightData = [
            'gender' => 'male',
            'start_weight' => 60,
            'height' => 175,
            'old' => 23,
            'end_weight' => 70,
            'activity' => 'medium',
            'choose' => 'increase',
            'temp' => 'fast',

        ];

        $response = $this->post(route('create_weight'), $weightData);

        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');

        $weightUpdateData = [
            'now_weight' => 68,
        ];

        $response = $this->post(route('update_now_weight'), $weightUpdateData);
        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Вес успешно обновлён');

        $this->assertDatabaseHas('weights', [
            'start_weight' => 60,
            'end_weight' => 70,
            'now_weight' => 68,
        ]);
    }
}
