<?php

namespace App\Models;

use CodeIgniter\Model;

class CentroVerificacionModel extends Model
{
    protected $table      = 'centros_verificacion_vehicular';
    protected $primaryKey = 'id_cvv';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_cvv',
        'clave',
        'municipio',
        'direccion',
        'telefono',
        'activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
