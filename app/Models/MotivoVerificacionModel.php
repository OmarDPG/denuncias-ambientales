<?php

namespace App\Models;

use CodeIgniter\Model;

class MotivoVerificacionModel extends Model
{
    protected $table      = 'motivo_verificacion';
    protected $primaryKey = 'id_motivo_verificacion';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_motivo_verificacion', 
    'id_motivo_verificacion', 
    'descripcion', 
    'requiere_accion', 
    'activo', 
    'created_at', 
    'updated_at'];


    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
