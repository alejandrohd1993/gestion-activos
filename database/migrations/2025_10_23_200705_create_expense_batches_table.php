<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_batches', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('expensable_type');
            $table->unsignedBigInteger('expensable_id');
            $table->string('scope');
            $table->string('equipment_type')->nullable();
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_batches');
    }
};
