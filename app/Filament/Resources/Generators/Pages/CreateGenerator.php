<?php

namespace App\Filament\Resources\Generators\Pages;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Enums\UnitType;
use App\Filament\Resources\Generators\GeneratorResource;
use App\Models\Component;
use App\Models\Generator;
use App\Models\Maintenance;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGenerator extends CreateRecord
{
    protected static string $resource = GeneratorResource::class;

    protected function afterCreate(): void
    {
        $components = $this->form->getState()['components'] ?? [];
        $syncData = [];

        foreach ($components as $componentData) {
            // Prepara los datos para la tabla pivote
            $syncData[$componentData['component_id']] = [
                'last_maintenance_date' => $componentData['last_maintenance_date'] ?? null,
                'last_maintenance_meter' => $componentData['last_maintenance_meter'] ?? null,
            ];
        }

        // Sincroniza los datos con la relación
        $this->record->components()->sync($syncData);

        /** @var Generator $generator */
        $generator = $this->getRecord();

        // Recorremos los componentes que se asociaron al generador
        foreach ($generator->components as $component) {

            $date = $component->unit->type === UnitType::USO_ACUMULADO
                ? '2025-01-01'
                : $component->pivot->last_maintenance_date;

            // Crear el mantenimiento inicial para este componente
            $maintenance = Maintenance::create([
                'name' => "Mantenimiento inicial - {$component->name} {$generator->name}",
                'maintainable_type' => Generator::class,
                'maintainable_id' => $generator->id,
                'date' => $date,
                'type' => MaintenanceType::PREVENTIVO,
                'status' => MaintenanceStatus::COMPLETADO,
                'user_id' => 1, // O un usuario por defecto
            ]);

            // Asociar solo este componente al mantenimiento
            $maintenance->components()->attach($component->id);

            // Si el componente es por "Uso Acumulado", crear el registro de uso
            if ($component->unit->type === UnitType::USO_ACUMULADO) {
                $maintenance->usages()->create([
                    'date' => '2025-01-01',
                    'equipment_type' => Generator::class,
                    'equipment_id' => $generator->id,
                    'initial_meter' => $component->pivot->last_maintenance_meter,
                    'final_meter' => $component->pivot->last_maintenance_meter,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
