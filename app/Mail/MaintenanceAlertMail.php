<?php

namespace App\Mail;

use App\Models\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use stdClass;

class MaintenanceAlertMail extends Mailable
{

    /**
     * The equipment instance (Asset or Generator).
     * @var Model
     */
    public Model $equipment;

    /**
     * The component instance.
     * @var \App\Models\Component
     */
    public \App\Models\Component $component;

    /**
     * The status data object from MaintenanceStatusService.
     * @var stdClass
     */
    public stdClass $status;

    /**
     * Create a new message instance.
     *
     * @param Model $equipment
     * @param \App\Models\Component $component
     * @param stdClass $status
     */
    public function __construct(Model $equipment, \App\Models\Component $component, stdClass $status)
    {
        $this->equipment = $equipment;
        $this->component = $component;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status->status === 'expired'
            ? "Alerta de Mantenimiento Vencido: {$this->component->name}"
            : "Alerta de Mantenimiento Próximo: {$this->component->name}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $title = $this->status->status === 'expired'
            ? 'Mantenimiento Vencido'
            : 'Mantenimiento Preventivo Próximo';

        $body = $this->buildBody();

        return new Content(
            view: 'emails.corporate-template',
            with: [
                'title' => $title,
                'body' => $body,
                // 'ctaUrl' => route('filament.admin.resources.maintenances.create'), // URL para crear un mantenimiento
                // 'ctaText' => 'Crear Mantenimiento',
                'companyName' => config('app.name'),
                'developer' => 'RV Producciones',
            ],
        );
    }

    /**
     * Builds the email body with component details.
     *
     * @return string
     */
    private function buildBody(): string
    {
        $equipmentName = $this->equipment->name;
        $componentName = $this->component->name;
        $unitName = $this->component->unit->name;

        $message = $this->status->status === 'expired'
            ? "El mantenimiento para el componente <strong>{$componentName}</strong> del equipo <strong>{$equipmentName}</strong> está vencido."
            : "El componente <strong>{$componentName}</strong> del equipo <strong>{$equipmentName}</strong> está próximo a requerir mantenimiento.";

        // Formateamos los valores si el equipo es un Generador (horómetro en segundos)
        $lastMaintenanceMeter = $this->formatSeconds($this->status->last_maintenance_meter);
        $currentMeter = $this->formatSeconds($this->status->current_meter);
        $nextMaintenanceMeter = $this->formatSeconds($this->status->next_maintenance_meter);
        $usageRemaining = $this->formatSeconds($this->status->usage_remaining);

        $details = "
            <p>{$message}</p>
            <ul style='list-style-type: none; padding: 0;'>
                <li><strong>Equipo:</strong> {$equipmentName}</li>
                <li><strong>Componente:</strong> {$componentName}</li>
                <li><strong>Medidor en último mant.:</strong> {$lastMaintenanceMeter}</li>
                <li><strong>Medidor actual:</strong> {$currentMeter}</li>
                <li><strong>Próximo mantenimiento a las:</strong> {$nextMaintenanceMeter}</li>
                <li><strong>Uso restante:</strong> <strong style='color: #dc3545;'>{$usageRemaining}</strong></li>
            </ul>
            <p>Por favor, programe el mantenimiento correspondiente a la brevedad posible.</p>
        ";

        return $details;
    }

    /**
     * Formats seconds into HH:MM:SS format if the equipment is a Generator.
     *
     * @param int $value The value in seconds (or km for Assets).
     * @return string The formatted value.
     */
    private function formatSeconds(int $value): string
    {
        // Si el equipo no es un generador, devolvemos el valor tal cual (ej. para Km).
        if (!$this->equipment instanceof Generator) {
            return (string) $value;
        }

        $isNegative = $value < 0;
        $absoluteValue = abs($value);

        $hours = floor($absoluteValue / 3600);
        $minutes = floor(($absoluteValue % 3600) / 60);
        $seconds = $absoluteValue % 60;

        return ($isNegative ? '-' : '') . sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
