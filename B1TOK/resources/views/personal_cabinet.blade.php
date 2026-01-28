@extends('layout')

@section('title', 'Личный кабинет')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-graph-up"></i> Мои цели</h4>
                    @php
                        $weight = App\Models\Weight::forUser(Auth::id())->current()->first();
                        $goalReached = $weight && abs($weight->now_weight - $weight->end_weight) < 0.1;
                    @endphp
                    @if($goalReached)
                        <span class="badge bg-warning">
                    <i class="bi bi-trophy"></i> Цель достигнута!
                </span>
                    @endif
                </div>
                <div class="card-body">
                    @php
                        $weight = App\Models\Weight::forUser(Auth::id())->current()->first();
                        $archivedWeights = App\Models\Weight::forUser(Auth::id())->where('used_now', false)->get();
                    @endphp

                    @if($weight)
                        <!-- Баннер достижения цели -->
                        @if(abs($weight->now_weight - $weight->end_weight) < 0.1)
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-trophy-fill fs-3 me-3"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">Поздравляем! 🎉</h5>
                                        <p class="mb-0">Вы достигли своей цели! Вес: <strong>{{ $weight->now_weight }} кг</strong></p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-muted">Текущий вес</h5>
                                        <h2 class="text-success">{{ number_format($weight->now_weight, 1) }} кг</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-muted">Целевой вес</h5>
                                        <h2 class="text-success">{{ number_format($weight->end_weight, 1) }} кг</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-muted">Изменение</h5>
                                        <h2 class="{{ ($weight->now_weight - $weight->start_weight) < 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($weight->now_weight - $weight->start_weight, 1) }} кг
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Прогресс к цели</h5>
                            @php
                                $totalChange = $weight->end_weight - $weight->start_weight;
                                $currentChange = $weight->now_weight - $weight->start_weight;

                                if (abs($totalChange) < 0.1) {
                                    $progress = 100;
                                } else {
                                    $progress = ($currentChange / $totalChange) * 100;
                                }

                                $progress = min(100, max(0, $progress));
                                $isGoalReached = abs($weight->now_weight - $weight->end_weight) < 0.1;
                            @endphp

                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success progress-bar-striped {{ $isGoalReached ? 'progress-bar-animated' : '' }}"
                                     role="progressbar"
                                     style="width: {{ $progress }}%">
                                    {{ round($progress) }}%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small>Начало: {{ number_format($weight->start_weight, 1) }} кг</small>
                                <small>Текущий: {{ number_format($weight->now_weight, 1) }} кг</small>
                                <small>Цель: {{ number_format($weight->end_weight, 1) }} кг</small>
                            </div>
                        </div>

                        <div class="card mb-4 {{ $isGoalReached ? 'bg-warning text-dark' : 'bg-info text-white' }}">
                            <div class="card-body">
                                <h5>
                                    <i class="bi {{ $isGoalReached ? 'bi-trophy' : 'bi-lightning-charge' }}"></i>
                                    {{ $isGoalReached ? 'Цель достигнута!' : 'Дневная норма калорий' }}
                                </h5>
                                <h2>{{ $weight->callorage }} ккал</h2>
                                <p class="mb-0">
                                    @if($isGoalReached)
                                        Поддерживайте текущий вес с этой нормой калорий
                                    @else
                                        Для достижения вашей цели
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('update_now_weight') }}" class="btn btn-outline-success me-2">
                                <i class="bi bi-pencil"></i> Обновить вес
                            </a>
                            <a href="{{ route('update_weight') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-gear"></i> Изменить цель
                            </a>

                            @if($isGoalReached)
                                <a href="{{ route('create_weight') }}" class="btn btn-success">
                                    <i class="bi bi-flag"></i> Создать новую цель
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-flag text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">У вас нет активной цели</h4>
                            <p class="text-muted">Создайте свою первую цель для отслеживания прогресса</p>
                            <a href="{{ route('create_weight') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-plus-circle"></i> Создать цель
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- История прошлых целей -->
            @if($archivedWeights->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> История целей</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Начальный вес</th>
                                    <th>Конечный вес</th>
                                    <th>Изменение</th>
                                    <th>Дата создания</th>
                                    <th>Статус</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($archivedWeights as $archived)
                                    @php
                                        $weightChange = $archived->end_weight - $archived->start_weight;
                                        $isCompleted = abs($archived->now_weight - $archived->end_weight) < 0.1;
                                    @endphp
                                    <tr>
                                        <td>{{ number_format($archived->start_weight, 1) }} кг</td>
                                        <td>{{ number_format($archived->end_weight, 1) }} кг</td>
                                        <td>
                                    <span class="{{ $weightChange < 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($weightChange, 1) }} кг
                                    </span>
                                        </td>
                                        <td>{{ $archived->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            @if($isCompleted)
                                                <span class="badge bg-success">Достигнута</span>
                                            @else
                                                <span class="badge bg-secondary">Не завершена</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Статистика -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6>Всего целей</h6>
                                        <h4 class="text-success">{{ $archivedWeights->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6>Достигнуто</h6>
                                        <h4 class="text-success">
                                            {{ $archivedWeights->where(fn($w) => abs($w->now_weight - $w->end_weight) < 0.1)->count() }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        @php
                                            $totalLoss = 0;
                                            foreach ($archivedWeights as $archived) {
                                                $change = $archived->end_weight - $archived->start_weight;
                                                if ($change < 0) {
                                                    $totalLoss += abs($change);
                                                }
                                            }
                                        @endphp
                                        <h6>Всего снижено</h6>
                                        <h4 class="text-success">{{ number_format($totalLoss, 1) }} кг</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-speedometer2"></i> Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('diary') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-journal-plus text-success"></i> Добавить приём пищи
                        </a>
                        <a href="{{ route('diary_new') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-search text-success"></i> Найти продукт
                        </a>
                        <a href="{{ route('new_recipes') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-plus-circle text-success"></i> Создать рецепт
                        </a>
                        <a href="{{ route('save_recipes') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-book text-success"></i> Мои рецепты
                        </a>
                        @if($weight && !$isGoalReached)
                            <a href="{{ route('update_now_weight') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-pencil text-success"></i> Обновить вес
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($weight)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Информация о цели</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-calendar text-success me-2"></i>
                                <strong>Создана:</strong>
                                {{ $weight->created_at->format('d.m.Y') }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-arrow-left-right text-success me-2"></i>
                                <strong>Цель:</strong>
                                {{ $weight->start_weight > $weight->end_weight ? 'Снижение веса' : 'Набор веса' }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-clock text-success me-2"></i>
                                <strong>Длительность:</strong>
                                @php
                                    $days = $weight->created_at->diffInDays(now());
                                    echo number_format($days, 1) . ' дней';
                                @endphp
                            </li>
                            @if($isGoalReached)
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <strong>Достигнута:</strong>
                                    {{ $weight->updated_at->format('d.m.Y') }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Советы</h5>
                </div>
                <div class="card-body">
                    @if($weight && $isGoalReached)
                        <div class="alert alert-warning mb-3">
                            <h6><i class="bi bi-trophy"></i> Цель достигнута!</h6>
                            <p class="small mb-0">
                                Создайте новую цель для дальнейшего прогресса или
                                поддерживайте текущий вес.
                            </p>
                        </div>
                    @endif

                    <div class="alert alert-info mb-3">
                        <h6><i class="bi bi-graph-up"></i> Отслеживайте прогресс</h6>
                        <p class="small mb-0">
                            Все ваши цели сохраняются в истории. Вы можете видеть,
                            сколько целей уже достигли.
                        </p>
                    </div>

                    <div class="alert alert-success mb-0">
                        <h6><i class="bi bi-heart"></i> Не забывайте про дневник</h6>
                        <p class="small mb-0">
                            Регулярное ведение дневника питания помогает
                            точнее достигать целей.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            from { background-position: 1rem 0; }
            to { background-position: 0 0; }
        }

        .goal-completed {
            background: linear-gradient(45deg, rgba(255, 215, 0, 0.1), rgba(255, 237, 78, 0.2));
            border-left: 4px solid #ffc107;
        }
    </style>
@endpush

@push('scripts')
    @if($weight ?? false)
        <script>
            // Анимация для достигнутой цели
            @if($isGoalReached ?? false)
            document.addEventListener('DOMContentLoaded', function() {
                // Анимация для трофея в заголовке
                const trophyBadge = document.querySelector('.badge.bg-warning');
                if (trophyBadge) {
                    setInterval(() => {
                        trophyBadge.classList.toggle('shadow');
                    }, 1000);
                }

                // Анимация для баннера достижения
                const achievementAlert = document.querySelector('.alert-warning');
                if (achievementAlert) {
                    achievementAlert.classList.add('goal-completed');
                }
            });
            @endif

            // Подсветка строк в таблице для достигнутых целей
            document.addEventListener('DOMContentLoaded', function() {
                const tableRows = document.querySelectorAll('table tbody tr');
                tableRows.forEach(row => {
                    const statusBadge = row.querySelector('.badge.bg-success');
                    if (statusBadge && statusBadge.textContent.includes('Достигнута')) {
                        row.classList.add('table-success');
                    }
                });
            });
        </script>
    @endif
@endpush
