<?php

namespace App\Models;

use CodeIgniter\Model;

class TurnadosModel extends Model
{
    protected $table      = 'turnados';
    protected $primaryKey = 'id_turnado';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_turnado',
        'id_denuncia',
        'id_area_origen',
        'id_area_destino',
        'id_usuario_turna',
        'id_usuario_recibe',
        'tipo_turnado',
        'motivo',
        'observaciones',
        'id_documento_oficio',
        'fecha_turnado',
        'fecha_recepcion',
        'fecha_respuesta',
        'estatus_turnado',
        'tiempo_respuesta_dias',
        'metadatos_json'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
