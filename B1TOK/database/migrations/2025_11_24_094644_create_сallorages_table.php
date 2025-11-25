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
        Schema::create('сallorages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedInteger('to_do_callorage')->default(0);
            $table->unsignedInteger('now_callorage')->default(0);
            $table->unsignedInteger('proteins')->default(0);
            $table->unsignedInteger('fats')->default(0);
            $table->unsignedInteger('carbohydrates')->default(0);
            $table->date('date');

            // Индексы для оптимизации запросов
            $table->index('user_id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('сallorages');
    }
};
