<?php

namespace App\Models;

use CodeIgniter\Model;

class TemaDenunciaModel extends Model
{
    protected $table      = 'tema_denuncia';
    protected $primaryKey = 'id_tema_denuncia';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_tema_denuncia',
        'id_tipo_denuncia',
        'nombre',
        'activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
