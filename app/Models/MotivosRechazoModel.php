<?php

namespace App\Models;

use CodeIgniter\Model;

class MotivosRechazoModel extends Model
{
    protected $table      = 'motivos_rechazo_dns';
    protected $primaryKey = 'id_motivo';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_motivo',
        'codigo',
        'nombre',
        'descripcion',
        'fundamento_legal',
        'activo'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
