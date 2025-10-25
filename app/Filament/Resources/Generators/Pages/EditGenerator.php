<?php

namespace App\Filament\Resources\Generators\Pages;

use App\Filament\Resources\Generators\GeneratorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGenerator extends EditRecord
{
    protected static string $resource = GeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar datos actuales desde la relación con campos pivote
        $data['components'] = $this->record->components->map(function ($component) {
            return [
                'component_id' => $component->id,
                'last_maintenance_date' => $component->pivot->last_maintenance_date,
                'last_maintenance_meter' => $component->pivot->last_maintenance_meter,
            ];
        })->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();

        // Eliminar todas las relaciones existentes
        $this->record->components()->detach();

        // Volver a agregar con los datos nuevos
        foreach ($data['components'] as $componentData) {
            $this->record->components()->attach(
                $componentData['component_id'],
                [
                    'last_maintenance_date' => $componentData['last_maintenance_date'] ?? null,
                    'last_maintenance_meter' => $componentData['last_maintenance_meter'] ?? null,
                ]
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
