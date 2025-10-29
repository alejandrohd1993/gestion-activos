<?php

namespace App\Services;

use App\Enums\UnitType;
use App\Models\Asset;
use App\Models\Component;
use App\Models\Generator;
use App\Models\Maintenance;
use App\Models\Usage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Service class to calculate the maintenance status of components for Assets and Generators.
 * This service relies solely on the 'maintenances' and 'usages' tables as the source of truth
 * for the last maintenance performed.
 */
class MaintenanceStatusService
{
    /**
     * Main entry point. Calculates the maintenance status for all components of a given equipment.
     *
     * @param Asset|Generator $equipment The equipment (Asset or Generator) to analyze.
     * @return Collection A collection of objects, each representing a component and its maintenance status.
     */
    public function getComponentsStatus(Model $equipment): Collection
    {
        // Eager load para evitar problemas de N+1 queries al acceder a component->unit.
        $equipment->load('components.unit');

        return $equipment->components->map(function (Component $component) use ($equipment) {
            // Estructura base de la respuesta para cada componente.
            $statusData = [
                'component_id' => $component->id,
                'component_name' => $component->name,
                'unit_type' => $component->unit->type->value, // Usamos ->value para obtener el string del Enum
                'status' => 'ok', // Estado por defecto
                'message' => 'No requiere acción.', // Mensaje por defecto
            ];

            $calculation = [];

            // Usamos el Enum UnitType para una comparación segura y legible.
            // Asumo que tu Enum se encuentra en App\Enums\UnitType
            if ($component->unit->type === UnitType::USO_ACUMULADO) {
                $calculation = $this->calculateUsageBasedStatus($equipment, $component);
            } elseif ($component->unit->type === UnitType::CALENDARIO) {
                $calculation = $this->calculateCalendarBasedStatus($equipment, $component);
            } else {
                // Manejar tipos de unidad desconocidos de forma segura.
                $calculation = [
                    'status' => 'unknown',
                    'message' => 'Tipo de unidad de mantenimiento no reconocido.',
                ];
            }

            // Fusionamos los datos base con el resultado del cálculo y lo convertimos a un objeto.
            return (object) array_merge($statusData, $calculation);
        });
    }

    /**
     * Calculates the status for a component whose maintenance is based on accumulated usage (horometer/odometer).
     *
     * @param Model $equipment The equipment instance.
     * @param Component $component The component to analyze.
     * @return array An array with the component's maintenance status details.
     */
    private function calculateUsageBasedStatus(Model $equipment, Component $component): array
    {
        $lastMaintenance = $this->findLastMaintenanceForComponent($equipment, $component);

        if (!$lastMaintenance) {
            return [
                'status' => 'unknown',
                'message' => 'No se encontró un mantenimiento previo para este componente.',
            ];
        }

        // 1. Obtener el valor del medidor en el último mantenimiento de este componente.
        // Buscamos el 'Usage' que está polimórficamente relacionado con el 'Maintenance'.
        $usageAtMaintenance = $lastMaintenance->usages()
            ->where('equipment_type', $equipment->getMorphClass())
            ->where('equipment_id', $equipment->getKey())
            ->first();

        if (!$usageAtMaintenance) {
            // Esto indica un problema de datos: un mantenimiento de 'uso_acumulado' debería tener un 'Usage' asociado.
            return [
                'status' => 'unknown',
                'message' => 'No se encontró registro de uso para el último mantenimiento.',
                'last_maintenance_date' => Carbon::parse($lastMaintenance->date)->toDateString(),
            ];
        }

        $lastMaintenanceMeter = (int) $usageAtMaintenance->final_meter;

        // 2. Obtener el valor actual del medidor del equipo.
        // Buscamos el último 'Usage' registrado para el equipo, sin importar el motivo.
        $latestUsage = Usage::where('equipment_type', $equipment->getMorphClass())
            ->where('equipment_id', $equipment->getKey())
            ->latest('date')
            ->latest('id') // Desempate por ID si hay varios en el mismo día
            ->first();

        // Si no hay ningún uso, usamos el valor 'current_meter' del equipo como fallback.
        $currentMeter = $latestUsage ? (int) $latestUsage->final_meter : (int) $equipment->current_meter;

        // 3. Realizar los cálculos.
        $duration = (int) $component->duration;
        if ($duration <= 0) {
            return [
                'status' => 'unknown',
                'message' => 'La duración del componente no está configurada.',
                'last_maintenance_meter' => $lastMaintenanceMeter,
                'current_meter' => $currentMeter,
            ];
        }

        $usageSinceMaintenance = max(0, $currentMeter - $lastMaintenanceMeter);
        $nextMaintenanceMeter = $lastMaintenanceMeter + $duration;
        $usageRemaining = $nextMaintenanceMeter - $currentMeter;

        $status = 'ok';
        $warningThreshold = $duration * 0.10; // Umbral de advertencia: 10% de la duración.
        if ($usageRemaining <= 0) {
            $status = 'expired';
        } elseif ($usageRemaining <= $warningThreshold) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'message' => "Próximo mantenimiento en {$usageRemaining} " . $component->unit->name . ".",
            'last_maintenance_meter' => $lastMaintenanceMeter,
            'current_meter' => $currentMeter,
            'next_maintenance_meter' => $nextMaintenanceMeter,
            'usage_remaining' => $usageRemaining,
        ];
    }

    /**
     * Calculates the status for a component whose maintenance is based on a calendar schedule.
     *
     * @param Model $equipment The equipment instance.
     * @param Component $component The component to analyze.
     * @return array An array with the component's maintenance status details.
     */
    private function calculateCalendarBasedStatus(Model $equipment, Component $component): array
    {
        $lastMaintenance = $this->findLastMaintenanceForComponent($equipment, $component);

        if (!$lastMaintenance) {
            return [
                'status' => 'unknown',
                'message' => 'No se encontró un mantenimiento previo para este componente.',
                'last_maintenance_date' => null,
                'next_maintenance_date' => null,
                'days_remaining' => null,
            ];
        }

        $lastMaintenanceDate = Carbon::parse($lastMaintenance->date);
        $durationInDays = (int) $component->duration;

        if ($durationInDays <= 0) {
            return [
                'status' => 'unknown',
                'message' => 'La duración del componente no está configurada.',
                'last_maintenance_date' => $lastMaintenanceDate->toDateString(),
            ];
        }

        $nextMaintenanceDate = $lastMaintenanceDate->copy()->addDays($durationInDays);
        // El segundo argumento `false` en diffInDays permite obtener valores negativos si la fecha ya pasó.
        $daysRemaining = Carbon::today()->diffInDays($nextMaintenanceDate, false);

        $status = 'ok';
        if ($daysRemaining < 0) {
            $status = 'expired';
        } elseif ($daysRemaining <= 7) { // Umbral de advertencia: 7 días o menos.
            $status = 'warning';
        }

        return [
            'status' => $status,
            'message' => "Próximo mantenimiento en {$daysRemaining} día(s).",
            'last_maintenance_date' => $lastMaintenanceDate->toDateString(),
            'next_maintenance_date' => $nextMaintenanceDate->toDateString(),
            'days_remaining' => $daysRemaining,
        ];
    }

    /**
     * Finds the last maintenance record for a specific component on a specific piece of equipment.
     *
     * @param Model $equipment
     * @param Component $component
     * @return Maintenance|null
     */
    private function findLastMaintenanceForComponent(Model $equipment, Component $component): ?Maintenance
    {
        // Buscamos en la tabla de mantenimientos.
        // Filtramos por el equipo polimórfico (sea Asset o Generator).
        // Usamos whereHas para asegurarnos que el mantenimiento incluye el componente específico.
        // Ordenamos por la fecha más reciente y tomamos el primero.
        return Maintenance::where('maintainable_type', $equipment->getMorphClass())
            ->where('maintainable_id', $equipment->getKey())
            ->whereHas('components', function ($query) use ($component) {
                $query->where('components.id', $component->id);
            })
            ->latest('date') // Equivalente a ->orderBy('date', 'desc')
            ->first();
    }
}
