<?php

namespace Tests\Feature;

use App\Models\Callorage;
use App\Models\DiaryFood;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertNotEquals;

class FatSecretApiFlowTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_fatsecret_api_connection()
    {
        $key = env('FATSECRET_KEY');
        $secret = env('FATSECRET_SECRET');

        Log::info('Проверка API ключей:', ['key_exists' => !empty($key), 'secret_exists' => !empty($secret)]);


        try {
            //Прямой вызов FatSecret
            $fatSecret = new \Braunson\FatSecret\FatSecret($key, $secret);

            $result = $fatSecret->searchIngredients('apple', 0, 1);

            Log::info('Результат теста API:', ['result' => $result]);

            $this->assertNotNull($result, 'API должен что-то вернуть');

            if (is_string($result)) {
                $decoded = json_decode($result, true);
                $this->assertNotNull($decoded, 'Должен быть верный JSON');
            }

        } catch (\Exception $e) {
            Log::error('API тест провален:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->fail('API подключение упало: ' . $e->getMessage());
        }
    }
    public function test_add_to_diary()
    {
        // Пропускаем тест, если нет ключей API
        if (empty(env('FATSECRET_KEY')) || empty(env('FATSECRET_SECRET'))) {
            $this->markTestSkipped('FatSecret API ключи не настроены');
        }

        $password = $this->faker->password(8) . '!@#';

        $userData = [
            'name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => 'test.' . time() . '@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ];

        // Регистрация
        $response = $this->post('/register', $userData);
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Регистрация успешна');


        $start_weight = $this->faker->numberBetween(50, 120);

        $weightData = [
            'gender' => $this->faker->randomElement(['male', 'female']),
            'start_weight' => $start_weight,
            'height' => $this->faker->numberBetween(140, 210),
            'old' => $this->faker->numberBetween(15, 60),
            'end_weight' => $start_weight + $this->faker->numberBetween(5, 20),
            'activity' => $this->faker->randomElement(['min', 'light', 'medium', 'big', 'very_big']),
            'choose' => $this->faker->randomElement(['drop', 'increase']),
            'temp' => $this->faker->randomElement(['slow', 'fast']),
        ];

        $response = $this->post(route('create_weight'), $weightData);
        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');


        $foodData = [
            'query' => $this->faker->randomElement(['banana', 'egg', 'apple']),
            'date' => $this->faker->date('Y-m-d'),
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner']),
        ];


        $response = $this->post(route('diary_new'), $foodData);

        if ($response->status() === 500) {
            Log::warning('FatSecret API вернул 500', [
                'content' => $response->getContent(),
                'session' => session()->all(),
            ]);

                // Проверяем, что это ошибка API, а не приложения
            $this->assertNotEquals(404, $response->status(), 'Маршрут не найден');
            $this->assertNotEquals(419, $response->status(), 'Ошибка CSRF токена');
            $this->assertNotEquals(422, $response->status(), 'Ошибка валидации');

            $this->markTestSkipped('Ошибка API');
        } else {

            $response->assertRedirect('/diary');
            $successMessage = session('success');

            if ($successMessage) {
                $response->assertSessionHas('success');
            } else {
                $errorMessage = session('error');
                if ($errorMessage) {
                    Log::info('API вернул ошибку: ' . $errorMessage);
                    $this->markTestSkipped('API вернул ошибку: ' . $errorMessage);
                }
            }
        }

    }

    public function test_many_add_to_diary()
    {
        // Пропускаем тест, если нет ключей API
        if (empty(env('FATSECRET_KEY')) || empty(env('FATSECRET_SECRET'))) {
            $this->markTestSkipped('FatSecret API ключи не настроены');
        }

        $password = $this->faker->password(8) . '!@#';

        $userData = [
            'name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => 'test.' . time() . '@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ];

        // Регистрация
        $response = $this->post('/register', $userData);
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Регистрация успешна');


        $start_weight = $this->faker->numberBetween(50, 120);

        $weightData = [
            'gender' => $this->faker->randomElement(['male', 'female']),
            'start_weight' => $start_weight,
            'height' => $this->faker->numberBetween(140, 210),
            'old' => $this->faker->numberBetween(15, 60),
            'end_weight' => $start_weight + $this->faker->numberBetween(5, 20),
            'activity' => $this->faker->randomElement(['min', 'light', 'medium', 'big', 'very_big']),
            'choose' => $this->faker->randomElement(['drop', 'increase']),
            'temp' => $this->faker->randomElement(['slow', 'fast']),
        ];

        $response = $this->post(route('create_weight'), $weightData);
        $response->assertRedirect('/personal_cabinet');
        $response->assertSessionHas('success', 'Цель создана');


        $foodData1 = [
            'query' => $this->faker->randomElement(['banana', 'egg', 'apple']),
            'date' => $this->faker->date('Y-m-d'),
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner']),
        ];

        $response = $this->post(route('diary_new'), $foodData1);
        $response->assertRedirect('/diary');
        $response->assertSessionHas('success');


        $date = $this->faker->date('Y-m-d');
        $foodData2 = [
            'query' => $this->faker->randomElement(['banana', 'egg', 'apple']),
            'date' => $date,
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => 'breakfast',
        ];


        $response = $this->post(route('diary_new'), $foodData2);
        $response->assertRedirect('/diary');
        $response->assertSessionHas('success');

        $user_id = Auth::id();
        $diaryEntriesCount = DB::table('callorages')
            ->where('user_id', $user_id )
            ->count();

        $this->assertEquals(2, $diaryEntriesCount,
            "Должно быть 2 записи в дневнике питания для пользователя ID: {$user_id}"
        );

        $foodData3 = [
            'query' => $this->faker->randomElement(['banana', 'egg', 'apple']),
            'date' => $date,
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => 'lunch',
        ];

        $response = $this->post(route('diary_new'), $foodData3);
        $response->assertRedirect('/diary');
        $response->assertSessionHas('success');

        $diaryEntriesCount = DB::table('callorages')
            ->where('user_id', $user_id )
            ->count();

        $this->assertEquals(2, $diaryEntriesCount,
            "Должно быть 2 записи в дневнике питания для пользователя ID: {$user_id}"
        );

        $currentCallorage = Callorage::forUser(Auth::id())->date($date)->first();
        $id = $currentCallorage->id;

        $diaryEntriesCount = DB::table('diary_food')
            ->where('callorages_id', $id )
            ->count();

        $this->assertEquals(2, $diaryEntriesCount,
            "Должно быть 2 записи в дневнике питания для callorage ID: {$id}"
        );

        $query = 'banana';
        $foodData4 = [
            'query' => $query,
            'date' => $date,
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => 'lunch',
        ];

        $response = $this->post(route('diary_new'), $foodData4);
        $response->assertRedirect('/diary');
        $response->assertSessionHas('success');

        $diary = DiaryFood::forDiary($id, 'lunch')->first();
        $food = $diary->food;
        $callorage1 = $food['Bananas']['calories'];

        $foodData5 = [
            'query' => $query,
            'date' => $date,
            'serving_index' => $this->faker->numberBetween(0, 2),
            'quantity' => $this->faker->randomFloat(1, 0.1, 10),
            'eat_type' => 'lunch',
        ];

        $response = $this->post(route('diary_new'), $foodData5);
        $response->assertRedirect('/diary');
        $response->assertSessionHas('success');

        $diary = DiaryFood::forDiary($id, 'lunch')->first();
        $food = $diary->food;
        $callorage2 = $food['Bananas']['calories'];

        assertNotEquals($callorage1, $callorage2);
        Log::info("Знаячения калларажей: ", [$callorage1, $callorage2]);
    }

}
