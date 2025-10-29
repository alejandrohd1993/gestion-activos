<?php

namespace App\Mail;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use NumberFormatter;

class ServiceCompletedForBilling extends Mailable
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
            subject: 'Servicio Completado y Listo para Facturar: ' . $this->service->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->service->load('customer');

        // Formatear el valor del servicio a moneda local (COP)
        $formatter = new NumberFormatter('es_CO', NumberFormatter::CURRENCY);
        $serviceValue = $formatter->formatCurrency($this->service->service_value, 'COP');

        $body = '<p>Hola equipo de contabilidad,</p>';
        $body .= '<p>El siguiente servicio ha sido marcado como <strong>completado</strong> y está listo para ser facturado:</p>';
        $body .= '<ul style="list-style-type: none; padding-left: 0;">';
        $body .= '<li><strong>Servicio:</strong> ' . $this->service->name . '</li>';
        $body .= '<li><strong>Cliente:</strong> ' . $this->service->customer->name . '</li>';
        $body .= '<li><strong>Fecha de Finalización:</strong> ' . (optional($this->service->end_date)->format('d/m/Y') ?? 'No especificada') . '</li>';
        $body .= '<li><strong>Valor del Servicio:</strong> ' . $serviceValue . '</li>';
        $body .= '</ul>';
        $body .= '<p>Por favor, procedan con el proceso de facturación correspondiente.</p>';

        return new Content(
            view: 'emails.corporate-template',
            with: [
                'title' => 'Servicio Listo para Facturar',
                'body' => $body,
                // 'ctaUrl' => route('filament.admin.resources.services.edit', $this->service),
                // 'ctaText' => 'Ver Detalles del Servicio',
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
