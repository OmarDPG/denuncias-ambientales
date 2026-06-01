<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Denuncia</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2c5f2d 0%, #97bc62 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #333;
            margin: 15px 0;
        }
        .success-box {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-left: 5px solid #2c5f2d;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .success-box h2 {
            color: #1b5e20;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .folio-box {
            background-color: #f8f9fa;
            border: 2px solid #2c5f2d;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }
        .folio-box .label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .folio-box .numero {
            font-size: 28px;
            font-weight: bold;
            color: #2c5f2d;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        .info-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .info-section h3 {
            color: #2c5f2d;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-bottom: 2px solid #2c5f2d;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 140px;
        }
        .info-value {
            color: #333;
        }
        .alert {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .alert h4 {
            color: #0d47a1;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .alert p {
            margin: 5px 0;
            color: #1565c0;
            font-size: 14px;
        }
        .alert strong {
            color: #0d47a1;
        }
        .attachment-notice {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .attachment-notice p {
            margin: 5px 0;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer strong {
            color: #2c5f2d;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #ddd, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✅</div>
            <h1>¡Denuncia Confirmada Exitosamente!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Estimado(a) <strong><?= esc($nombreCompleto) ?></strong>,</p>

            <div class="success-box">
                <h2>Su denuncia ha sido recibida y confirmada</h2>
                <p style="margin: 0; color: #2e7d32;">
                    Le confirmamos que su denuncia ambiental ha sido verificada exitosamente 
                    y ha sido registrada en el sistema de la Secretaría de Medio Ambiente, 
                    Ordenamiento Territorial y Desarrollo Sustentable del Estado de Puebla.
                </p>
            </div>

            <!-- Folio -->
            <div class="folio-box">
                <div class="label">Su Número de Folio</div>
                <div class="numero"><?= esc($folio) ?></div>
            </div>

            <!-- Información del Registro -->
            <div class="info-section">
                <h3>📋 Datos del Registro</h3>
                <div class="info-row">
                    <div class="info-label">Folio:</div>
                    <div class="info-value"><?= esc($folio) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Denunciante:</div>
                    <div class="info-value"><?= esc($nombreCompleto) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tipo de Denuncia:</div>
                    <div class="info-value"><?= esc($tipoDenuncia) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Recepción:</div>
                    <div class="info-value"><?= esc($fechaRecepcion) ?></div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Acuse Adjunto -->
            <div class="attachment-notice">
                <p><strong>📎 Documento Adjunto:</strong></p>
                <p>
                    Se adjunta a este correo el <strong>Acuse de Recibido Oficial</strong> 
                    en formato PDF. Le recomendamos guardar este documento para sus registros.
                </p>
            </div>

            <!-- Información Importante -->
            <div class="alert">
                <h4>📢 Información Importante sobre el Seguimiento</h4>
                <p>
                    De conformidad con el artículo 55 segundo párrafo del Código de Procedimientos 
                    Civiles para el Estado Libre y Soberano de Puebla, aplicado de manera supletoria 
                    al artículo 3 de la Ley para la Protección del Ambiente Natural y el Desarrollo 
                    Sustentable del Estado de Puebla, <strong>la notificación del estatus de su 
                    denuncia será realizada por lista</strong> en los estrados de esta Secretaría.
                </p>
            </div>

            <div class="info-section">
                <h3>📍 Ubicación para Consulta de Estrados</h3>
                <div class="info-row">
                    <div class="info-label">Dependencia:</div>
                    <div class="info-value">Dirección General de Inspección y Vigilancia</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dirección:</div>
                    <div class="info-value">Lateral Recta a Cholula KM. 5.5, número 2401<br>San Andrés Cholula, Puebla, C.P. 72810</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Horario:</div>
                    <div class="info-value">Lunes a Viernes de 9:00 a 15:00 horas</div>
                </div>
            </div>

            <div class="divider"></div>

            <p style="text-align: center; color: #2c5f2d; font-weight: 600; font-size: 16px;">
                Le agradecemos su compromiso con el cuidado del medio ambiente
            </p>

            <p style="text-align: center; color: #666; font-size: 13px; margin-top: 30px;">
                <strong>Nota:</strong> Este es un correo electrónico automático, por favor no responda a este mensaje.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Secretaría de Medio Ambiente, Ordenamiento Territorial<br>y Desarrollo Sustentable del Estado de Puebla</strong></p>
            <p>Sistema de Denuncias Ambientales</p>
            <p style="margin-top: 15px; font-size: 11px; color: #999;">
                Gobierno del Estado de Puebla | Por Amor a Puebla
            </p>
        </div>
    </div>
</body>
</html>
