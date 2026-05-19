<?php

namespace App\Models;

use CodeIgniter\Model;

class ArchivosDenunciasModel extends Model
{
    protected $table      = 'evidencias_denuncias';
    protected $primaryKey = 'id_evidencia';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_evidencia', 'id_denuncia', 'nombre_original', 'ruta_archivo', 'tipo_archivo', 'peso_bytes', 'fecha_subida',];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
