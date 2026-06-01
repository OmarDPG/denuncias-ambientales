<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: letter; margin: 20mm 15mm; }
        body { font-family: 'Montserrat', Arial, sans-serif; font-size: 11pt; line-height: 1.6; }
        .marca-agua { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                      opacity: 0.08; z-index: -1; width: 400px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { margin: 0 10px; vertical-align: middle; }
        .fecha { text-align: right; margin: 20px 0; font-size: 11pt; color: #000; }
        .titulo { text-align: justify; font-weight: bold; font-size: 11pt; margin: 20px 0; }
        .datos-folio { border: 2px solid #2c5f2d; padding: 15px; margin: 20px 0; 
                       background: #f0f7f0; }
        .cuerpo { text-align: justify; line-height: 1; font-size: 11pt; }
        .firma { text-align: center; margin-top: 40px; font-weight: bold; font-size: 12pt; }
        .footer { position: fixed; bottom: 10mm; width: 100%; font-size: 8pt; 
                  text-align: center; color: #666; }
        .quetzal { width: 140%; }
        .gobPue { height: 80px; }
        .desarrolloSustentable { height: 60px; }
        .porAmor { height: 45px; }
    </style>
</head>
<body>
    <!-- Marca de agua -->
    <div class="marca-agua">
        <img src="<?= FCPATH . 'acuse/quetzal.png' ?>" alt="" class="quetzal">
    </div>
    
    <!-- Encabezado con logos -->
    <div class="header">
        <img src="<?= FCPATH . 'acuse/gobPue.png' ?>" alt="Gobierno de Puebla" class="gobPue">
        <img src="<?= FCPATH . 'acuse/desarrolloSustentable.png' ?>" alt="Desarrollo Sustentable" class="desarrolloSustentable">
        <img src="<?= FCPATH . 'acuse/porAmor.png' ?>" alt="Por Amor a Puebla" class="porAmor">
    </div>
    
    <!-- Fecha -->
    <div class="fecha">
        <p></p>San Andrés Cholula, Puebla a <?= $fechaActual ?></p>
        <p><?= $folio ?></p>
    </div>
    
    <!-- Título -->
    <div class="titulo">
        ACUSE DE RECIBIDO DENUNCIA POPULAR EN MATERIA AMBIENTAL
    </div>

    
    <!-- Cuerpo del texto -->
    <div class="cuerpo">
        <p>C. <?= mb_strtoupper($nombreCompleto, 'UTF-8') ?>, esperando se encuentre bien, esta Dependencia 
        Estatal agradece el acercamiento y su interés en salvaguardar el medio ambiente. 
        Por lo cual, se le hace de conocimiento que su denuncia popular fue recibida en esta 
        Dependencia en fecha <?= $fechaRecepcionFormato ?>. Misma que será estudiada y analizada 
        con la finalidad de proporcionar una atención oportuna de acuerdo a la competencia de 
        esta Dependencia Estatal; sin embargo, en caso de que la denuncia resultase materia de 
        otra instancia, esta será remitida para su atención.</p>
        
        <p>No se omite mencionar que, con fundamento por lo previsto en el artículo 55 segundo 
        párrafo del Código de Procedimientos Civiles para el Estado Libre y Soberano de Puebla, 
        numeral aplicado de manera supletoria al artículo 3 de la Ley para la Protección del 
        Ambiente Natural y el Desarrollo Sustentable del Estado de Puebla, la notificación del 
        estatus de su denuncia será realizada por lista, la cual se fija para su consulta en los 
        estrados que ocupa la Dirección General de Inspección y Vigilancia de esta Secretaría, 
        encontrada en Lateral Recta a Cholula KM. 5.5, número 2401, San Andrés Cholula, Puebla, 
        C.P. 72810. A la cual podrá acudir en un horario de lunes a viernes de 9:00 a 15:00 horas.</p>
        
        <p>Sin otro particular por el momento, le reiteramos nuestro agradecimiento por preocuparse 
        por el medio ambiente.</p>
    </div>
    
    <!-- Firma -->
    <div class="firma">
        <p>ATENTAMENTE</p>
        <p>DIRECCIÓN GENERAL DE INSPECCIÓN Y VIGILANCIA<br>
        DEPARTAMENTO DE DENUNCIAS AMBIENTALES</p>
    </div>
</body>
</html>