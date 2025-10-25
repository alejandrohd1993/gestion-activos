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
        Schema::create('expense_records', function (Blueprint $table) {
            $table->id();

            // Nueva relación con expense_batches
            $table->foreignId('expense_batch_id')
                ->constrained('expense_batches')
                ->cascadeOnDelete();

            // Relación con el tipo de gasto
            $table->foreignId('expense_id')
                ->constrained('expenses')
                ->cascadeOnDelete();

            // Datos del registro
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_records');
    }
};
