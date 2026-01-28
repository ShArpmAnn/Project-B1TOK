@extends('layout')

@section('title', 'Добавить продукт')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-search"></i> Поиск продукта</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('diary_new') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="eat_type" class="form-label">Тип приёма пищи *</label>
                            <select class="form-select" id="eat_type" name="eat_type" required>
                                <option value="" {{ old('eat_type', request('eat_type')) == '' ? 'selected' : '' }}>Выберите тип</option>
                                <option value="breakfast" {{ old('eat_type', request('eat_type')) == 'breakfast' ? 'selected' : '' }}>Завтрак</option>
                                <option value="lunch" {{ old('eat_type', request('eat_type')) == 'lunch' ? 'selected' : '' }}>Обед</option>
                                <option value="dinner" {{ old('eat_type', request('eat_type')) == 'dinner' ? 'selected' : '' }}>Ужин</option>
                                <option value="snack" {{ old('eat_type', request('eat_type')) == 'snack' ? 'selected' : '' }}>Перекус</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Дата *</label>
                            <input type="date" class="form-control" id="date" name="date"
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="query" class="form-label">Название продукта *</label>
                            <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                                <input type="text" class="form-control" id="query" name="query"
                                       value="{{ old('query') }}"
                                       placeholder="Например: яблоко, куриная грудка, овсянка..." required>
                            </div>
                            <div class="form-text">Поиск по базе FatSecret</div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Количество</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       value="{{ old('quantity', 1) }}" step="0.1" min="0.1">
                                <span class="input-group-text">порция(и)</span>
                            </div>
                            <div class="form-text">Укажите количество порций (по умолчанию 1)</div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Как это работает?</h6>
                            <p class="small mb-0">
                                Система найдёт продукт в базе FatSecret API, получит точные данные о КБЖУ
                                и добавит в ваш дневник питания.
                            </p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-search"></i> Найти и добавить
                            </button>
                            <a href="{{ route('diary') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Назад к дневнику
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Устанавливаем сегодняшнюю дату по умолчанию
        document.getElementById('date').value = new Date().toISOString().split('T')[0];
    </script>
@endpush
