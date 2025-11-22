<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->decimal('start_weight', 8, 2);
            $table->decimal('end_weight', 8, 2);
            $table->decimal('now_weight', 8, 2);
            $table->decimal('to_do_weight', 8, 2);
            $table->boolean('used_now')->default(false);
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index('user_id');
            $table->index(['user_id', 'used_now']); // Для быстрого поиска текущей записи
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weights');
    }
};
