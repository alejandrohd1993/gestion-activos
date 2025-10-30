<?php

namespace App\Observers;

use App\Enums\UnitType;
use App\Enums\UserType;
use App\Mail\MaintenanceAlertMail;
use App\Models\Component;
use App\Models\Usage;
use App\Models\User;
use App\Services\MaintenanceStatusService;
use Illuminate\Support\Facades\Mail;

class UsageObserver
{
    /**
     * Inyectamos nuestro servicio para poder usarlo.
     * @var MaintenanceStatusService
     */
    protected MaintenanceStatusService $maintenanceStatusService;

    public function __construct(MaintenanceStatusService $maintenanceStatusService)
    {
        $this->maintenanceStatusService = $maintenanceStatusService;
    }

    /**
     * Handle the Usage "created" event.
     * Este método se ejecuta automáticamente después de que se crea un nuevo registro de Usage.
     */
    public function created(Usage $usage): void
    {
        // Cargar las relaciones necesarias para evitar consultas N+1.
        $usage->load('equipment.components.unit');
        $equipment = $usage->equipment;

        // Si por alguna razón el uso no tiene un equipo asociado, no hacemos nada.
        if (!$equipment) {
            return;
        }

        // Usamos nuestro servicio para obtener el estado de todos los componentes del equipo.
        $componentStatuses = $this->maintenanceStatusService->getComponentsStatus($equipment);

        // Filtramos solo los componentes que necesitan una notificación.
        $componentsToNotify = $componentStatuses->filter(function ($status) {
            // Solo nos interesan los de 'uso_acumulado' que estén en 'warning' o 'expired'.
            return $status->unit_type === UnitType::USO_ACUMULADO->value
                && in_array($status->status, ['warning', 'expired']);
        });

        // Si no hay componentes que notificar, terminamos el proceso.
        if ($componentsToNotify->isEmpty()) {
            return;
        }

            // Buscamos a todos los usuarios de tipo 'ADMINISTRATIVO' que estén activos.
            $adminUsers = User::where('type', UserType::ADMINISTRATIVO)
                ->where('is_active', true)
                ->get();

        // Si no hay administradores, no podemos enviar correos.
        if ($adminUsers->isEmpty()) {
            return;
        }

        // Iteramos sobre cada componente que requiere alerta y enviamos el correo.
        foreach ($componentsToNotify as $status) {
            // Recuperamos la instancia completa del componente para pasarla al Mailable.
            $component = $equipment->components->firstWhere('id', $status->component_id);

            if ($component) {
                // Enviamos el correo a todos los administradores de forma síncrona.
                Mail::to($adminUsers)->send(new MaintenanceAlertMail($equipment, $component, $status));
            }
        }
    }
}
