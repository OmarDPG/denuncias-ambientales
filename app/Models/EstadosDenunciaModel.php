<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadosDenunciaModel extends Model
{
    protected $table      = 'estados_denuncia';
    protected $primaryKey = 'id_estado';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_estado',
        'codigo',
        'nombre_mostrar',
        'descripcion',
        'orden',
        'es_terminal',
        'requiere_documento',
        'color_ui',
        'nombre_estado'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
