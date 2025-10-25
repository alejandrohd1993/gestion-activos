<?php

namespace App\Filament\Resources\Maintenances\Pages;

use App\Filament\Resources\Maintenances\MaintenanceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMaintenance extends CreateRecord
{
    protected static string $resource = MaintenanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extraer los datos de los componentes antes de crear el mantenimiento
        $componentsData = $data['components'];
        unset($data['components']);

        // Crear el registro de mantenimiento
        $maintenance = static::getModel()::create($data);

        // Preparar los datos para la sincronización en la tabla pivote
        $syncData = collect($componentsData)->pluck('component_id')->toArray();

        

        // Sincronizar la relación
        $maintenance->components()->sync($syncData);

        return $maintenance;
    }

}
