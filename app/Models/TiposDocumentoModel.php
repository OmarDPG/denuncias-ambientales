<?php

namespace App\Models;

use CodeIgniter\Model;

class TiposDocumentoModel extends Model
{
    protected $table      = 'tipos_documento';
    protected $primaryKey = 'id_tipo_documento';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_tipo_documento',
        'codigo',
        'nombre',
        'descripcion',
        'obligatorio',
        'requiere_firma',
        'extensiones_permitidas',
        'tamano_maximo_kb',
        'activo'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
