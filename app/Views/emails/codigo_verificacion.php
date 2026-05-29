<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
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
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #333;
            margin: 15px 0;
        }
        .folio-box {
            background-color: #f8f9fa;
            border-left: 4px solid #2c5f2d;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .folio-box strong {
            color: #2c5f2d;
        }
        .codigo-container {
            background: linear-gradient(135deg, #f0f7f0 0%, #e8f5e9 100%);
            border: 3px solid #2c5f2d;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .codigo-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .codigo-numero {
            font-size: 48px;
            font-weight: bold;
            color: #2c5f2d;
            letter-spacing: 12px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .alert p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .instructions {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .instructions p {
            margin: 5px 0;
            color: #0d47a1;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .footer .logo {
            color: #2c5f2d;
            font-weight: bold;
            font-size: 14px;
        }
        .warning {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #c62828;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🌱</div>
            <h1>Sistema de Denuncias Ambientales</h1>
        </div>
        
        <div class="content">
            <p>Estimado(a) <strong><?= esc($nombre) ?></strong>,</p>
            
            <p>Gracias por presentar tu denuncia ambiental. Para completar el proceso de registro y activar tu denuncia, necesitamos verificar tu dirección de correo electrónico.</p>
            
            <div class="folio-box">
                <p style="margin: 0;">
                    <strong>Folio de tu denuncia:</strong> <?= esc($folio) ?>
                </p>
            </div>
            
            <div class="codigo-container">
                <div class="codigo-label">
                    Tu código de verificación es:
                </div>
                <div class="codigo-numero">
                    <?= esc($codigo) ?>
                </div>
            </div>
            
            <div class="alert">
                <p>
                    ⏱ <strong>Este código es válido por <?= esc($expiracion) ?></strong>
                </p>
            </div>
            
            <div class="instructions">
                <p><strong>📋 Instrucciones:</strong></p>
                <p>1. Ingresa este código de 6 dígitos en la ventana de verificación</p>
                <p>2. El código es de un solo uso y expirará automáticamente</p>
                <p>3. Si el código expira, puedes solicitar uno nuevo</p>
            </div>
            
            <p>Una vez verificado tu correo, tu denuncia será procesada y podrás darle seguimiento usando el folio proporcionado.</p>
            
            <div class="warning">
                <p>
                    <strong>⚠️ Seguridad:</strong> Si no solicitaste este código, ignora este mensaje de forma segura. Nadie más puede activar tu denuncia sin acceso a este correo.
                </p>
            </div>
            
            <p style="color: #999; font-size: 13px; margin-top: 30px;">
                <strong>Nota:</strong> Si no recibes más mensajes de nosotros, revisa tu carpeta de correo no deseado o spam.
            </p>
        </div>
        
        <div class="footer">
            <p class="logo">🌱 Sistema de Denuncias Ambientales</p>
            <p>Gobierno del Estado</p>
            <p style="margin-top: 15px; color: #999;">
                Este es un correo automático. Por favor no respondas a este mensaje.
            </p>
            <p style="color: #999;">
                Fecha de envío: <?= esc($fecha) ?>
            </p>
        </div>
    </div>
</body>
</html>
