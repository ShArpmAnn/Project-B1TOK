@extends('layout')

@section('title', 'Обновить вес')

@section('content')
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Обновить текущий вес</h4>
                </div>
                <div class="card-body">
                    @php
                        $weight = App\Models\Weight::forUser(Auth::id())->current()->first();
                    @endphp

                    @if($weight)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <i class="bi bi-info-circle fs-4 me-3"></i>
                                <div>
                                    <h6>Текущая цель</h6>
                                    <p class="mb-0">
                                        Начальный вес: <strong>{{ $weight->start_weight }} кг</strong><br>
                                        Текущий вес: <strong>{{ $weight->now_weight }} кг</strong><br>
                                        Целевой вес: <strong>{{ $weight->end_weight }} кг</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('update_now_weight') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="now_weight" class="form-label">Новый текущий вес (кг) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="now_weight" name="now_weight"
                                           value="{{ old('now_weight', $weight->now_weight) }}"
                                           step="0.1" min="1" max="300" required>
                                    <span class="input-group-text">кг</span>
                                </div>
                                <div class="form-text">Укажите ваш текущий вес с точностью до 0.1 кг</div>
                            </div>

                            <!-- Предварительный расчет -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6>Что изменится:</h6>
                                    <div id="weightPreview">
                                        <p class="mb-1">Цель по снижению веса: <span id="goalChange">0</span> кг</p>
                                        <p class="mb-1">Осталось до цели: <span id="remainingWeight">0</span> кг</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Обновить вес
                                </button>
                                <a href="{{ route('personal_cabinet') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Назад в кабинет
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-flag text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">У вас нет активной цели</h4>
                            <p class="text-muted">Сначала создайте цель для отслеживания веса</p>
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
    @if($weight ?? false)
        <script>
            const startWeight = {{ $weight->start_weight }};
            const endWeight = {{ $weight->end_weight }};

            function updatePreview() {
                const nowWeight = parseFloat(document.getElementById('now_weight').value);

                if (nowWeight && !isNaN(nowWeight)) {
                    const goalChange = Math.abs(startWeight - nowWeight).toFixed(1);
                    const remainingWeight = Math.abs(endWeight - nowWeight).toFixed(1);

                    document.getElementById('goalChange').textContent = goalChange;
                    document.getElementById('remainingWeight').textContent = remainingWeight;

                    // Цвет для оставшегося веса
                    const remainingElement = document.getElementById('remainingWeight');
                    if (endWeight > nowWeight) {
                        remainingElement.className = 'text-success';
                    } else if (endWeight < nowWeight) {
                        remainingElement.className = 'text-warning';
                    } else {
                        remainingElement.className = 'text-danger';
                    }
                }
            }

            document.getElementById('now_weight').addEventListener('input', updatePreview);
            document.addEventListener('DOMContentLoaded', updatePreview);
        </script>
    @endif
@endpush
