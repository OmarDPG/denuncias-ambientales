<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoDenunciaModel extends Model
{
    protected $table      = 'tipo_denuncia';
    protected $primaryKey = 'id_tipo_denuncia';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_tipo_denuncia',
        'nombre',
        'activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
