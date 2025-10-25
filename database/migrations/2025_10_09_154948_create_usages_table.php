<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->date('date');

            // Relación polimórfica para el equipo (Activo o Generador)
            $table->morphs('equipment'); // Crea equipment_id y equipment_type

            // Relación polimórfica para el evento (Servicio o Mantenimiento)
            $table->morphs('usable'); // Crea usable_id y usable_type

            $table->bigInteger('initial_meter')->comment('Odómetro/Horómetro inicial en km/segundos');
            $table->bigInteger('final_meter')->comment('Odómetro/Horómetro final en km/segundos');
            // $table->bigInteger('total_usage')->comment('Uso total en km/segundos');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usages');
    }
};
