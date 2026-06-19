<?php

namespace App\Models;

use CodeIgniter\Model;

class AreasModel extends Model
{
    protected $table      = 'areas';
    protected $primaryKey = 'id_area';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_area', 
    'nombre', 
    'descripcion', 
    'activo', 
    'created_at', 
    'updated_at', 
    'id_area'];


    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
