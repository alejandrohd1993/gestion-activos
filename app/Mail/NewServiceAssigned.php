<?php

namespace App\Mail;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewServiceAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia del servicio.
     *
     * @var \App\Models\Service
     */
    public $service;

    /**
     * Create a new message instance.
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Servicio Asignado: ' . $this->service->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Cargar relaciones necesarias para evitar consultas N+1
        $this->service->load('customer', 'operator', 'assets', 'generators');

        // Construir el cuerpo del correo
        $body = '<p>Hola <strong>' . $this->service->operator->name . '</strong>,</p>';
        $body .= '<p>Se te ha asignado un nuevo servicio con los siguientes detalles:</p>';
        $body .= '<ul style="list-style-type: none; padding-left: 0;">';
        $body .= '<li><strong>Servicio:</strong> ' . $this->service->name . '</li>';
        $body .= '<li><strong>Lugar:</strong> ' . $this->service->location . '</li>';
        $body .= '<li><strong>Cliente:</strong> ' . $this->service->customer->name . '</li>';
        $body .= '<li><strong>Fecha de Inicio:</strong> ' . ($this->service->start_date ? $this->service->start_date->format('d/m/Y') : 'No especificada') . '</li>';
        
        if ($this->service->assets->isNotEmpty()) {
            $body .= '<li><strong>Activos Asignados:</strong> ' . $this->service->assets->pluck('name')->join(', ') . '</li>';
        }
        if ($this->service->generators->isNotEmpty()) {
            $body .= '<li><strong>Generadores Asignados:</strong> ' . $this->service->generators->pluck('name')->join(', ') . '</li>';
        }

        if ($this->service->notes) {
            $body .= '<li><strong>Notas:</strong> ' . $this->service->notes . '</li>';
        }
        $body .= '</ul>';
        $body .= '<p>Por favor, revisa los detalles y prepárate para la ejecución del servicio.</p>';

        return new Content(
            view: 'emails.corporate-template',
            with: [
                'title' => 'Nuevo Servicio Asignado',
                'body' => $body,
                // 'ctaUrl' => route('filament.admin.resources.services.edit', $this->service),
                // 'ctaText' => 'Ver Detalles del Servicio',
                'companyName' => 'RV Producciones', // O tómalo de config('app.name')
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
