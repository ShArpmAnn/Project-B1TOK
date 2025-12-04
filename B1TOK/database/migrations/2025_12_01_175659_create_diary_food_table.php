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
        Schema::create('diary_food', function (Blueprint $table) {
            $table->id();
            $table->foreignId('callorages_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->json('food')->nullable();
            $table->string('eat_type');
            $table->timestamps();

            $table->index('callorages_id');
            $table->index(['callorages_id', 'eat_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_food');
    }
};
