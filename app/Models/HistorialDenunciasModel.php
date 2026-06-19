<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialDenunciasModel extends Model
{
    protected $table      = 'historial_denuncias';
    protected $primaryKey = 'id_historial';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_historial',
        'id_denuncia',
        'id_usuario',
        'id_area_origen',
        'id_area_destino',
        'estado_anterior',
        'estado_nuevo',
        'accion',
        'observaciones',
        'metadatos_json',
        'fecha_accion'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
