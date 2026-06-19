<?php

namespace App\Models;

use CodeIgniter\Model;

class PermisosRolEstadoModel extends Model
{
    protected $table      = 'permisos_rol_estado';
    protected $primaryKey = 'id_permiso';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_permiso',
        'id_rol',
        'id_estado_origen',
        'id_estado_destino',
        'puede_transicionar',
        'requiere_aprobacion'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
