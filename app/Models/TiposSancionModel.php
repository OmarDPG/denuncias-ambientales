<?php

namespace App\Models;

use CodeIgniter\Model;

class TiposSancionModel extends Model
{
    protected $table      = 'tipos_sancion';
    protected $primaryKey = 'id_tipo_sancion';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_tipo_sancion',
        'codigo',
        'nombre',
        'descripcion_legal',
        'monto_minimo',
        'monto_maximo',
        'fundamento_legal',
        'activo'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
