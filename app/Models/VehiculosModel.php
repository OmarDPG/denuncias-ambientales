<?php

namespace App\Models;

use CodeIgniter\Model;

class VehiculosModel extends Model
{
    protected $table      = 'vehiculos';
    protected $primaryKey = 'id_vehiculo';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_vehiculo',
        'id_denuncia', 
        'placa', 
        'marca', 
        'modelo', 
        'color', 
        'submarca'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
