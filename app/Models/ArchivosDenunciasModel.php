<?php

namespace App\Models;

use CodeIgniter\Model;

class ArchivosDenunciasModel extends Model
{
    protected $table      = 'evidencias_denuncia';
    protected $primaryKey = 'id_evidencia';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_denuncia', 'nombre_original', 'ruta_archivo', 'tipo_archivo', 'tipo_documento', 'peso_bytes'];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
