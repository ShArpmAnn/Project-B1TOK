@extends('layout')

@section('title', 'Редактировать рецепт')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> Редактирование рецепта</h4>
                </div>
                <div class="card-body">
                    @php
                        $recipeId = request('id');
                        $recipe = $recipeId ? App\Models\SaveRecipe::forUser(Auth::id())->where('id', $recipeId)->first() : null;
                    @endphp

                    @if($recipe)
                        <form action="{{ route('update_recipes') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $recipe->id }}">

                            <div class="mb-4">
                                <label for="title" class="form-label">Название рецепта *</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title', $recipe->title) }}" required>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="callorage" class="form-label">Калории (на порцию) *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="callorage" name="callorage"
                                               value="{{ old('callorage', $recipe->callorage) }}" min="0" required>
                                        <span class="input-group-text">ккал</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="proteins" class="form-label">Белки *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="proteins" name="proteins"
                                               value="{{ old('proteins', $recipe->proteins) }}" step="0.1" min="0" required>
                                        <span class="input-group-text">г</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="fats" class="form-label">Жиры *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="fats" name="fats"
                                               value="{{ old('fats', $recipe->fats) }}" step="0.1" min="0" required>
                                        <span class="input-group-text">г</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="carbohydrates" class="form-label">Углеводы *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="carbohydrates" name="carbohydrates"
                                               value="{{ old('carbohydrates', $recipe->carbohydrates) }}" step="0.1" min="0" required>
                                        <span class="input-group-text">г</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="food" class="form-label">Ингредиенты *</label>
                                @php
                                    $ingredientsText = is_array($recipe->food) ? implode("\n", array_filter($recipe->food)) : '';
                                @endphp
                                <textarea class="form-control" id="food" name="food" rows="5" required>{{ old('food', $ingredientsText) }}</textarea>
                                <div class="form-text">
                                    Каждый ингредиент с новой строки. Можно использовать маркировку или дефисы.
                                </div>
                            </div>

                            <!-- Информация о создании -->
                            <div class="alert alert-secondary mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-clock-history fs-4 me-3"></i>
                                    <div>
                                        <h6>Информация о рецепте</h6>
                                        <p class="mb-0">
                                            Создан: {{ $recipe->created_at->format('d.m.Y H:i') }}<br>
                                            Последнее изменение: {{ $recipe->updated_at->format('d.m.Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Сохранить изменения
                                </button>
                                <a href="{{ route('save_recipes') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Назад к рецептам
                                </a>
                                <button type="button" class="btn btn-outline-danger ms-auto"
                                        onclick="if(confirm('Удалить этот рецепт?')) { window.location.href='{{ route('delete_recipes') }}?id={{ $recipe->id }}'; }">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Рецепт не найден</h4>
                            <p class="text-muted">Рецепт не существует или у вас нет к нему доступа</p>
                            <a href="{{ route('save_recipes') }}" class="btn btn-success">
                                <i class="bi bi-arrow-left"></i> Назад к рецептам
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if($recipe)
        <script>
            function formatIngredients() {
                const textarea = document.getElementById('food');
                const lines = textarea.value.split('\n');
                const formattedLines = lines.map(line => {
                    // Убираем начальные маркеры и лишние пробелы
                    return line.replace(/^[-\s•*]+/, '').trim();
                }).filter(line => line.length > 0);

                textarea.value = formattedLines.join('\n');
            }

            document.getElementById('food').addEventListener('blur', formatIngredients);
        </script>
    @endif
@endpush

