@extends('layout')

@section('title', 'Изменить цель')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-gear"></i> Изменение цели</h4>
                </div>
                <div class="card-body">
                    @php
                        $weight = App\Models\Weight::forUser(Auth::id())->current()->first();
                    @endphp

                    @if($weight)
                        <div class="alert alert-warning mb-4">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
                                <div>
                                    <h6>Внимание!</h6>
                                    <p class="mb-0">
                                        Изменение цели приведёт к пересчёту дневной нормы калорий.
                                        Все существующие записи в дневнике останутся без изменений.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('update_weight') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Пол *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Выберите пол</option>
                                        <option value="male" {{ old('gender', $weight->gender ?? 'male') == 'male' ? 'selected' : '' }}>Мужской</option>
                                        <option value="female" {{ old('gender', $weight->gender ?? 'female') == 'female' ? 'selected' : '' }}>Женский</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="old" class="form-label">Возраст *</label>
                                    <input type="number" class="form-control" id="old" name="old"
                                           value="{{ old('old', $weight->old ?? 30) }}" min="1" max="120" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="height" class="form-label">Рост (см) *</label>
                                    <input type="number" class="form-control" id="height" name="height"
                                           value="{{ old('height', $weight->height ?? 170) }}" min="50" max="250" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="start_weight" class="form-label">Начальный вес (кг) *</label>
                                    <input type="number" class="form-control" id="start_weight" name="start_weight"
                                           value="{{ old('start_weight', $weight->start_weight) }}"
                                           step="0.1" min="1" max="300" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="now_weight" class="form-label">Текущий вес (кг) *</label>
                                    <input type="number" class="form-control" id="now_weight" name="now_weight"
                                           value="{{ old('now_weight', $weight->now_weight) }}"
                                           step="0.1" min="1" max="300" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_weight" class="form-label">Целевой вес (кг) *</label>
                                    <input type="number" class="form-control" id="end_weight" name="end_weight"
                                           value="{{ old('end_weight', $weight->end_weight) }}"
                                           step="0.1" min="1" max="300" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="activity" class="form-label">Уровень активности *</label>
                                <select class="form-select" id="activity" name="activity" required>
                                    <option value="">Выберите активность</option>
                                    <option value="min" {{ old('activity', $weight->activity ?? 'medium') == 'min' ? 'selected' : '' }}>
                                        Минимальная (сидячая работа)
                                    </option>
                                    <option value="light" {{ old('activity', $weight->activity ?? 'medium') == 'light' ? 'selected' : '' }}>
                                        Лёгкая (1-3 тренировки в неделю)
                                    </option>
                                    <option value="medium" {{ old('activity', $weight->activity ?? 'medium') == 'medium' ? 'selected' : '' }}>
                                        Средняя (3-5 тренировок в неделю)
                                    </option>
                                    <option value="big" {{ old('activity', $weight->activity ?? 'medium') == 'big' ? 'selected' : '' }}>
                                        Высокая (6-7 тренировок в неделю)
                                    </option>
                                    <option value="very_big" {{ old('activity', $weight->activity ?? 'medium') == 'very_big' ? 'selected' : '' }}>
                                        Очень высокая (тяжёлая физическая работа)
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Цель *</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="choose" id="drop"
                                               value="drop" {{ old('choose', $weight->choose ?? 'drop') == 'drop' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="drop">Снижение веса</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="choose" id="increase"
                                               value="increase" {{ old('choose', $weight->choose ?? 'drop') == 'increase' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="increase">Набор веса</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Темп *</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="temp" id="slow"
                                               value="slow" {{ old('temp', $weight->temp ?? 'slow') == 'slow' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="slow">Медленный</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="temp" id="fast"
                                               value="fast" {{ old('temp', $weight->temp ?? 'slow') == 'fast' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fast">Быстрый</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Предварительный расчет калорий -->
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <h5><i class="bi bi-calculator"></i> Предварительный расчёт</h5>
                                    <div id="caloriePreview">
                                        <p class="mb-0">Новая дневная норма калорий: <strong><span id="calculatedCalories">0</span> ккал</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Сохранить изменения
                                </button>
                                <a href="{{ route('personal_cabinet') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Отмена
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-flag text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">У вас нет активной цели</h4>
                            <p class="text-muted">Сначала создайте цель для изменения</p>
                            <a href="{{ route('create_weight') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Создать цель
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateBMR() {
            const gender = document.getElementById('gender').value;
            const startWeight = parseFloat(document.getElementById('start_weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const old = parseInt(document.getElementById('old').value);
            const activity = document.getElementById('activity').value;
            const choose = document.querySelector('input[name="choose"]:checked')?.value;
            const temp = document.querySelector('input[name="temp"]:checked')?.value;

            if (!gender || !startWeight || !height || !old || !activity || !choose || !temp) {
                return 0;
            }

            let BMR = 0;

            // Формула Миффлина-Сан Жеора
            if (gender === 'male') {
                BMR = (10 * startWeight) + (6.25 * height) - (5 * old) + 5;
            } else {
                BMR = (10 * startWeight) + (6.25 * height) - (5 * old) - 161;
            }

            // Коэффициент активности
            const activityMultipliers = {
                'min': 1.2,
                'light': 1.375,
                'medium': 1.55,
                'big': 1.725,
                'very_big': 1.9
            };

            BMR *= activityMultipliers[activity] || 1;

            // Корректировка по цели и темпу
            if (choose === 'drop') {
                if (temp === 'slow') {
                    BMR -= 300;
                } else {
                    BMR -= 500;
                }
            } else if (choose === 'increase') {
                if (temp === 'slow') {
                    BMR += 300;
                } else {
                    BMR += 500;
                }
            }

            return Math.round(BMR);
        }

        function updateCaloriePreview() {
            const calories = calculateBMR();
            document.getElementById('calculatedCalories').textContent = calories;
        }

        // Слушатели изменений
        const inputs = ['gender', 'start_weight', 'height', 'old', 'activity', 'choose', 'temp'];
        inputs.forEach(input => {
            if (input === 'choose' || input === 'temp') {
                document.querySelectorAll(`input[name="${input}"]`).forEach(radio => {
                    radio.addEventListener('change', updateCaloriePreview);
                });
            } else {
                const element = document.getElementById(input);
                if (element) {
                    element.addEventListener('input', updateCaloriePreview);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', updateCaloriePreview);
    </script>
@endpush
