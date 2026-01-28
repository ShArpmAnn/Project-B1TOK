@extends('layout')

@section('title', 'Создать цель')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-flag"></i> Создание новой цели</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('create_weight') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Пол *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Выберите пол</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="old" class="form-label">Возраст *</label>
                                <input type="number" class="form-control" id="old" name="old"
                                       value="{{ old('old') }}" min="1" max="120" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="height" class="form-label">Рост (см) *</label>
                                <input type="number" class="form-control" id="height" name="height"
                                       value="{{ old('height') }}" min="50" max="250" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_weight" class="form-label">Текущий вес (кг) *</label>
                                <input type="number" class="form-control" id="start_weight" name="start_weight"
                                       value="{{ old('start_weight') }}" step="0.1" min="1" max="300" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="end_weight" class="form-label">Целевой вес (кг) *</label>
                            <input type="number" class="form-control" id="end_weight" name="end_weight"
                                   value="{{ old('end_weight') }}" step="0.1" min="1" max="300" required>
                        </div>

                        <div class="mb-3">
                            <label for="activity" class="form-label">Уровень активности *</label>
                            <select class="form-select" id="activity" name="activity" required>
                                <option value="">Выберите активность</option>
                                <option value="min" {{ old('activity') == 'min' ? 'selected' : '' }}>
                                    Минимальная (сидячая работа)
                                </option>
                                <option value="light" {{ old('activity') == 'light' ? 'selected' : '' }}>
                                    Лёгкая (1-3 тренировки в неделю)
                                </option>
                                <option value="medium" {{ old('activity') == 'medium' ? 'selected' : '' }}>
                                    Средняя (3-5 тренировок в неделю)
                                </option>
                                <option value="big" {{ old('activity') == 'big' ? 'selected' : '' }}>
                                    Высокая (6-7 тренировок в неделю)
                                </option>
                                <option value="very_big" {{ old('activity') == 'very_big' ? 'selected' : '' }}>
                                    Очень высокая (тяжёлая физическая работа)
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Цель *</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="choose" id="drop"
                                           value="drop" {{ old('choose') == 'drop' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="drop">Снижение веса</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="choose" id="increase"
                                           value="increase" {{ old('choose') == 'increase' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="increase">Набор веса</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Темп *</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="temp" id="slow"
                                           value="slow" {{ old('temp') == 'slow' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="slow">Медленный</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="temp" id="fast"
                                           value="fast" {{ old('temp') == 'fast' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fast">Быстрый</label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="bi bi-calculator"></i> Как рассчитывается норма калорий?</h6>
                            <p class="small mb-0">
                                Система использует формулу Миффлина-Сан Жеора для расчёта базового метаболизма (BMR),
                                затем умножает на коэффициент активности и корректирует в зависимости от цели и темпа.
                            </p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Создать цель
                            </button>
                            <a href="{{ route('personal_cabinet') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
