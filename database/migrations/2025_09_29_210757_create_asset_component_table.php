<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_component', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('component_id')->constrained()->cascadeOnDelete();
            
            // Campos extra en la tabla pivote
            $table->bigInteger('last_maintenance_meter')->nullable()->comment('Valor del medidor en el último mantenimiento (km o segundos)');
            $table->date('last_maintenance_date')->nullable()->comment('Fecha del último mantenimiento');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_component');
    }
};
