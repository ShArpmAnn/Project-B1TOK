@extends('layout')

@section('title', 'Новый рецепт')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Создание нового рецепта</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('new_recipes') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label">Название рецепта *</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="{{ old('title') }}"
                                   placeholder="Например: Куриная грудка с овощами" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="callorage" class="form-label">Калории (на порцию) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="callorage" name="callorage"
                                           value="{{ old('callorage') }}" min="0" required>
                                    <span class="input-group-text">ккал</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="proteins" class="form-label">Белки *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="proteins" name="proteins"
                                           value="{{ old('proteins', 0) }}" step="0.1" min="0" required>
                                    <span class="input-group-text">г</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="fats" class="form-label">Жиры *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="fats" name="fats"
                                           value="{{ old('fats', 0) }}" step="0.1" min="0" required>
                                    <span class="input-group-text">г</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="carbohydrates" class="form-label">Углеводы *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="carbohydrates" name="carbohydrates"
                                           value="{{ old('carbohydrates', 0) }}" step="0.1" min="0" required>
                                    <span class="input-group-text">г</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="food" class="form-label">Ингредиенты *</label>
                            <textarea class="form-control" id="food" name="food" rows="5"
                                      placeholder="Введите каждый ингредиент с новой строки. Например:
- Куриная грудка 200г
- Брокколи 150г
- Морковь 100г
- Оливковое масло 1 ст. ложка" required>{{ old('food') }}</textarea>
                            <div class="form-text">
                                Каждый ингредиент с новой строки. Можно использовать маркировку или дефисы.
                                Система разделит их автоматически.
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <h6><i class="bi bi-lightbulb"></i> Советы по заполнению</h6>
                            <ul class="small mb-0">
                                <li>Указывайте пищевую ценность на одну порцию</li>
                                <li>Для точного расчёта используйте кухонные весы</li>
                                <li>Можно использовать данные с упаковки продуктов</li>
                                <li>Сохраняйте рецепты для быстрого добавления в дневник</li>
                            </ul>
                        </div>

                        <!-- Предварительный просмотр -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-eye"></i> Предварительный просмотр</h6>
                            </div>
                            <div class="card-body">
                                <div id="recipePreview">
                                    <p class="text-muted">Заполните форму для предварительного просмотра</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Сохранить рецепт
                            </button>
                            <a href="{{ route('save_recipes') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Назад к рецептам
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
        function updateRecipePreview() {
            const title = document.getElementById('title').value || 'Название рецепта';
            const callorage = document.getElementById('callorage').value || '0';
            const proteins = document.getElementById('proteins').value || '0';
            const fats = document.getElementById('fats').value || '0';
            const carbohydrates = document.getElementById('carbohydrates').value || '0';
            const foodText = document.getElementById('food').value;

            let previewHtml = `
            <h5>${title}</h5>
            <div class="row g-2 text-center mb-3">
                <div class="col-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded">
                        <div class="text-info fw-bold">${proteins}г</div>
                        <small class="text-muted">Белки</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                        <div class="text-warning fw-bold">${fats}г</div>
                        <small class="text-muted">Жиры</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-success bg-opacity-10 p-2 rounded">
                        <div class="text-success fw-bold">${carbohydrates}г</div>
                        <small class="text-muted">Углеводы</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <div class="text-primary fw-bold">${callorage}</div>
                        <small class="text-muted">Ккал</small>
                    </div>
                </div>
            </div>
        `;

            if (foodText.trim()) {
                const ingredients = foodText.split('\n')
                    .filter(line => line.trim())
                    .map(line => line.replace(/^[-\s•]*/, '').trim());

                if (ingredients.length > 0) {
                    previewHtml += '<h6>Ингредиенты:</h6><ul class="list-group list-group-flush">';
                    ingredients.forEach(ingredient => {
                        if (ingredient) {
                            previewHtml += `<li class="list-group-item px-0 py-1 small">${ingredient}</li>`;
                        }
                    });
                    previewHtml += '</ul>';
                }
            }

            document.getElementById('recipePreview').innerHTML = previewHtml;
        }

        // Слушатели изменений
        const inputs = ['title', 'callorage', 'proteins', 'fats', 'carbohydrates', 'food'];
        inputs.forEach(input => {
            document.getElementById(input).addEventListener('input', updateRecipePreview);
        });

        document.addEventListener('DOMContentLoaded', updateRecipePreview);
    </script>
@endpush

