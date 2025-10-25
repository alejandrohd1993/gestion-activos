<?php

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->foreignId('user_id')->comment('Operador asignado')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default(MaintenanceStatus::PENDIENTE->value);
            $table->string('type');
            $table->date('date');
            
            // Columnas para la relación polimórfica
            $table->morphs('maintainable'); // Esto crea maintainable_id y maintainable_type

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
