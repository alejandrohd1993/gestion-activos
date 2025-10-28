<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* Estilos generales que algunos clientes de correo pueden respetar */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
    </style>
</head>
<body style="background-color: #f4f4f7; margin: 0; padding: 0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f4f7;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto;">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding: 25px 0;">
                            <img src="https://rvproducciones.com.co/wp-content/uploads/2023/01/LOGO-2.png" alt="{{ $companyName ?? config('app.name') }} Logo" width="180" style="display: block;">
                        </td>
                    </tr>

                    <!-- Contenido Principal -->
                    <tr>
                        <td style="background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <!-- Título -->
                                <tr>
                                    <td style="color: #333333; font-size: 24px; font-weight: bold; padding-bottom: 20px;">
                                        {{ $title }}
                                    </td>
                                </tr>
                                <!-- Cuerpo del correo -->
                                <tr>
                                    <td style="color: #555555; font-size: 16px; line-height: 1.6;">
                                        {!! $body !!}
                                    </td>
                                </tr>
                                <!-- Botón de Llamada a la Acción (CTA) -->
                                @if(isset($ctaUrl) && isset($ctaText))
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="border-radius: 5px; background-color: #0d6efd;">
                                                    <a href="{{ $ctaUrl }}" target="_blank" style="font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 5px; padding: 12px 25px; border: 1px solid #0d6efd; display: inline-block;">{{ $ctaText }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                                <!-- Firma -->
                                <tr>
                                    <td style="color: #555555; font-size: 16px; line-height: 1.6; padding-top: 20px; border-top: 1px solid #eeeeee;">
                                        Saludos cordiales,<br>
                                        El equipo de {{ $companyName ?? config('app.name') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Pie de Página -->
                    <tr>
                        <td align="center" style="padding: 25px 0; font-size: 12px; color: #999999;">
                            <p>&copy; {{ date('Y') }} {{ $companyName ?? config('app.name') }}. Todos los derechos reservados.</p>
                            @if(isset($developer))
                                <p>Desarrollado por {{ $developer }}.</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
