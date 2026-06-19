<?php

namespace App\Models;

use CodeIgniter\Model;

class DenunciasModel extends Model
{
    protected $table      = 'denuncias';
    protected $primaryKey = 'id_denuncia';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_denuncia',
        'folio',
        'estatus',
        'fecha_captura',
        'fecha_resolucion',
        'notas_internas',
        'tipo_persona',
        'nombre_completo',
        'genero',
        'estado',
        'municipio',
        'colonia',
        'codigo_postal',
        'calle',
        'numero_exterior',
        'numero_interior',
        'email',
        'telefono',
        'es_representante',
        'razon_social',
        'nombre_representante',
        'id_tipo_denuncia',
        'id_tema_denuncia',
        'id_motivo_verificacion',
        'tipo_denuncia',
        'hechos_denunciados',
        'latitud',
        'longitud',
        'nombre_denunciado',
        'denunciado_es_moral',
        'razon_social_denunciado',
        'municipio_denunciado',
        'colonia_denunciado',
        'calle_denunciado',
        'codigo_postal_denunciado',
        'numero_exterior_denunciado',
        'numero_interior_denunciado',
        'clave_cvv',
        'codigo_verificacion',
        'codigo_verificacion_expira',
        'intentos_verificacion',
        'verificado_en',
        'ultimo_envio_codigo',
        // Nuevos campos de flujo operativo
        'id_area_responsable',
        'id_estado_actual',
        'id_usuario_asignado',
        'fecha_ultimo_cambio_estado',
        'fecha_turnado_dns',
        'fecha_turnado_ds',
        'fecha_aprobacion_dns',
        'fecha_inicio_inspeccion',
        'fecha_fin_inspeccion',
        'fecha_sancion',
        'prioridad',
        'flujo_excepcional',
        'razon_flujo_excepcional',
        'id_motivo_rechazo',
        'id_tipo_sancion',
        'monto_sancion'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
