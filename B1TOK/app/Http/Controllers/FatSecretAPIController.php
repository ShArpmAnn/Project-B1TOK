<?php

namespace App\Http\Controllers;

use Braunson\FatSecret\Facade as FatSecret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FatSecretAPIController extends Controller
{
    /**
     * Получение информации о продукте и добавление в дневник
     */
    public function AddToDiary(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'date' => 'required|date',
            'serving_index' => 'sometimes|integer|min:0',
            'quantity' => 'sometimes|numeric|min:0.1',
            'eat_type' => 'required|string',
        ]);

        try {
            Log::info('FatSecret API поиск продуктов', ['query' => $request->query]);

            // Ищем продукты
            $searchResult = FatSecret::searchIngredients(
                $request->input('query'),
                1,
                10
            );

            Log::info('Результат поиска FatSecret', ['result' => $searchResult]);

            // Форматируем результаты
            $formattedResult = $this->formatSearchResults($searchResult);

            if (empty($formattedResult['foods'])) {
                return redirect()->route('diary')->with('error', 'Продукт не найден');
            }

            // Берем первый найденный продукт
            $firstFood = $formattedResult['foods'][0];
            $foodId = $firstFood['id'] ?? null;

            if (!$foodId) {
                return redirect()->route('diary')->with('error', 'Не удалось получить ID продукта');
            }

            Log::info('Получение информации о продукте', ['food_id' => $foodId]);

            // Получаем информацию о продукте
            $result = FatSecret::getIngredient($foodId);

            Log::info('Детали продукта FatSecret', ['result' => $result]);

            $foodData = $this->formatFoodDetails($result);

            if (empty($foodData)) {
                return redirect()->route('diary')->with('error', 'Информация о продукте не найдена');
            }

            // Получаем выбранную порцию
            $servingIndex = $request->input('serving_index', 0);
            $quantity = floatval($request->input('quantity', 1));

            if (empty($foodData['servings'])) {
                return redirect()->route('diary')->with('error', 'Информация о порциях не найдена');
            }

            $selectedServing = $foodData['servings'][$servingIndex] ?? $foodData['servings'][0];

            // Рассчитываем итоговые значения КБЖУ
            $nutritionData = [
                'food_id' => $foodData['id'],
                'name' => $foodData['name'] ?? 'Неизвестный продукт',
                'calories' => ($selectedServing['calories'] ?? 0) * $quantity,
                'proteins' => ($selectedServing['protein'] ?? 0) * $quantity,
                'fats' => ($selectedServing['fat'] ?? 0) * $quantity,
                'carbohydrates' => ($selectedServing['carbohydrates'] ?? 0) * $quantity,
                'date' => $request->date,
                'eat_type' => $request->eat_type ?? 'other',
            ];

            Log::info('Данные для добавления в дневник', $nutritionData);

            // Передаем данные в CallorageController
            $callorageController = new CallorageController();
            return $callorageController->update($nutritionData);

        } catch (\Exception $e) {
            Log::error('Ошибка FatSecret API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->route('diary')->with('error', 'Ошибка при добавлении продукта: ' . $e->getMessage());
        }
    }

    /**
     * Форматирование результатов поиска
     */
    private function formatSearchResults($result): array
    {
        // Если это JSON строка, декодируем
        if (is_string($result)) {
            $result = json_decode($result, true);
        }

        // Если это уже массив, работаем с ним
        if (!is_array($result) || !isset($result['foods']) || !isset($result['foods']['food'])) {
            return [
                'foods' => [],
                'total_results' => 0
            ];
        }

        $foods = $result['foods']['food'];

        // Если это один элемент, преобразуем в массив
        if (isset($foods['food_id'])) {
            $foods = [$foods];
        }

        $formattedFoods = [];
        foreach ($foods as $food) {
            $formattedFoods[] = [
                'id' => $food['food_id'] ?? null,
                'name' => $food['food_name'] ?? null,
                'description' => $food['food_description'] ?? null,
                'type' => $food['food_type'] ?? null,
                'brand' => $food['brand_name'] ?? null
            ];
        }

        return [
            'foods' => $formattedFoods,
            'total_results' => $result['foods']['total_results'] ?? count($formattedFoods)
        ];
    }

    /**
     * Форматирование детальной информации о продукте
     */
    private function formatFoodDetails($result): array
    {
        // Если это JSON строка, декодируем
        if (is_string($result)) {
            $result = json_decode($result, true);
        }

        if (!is_array($result) || !isset($result['food'])) {
            return [];
        }

        $food = $result['food'];

        return [
            'id' => $food['food_id'] ?? null,
            'name' => $food['food_name'] ?? null,
            'type' => $food['food_type'] ?? null,
            'brand' => $food['brand_name'] ?? null,
            'servings' => $this->formatServings($food['servings'] ?? null)
        ];
    }

    /**
     * Форматирование информации о порциях
     */
    private function formatServings($servings): array
    {
        if (!$servings || !isset($servings['serving'])) {
            return [];
        }

        $servingsArray = $servings['serving'];

        // Если это один элемент, преобразуем в массив
        if (isset($servingsArray['serving_id'])) {
            $servingsArray = [$servingsArray];
        }

        $formattedServings = [];
        foreach ($servingsArray as $serving) {
            $formattedServings[] = [
                'description' => $serving['serving_description'] ?? null,
                'calories' => floatval($serving['calories'] ?? 0),
                'protein' => floatval($serving['protein'] ?? 0),
                'fat' => floatval($serving['fat'] ?? 0),
                'carbohydrates' => floatval($serving['carbohydrate'] ?? 0),
                'measurement' => $serving['measurement_description'] ?? null
            ];
        }

        return $formattedServings;
    }
}
