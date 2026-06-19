<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentosDenunciaModel extends Model
{
    protected $table      = 'documentos_denuncia';
    protected $primaryKey = 'id_documento';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_documento',
        'id_denuncia',
        'id_tipo_documento',
        'id_usuario_subida',
        'id_area',
        'id_estado_denuncia',
        'nombre_original',
        'nombre_almacenado',
        'ruta_archivo',
        'tipo_mime',
        'peso_bytes',
        'hash_sha256',
        'version',
        'es_oficial',
        'firmado',
        'metadata_json',
        'fecha_subida',
        'observaciones'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
