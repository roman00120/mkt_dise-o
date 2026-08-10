<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b; line-height: 1.6;">

    <!-- Email Container -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 650px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">

                    <!-- Top Red Accent Bar -->
                    <tr>
                        <td style="background-color: #e30613; height: 6px; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    <!-- Header Banner -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 28px 36px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" style="vertical-align: middle;">
                                        <div style="font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px;">
                                            TOTAL<span style="color: #e30613;">GROUND</span>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px;">
                                            TG Creative Hub
                                        </div>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="display: inline-block; background-color: rgba(227, 6, 19, 0.15); border: 1px solid rgba(227, 6, 19, 0.4); color: #ff4d5a; font-size: 11px; font-weight: 800; padding: 6px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ $badge }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Body Content -->
                    <tr>
                        <td style="padding: 36px 36px 28px;">
                            <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 800; color: #0f172a; tracking: -0.3px;">
                                Hola, {{ $recipientName }} 👋
                            </h2>

                            <div style="background-color: #f8fafc; border-left: 4px solid #e30613; padding: 16px 20px; border-radius: 0 12px 12px 0; margin-bottom: 28px;">
                                <p style="margin: 0; font-size: 14px; color: #334155; line-height: 1.6; font-weight: 500;">
                                    {{ $intro }}
                                </p>
                            </div>

                            <!-- Request Details Card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e2e8f0; border-radius: 12px; border-collapse: separate; border-spacing: 0; overflow: hidden; margin-bottom: 24px;">
                                <thead>
                                    <tr style="background-color: #f8fafc;">
                                        <th colspan="2" align="left" style="padding: 14px 20px; font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid #e2e8f0;">
                                            📋 Especificaciones de la Solicitud
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="50%" style="padding: 14px 20px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Folio</div>
                                            <div style="font-size: 15px; font-weight: 900; color: #e30613; margin-top: 3px; font-family: monospace;">
                                                {{ $requestModel->folio }}
                                            </div>
                                        </td>
                                        <td width="50%" style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Prioridad</div>
                                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">
                                                {{ $requestModel->requested_priority?->label() }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 14px 20px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Solicitante</div>
                                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">
                                                {{ $requestModel->requester?->name }}
                                            </div>
                                        </td>
                                        <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Servicio Requerido</div>
                                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">
                                                {{ $requestModel->service?->label() }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 14px 20px; border-right: 1px solid #e2e8f0; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Responsable Asignado</div>
                                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">
                                                {{ $requestModel->assignee?->name ?: 'Pendiente de asignación' }}
                                            </div>
                                        </td>
                                        <td style="padding: 14px 20px; vertical-align: top;">
                                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Estado Actual</div>
                                            <div style="font-size: 14px; font-weight: 800; color: #059669; margin-top: 3px;">
                                                {{ $status }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Description Box -->
                            @if($requestModel->description)
                                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                        📝 Descripción del Proyecto / Brief:
                                    </div>
                                    <div style="font-size: 14px; color: #1e293b; line-height: 1.6; whitespace: pre-line;">
                                        {{ $requestModel->description }}
                                    </div>
                                </div>
                            @endif

                            <!-- Attachments -->
                            @if($requestModel->files->isNotEmpty())
                                <div style="border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px 20px; margin-bottom: 28px; background-color: #fafafa;">
                                    <div style="font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                                        📁 Archivos Adjuntos ({{ $requestModel->files->count() }}):
                                    </div>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #334155;">
                                        @foreach($requestModel->files as $file)
                                            <li style="margin-bottom: 4px;">
                                                <strong>{{ $file->original_name }}</strong> ({{ ucfirst($file->category) }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Primary Action Button -->
                            <div style="text-align: center; margin: 32px 0 16px;">
                                <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; background-color: #e30613; color: #ffffff; font-size: 15px; font-weight: 800; text-decoration: none; padding: 16px 40px; border-radius: 12px; box-shadow: 0 4px 14px rgba(227, 6, 19, 0.35); transition: background-color 0.2s;">
                                    {{ $actionLabel }} &nbsp; →
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 36px; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 12px; color: #64748b; font-weight: 500;">
                                Mensaje generado automáticamente por <strong>TG Creative Hub</strong>.
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                © {{ now()->year }} Total Ground. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
