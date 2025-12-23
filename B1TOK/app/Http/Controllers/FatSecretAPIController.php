<?php

namespace App\Http\Controllers;

use Braunson\FatSecret\Facade as FatSecret;
use App\Http\Controllers\CallorageController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FatSecretAPIController extends Controller
{
    /**
     * Получение информации о продукте и добавление в дневник
     */
    public function AddToDiary(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'date' => 'required|date',
            'max_results' => 'sometimes|integer|min:1|max:50',
            'serving_index' => 'sometimes|integer|min:0', // индекс порции
            'quantity' => 'sometimes|numeric|min:0.1' // количество порций
        ]);

        try {
            // Ищем продукты
            $searchResult = FatSecret::searchIngredients(
                $request->input('query'),
                0,
                10
            );

            $food = $this->formatSearchResults($searchResult);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            // Получаем информацию о продукте
            $result = FatSecret::getIngredient($food['id']);
            $foodData = $this->formatFoodDetails($result);

            if (empty($foodData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            // Получаем выбранную порцию
            $servingIndex = $request->input('serving_index', 0);
            $quantity = $request->input('quantity', 1);

            $selectedServing = $foodData['servings'][$servingIndex] ?? $foodData['servings'][0];

            // Рассчитываем итоговые значения КБЖУ
            $nutritionData = [
                'food_id' => $foodData['id'],
                'food_name' => $foodData['name'],
                'calories' => $selectedServing['calories'] * $quantity,
                'proteins' => $selectedServing['protein'] * $quantity,
                'fats' => $selectedServing['fat'] * $quantity,
                'carbohydrates' => $selectedServing['carbohydrates'] * $quantity,
                'serving_description' => $selectedServing['description'],
                'quantity' => $quantity,
                'date' => $request->date,
                'eat_type' => $request->eat_type,
            ];

            // Передаем данные в CallorageController
            $callorageController = new CallorageController();
            $result = $callorageController->update($nutritionData);

            return response()->json([
                'success' => true,
                'data' => [
                    'food' => $foodData,
                    'added_to_diary' => $result,
                    'nutrition_data' => $nutritionData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Форматирование результатов поиска
     */
    private function formatSearchResults($result): array
    {
        if (!isset($result->foods) || !isset($result->foods->food)) {
            return [
                'foods' => [],
                'total_results' => 0
            ];
        }

        $foods = is_array($result->foods->food) ? $result->foods->food : [$result->foods->food];

        return [
            'foods' => array_map(function($food) {
                return [
                    'id' => $food->food_id,
                    'name' => $food->food_name,
                    'description' => $food->food_description ?? null,
                    'type' => $food->food_type ?? null,
                    'brand' => $food->brand_name ?? null
                ];
            }, $foods),
            'total_results' => $result->foods->total_results ?? count($foods)
        ];
    }

    /**
     * Форматирование детальной информации о продукте
     */
    private function formatFoodDetails($result): array
    {
        if (!isset($result->food)) {
            return [];
        }

        $food = $result->food;

        return [
            'id' => $food->food_id,
            'name' => $food->food_name,
            'type' => $food->food_type ?? null,
            'brand' => $food->brand_name ?? null,
            'servings' => $this->formatServings($food->servings ?? null)
        ];
    }

    /**
     * Форматирование информации о порциях
     */
    private function formatServings($servings): array
    {
        if (!$servings) {
            return [];
        }

        $servingsArray = is_array($servings->serving) ? $servings->serving : [$servings->serving];

        return array_map(function($serving) {
            return [
                'description' => $serving->serving_description ?? null,
                'calories' => $serving->calories ?? 0,
                'protein' => $serving->protein ?? 0,
                'fat' => $serving->fat ?? 0,
                'carbohydrates' => $serving->carbohydrate ?? 0,
                'measurement' => $serving->measurement_description ?? null
            ];
        }, $servingsArray);
    }
}
