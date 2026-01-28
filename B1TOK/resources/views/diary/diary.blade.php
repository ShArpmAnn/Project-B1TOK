@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layout')

@section('title', 'Дневник питания')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1><i class="bi bi-journal-text text-success"></i> Дневник питания</h1>
            <p class="text-muted">Отслеживайте ваше питание за сегодня</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('diary_new') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Добавить продукт
            </a>
        </div>
    </div>

    @php
        $today = now()->format('Y-m-d');
        $callorage = App\Models\Callorage::forUser(Auth::id())->date($today)->first();
        $weight = App\Models\Weight::forUser(Auth::id())->current()->first();
    @endphp

        <!-- Статистика за день -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted">Норма</h6>
                    <h4 class="text-success">{{ $weight->callorage ?? 0 }} ккал</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted">Съедено</h6>
                    <h4 class="{{ ($callorage && $callorage->now_callorage > ($weight->callorage ?? 0)) ? 'text-danger' : 'text-success' }}">
                        {{ $callorage->now_callorage ?? 0 }} ккал
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted">Осталось</h6>
                    <h4 class="text-success">{{ $callorage->to_do_callorage ?? ($weight->callorage ?? 0) }} ккал</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted">Дата</h6>
                    <h4>{{ now()->format('d.m.Y') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Прогресс бар калорий -->
    @if($weight && $callorage)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Прогресс по калориям</h5>
                @php
                    $percent = min(100, ($callorage->now_callorage / $weight->callorage) * 100);
                @endphp
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success"
                         role="progressbar"
                         style="width: {{ $percent }}%"
                         aria-valuenow="{{ $percent }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        {{ round($percent) }}%
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small>0 ккал</small>
                    <small>{{ $callorage->now_callorage }} / {{ $weight->callorage }} ккал</small>
                    <small>{{ $weight->callorage }} ккал</small>
                </div>
            </div>
        </div>
    @endif

    <!-- Приёмы пищи -->
    <div class="row">
        @foreach(['breakfast' => 'Завтрак', 'lunch' => 'Обед', 'dinner' => 'Ужин', 'snack' => 'Перекус'] as $type => $name)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-{{ $type == 'breakfast' ? 'sun' : ($type == 'lunch' ? 'cloud-sun' : ($type == 'dinner' ? 'moon' : 'cup')) }}"></i>
                            {{ $name }}
                        </h5>
                        <span class="badge bg-success">
                    @if($callorage)
                                @php
                                    $diaryFood = App\Models\DiaryFood::forDiary($callorage->id, $type)->first();
                                    $totalCalories = 0;

                                    if($diaryFood && $diaryFood->food) {
                                        // Исправлено: проверяем, является ли food массивом
                                        $foodArray = $diaryFood->food;

                                        if (is_array($foodArray)) {
                                            foreach($foodArray as $food) {
                                                if (isset($food['calories'])) {
                                                    $totalCalories += $food['calories'];
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                {{ $totalCalories }} ккал
                            @else
                                0 ккал
                            @endif
                </span>
                    </div>
                    <div class="card-body">
                        @if($callorage)
                            @php
                                $diaryFood = App\Models\DiaryFood::forDiary($callorage->id, $type)->first();
                            @endphp

                            @if($diaryFood && $diaryFood->food)
                                @php
                                    $foodArray = $diaryFood->food;
                                    // Преобразуем в массив, если это не массив
                                    if (!is_array($foodArray)) {
                                        // Попробуем декодировать, если это JSON строка
                                        if (is_string($foodArray)) {
                                            $foodArray = json_decode($foodArray, true);
                                        } else {
                                            // Если это скалярное значение, создаем пустой массив
                                            $foodArray = [];
                                        }
                                    }
                                @endphp

                                @if(is_array($foodArray) && count($foodArray) > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($foodArray as $foodName => $nutrition)
                                            @if(is_array($nutrition) && isset($nutrition['calories']))
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $foodName }}</strong>
                                                        <div class="small text-muted">
                                                            {{ $nutrition['calories'] }} ккал •
                                                            Б:{{ $nutrition['proteins'] ?? 0 }}г •
                                                            Ж:{{ $nutrition['fats'] ?? 0 }}г •
                                                            У:{{ $nutrition['carbohydrates'] ?? 0 }}г
                                                        </div>
                                                    </div>
                                                    <form action="{{ route('diary_delete') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="date" value="{{ $today }}">
                                                        <input type="hidden" name="eat_type" value="{{ $type }}">
                                                        <input type="hidden" name="name" value="{{ $foodName }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-egg-fried" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Продукты не добавлены</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-egg-fried" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Продукты не добавлены</p>
                                </div>
                            @endif
                        @endif

                        <div class="text-center mt-3">
                            <a href="{{ route('diary_new') }}?eat_type={{ $type }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-plus"></i> Добавить продукт
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Общая статистика по БЖУ -->
    @if($callorage)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Итоги за день</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <h3 class="text-info">{{ $callorage->proteins ?? 0 }}г</h3>
                            <p class="text-muted mb-0">Белки</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                            <h3 class="text-warning">{{ $callorage->fats ?? 0 }}г</h3>
                            <p class="text-muted mb-0">Жиры</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <h3 class="text-success">{{ $callorage->carbohydrates ?? 0 }}г</h3>
                            <p class="text-muted mb-0">Углеводы</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <h3 class="text-primary">{{ $callorage->now_callorage ?? 0 }} ккал</h3>
                            <p class="text-muted mb-0">Всего калорий</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
