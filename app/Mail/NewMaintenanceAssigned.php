<?php

namespace App\Mail;

use App\Models\Maintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMaintenanceAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia del mantenimiento.
     *
     * @var \App\Models\Maintenance
     */
    public $maintenance;

    /**
     * Create a new message instance.
     */
    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Mantenimiento Asignado: ' . $this->maintenance->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Cargar relaciones para evitar consultas N+1
        $this->maintenance->load('operator', 'maintainable', 'components', 'provider');

        // Construir el cuerpo del correo dinámicamente
        $body = '<p>Hola <strong>' . $this->maintenance->operator->name . '</strong>,</p>';
        $body .= '<p>Se te ha asignado un nuevo mantenimiento con los siguientes detalles:</p>';
        $body .= '<ul style="list-style-type: none; padding-left: 0;">';
        $body .= '<li><strong>Mantenimiento:</strong> ' . $this->maintenance->name . '</li>';
        $body .= '<li><strong>Equipo:</strong> ' . $this->maintenance->maintainable->name . '</li>';
        $body .= '<li><strong>Tipo:</strong> ' . $this->maintenance->type->getLabel() . '</li>';
        $body .= '<li><strong>Fecha Programada:</strong> ' . $this->maintenance->date->format('d/m/Y') . '</li>';

        if ($this->maintenance->provider) {
            $body .= '<li><strong>Proveedor:</strong> ' . $this->maintenance->provider->name . '</li>';
        }

        if ($this->maintenance->components->isNotEmpty()) {
            $body .= '<li><strong>Componentes/Insumos:</strong> ' . $this->maintenance->components->pluck('name')->join(', ') . '</li>';
        }

        if ($this->maintenance->notes) {
            $body .= '<li><strong>Notas:</strong> ' . $this->maintenance->notes . '</li>';
        }
        $body .= '</ul>';
        $body .= '<p>Por favor, revisa los detalles y prepárate para la ejecución del mantenimiento.</p>';

        return new Content(
            view: 'emails.corporate-template',
            with: [
                'title' => 'Nuevo Mantenimiento Asignado',
                'body' => $body,
                // 'ctaUrl' => route('filament.admin.resources.maintenances.edit', $this->maintenance),
                // 'ctaText' => 'Ver Detalles del Mantenimiento',
                'companyName' => 'RV Producciones',
                'developer' => 'Persistencia Digital',
            ],
        );
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
