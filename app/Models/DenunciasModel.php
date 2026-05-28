<?php

namespace App\Models;

use CodeIgniter\Model;

class DenunciasModel extends Model
{
    protected $table      = 'denuncias';
    protected $primaryKey = 'id_denuncia';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_denuncia',
        'folio',
        'estatus',
        'fecha_captura',
        'fecha_resolucion',
        'notas_internas',
        'tipo_persona',
        'nombre_completo',
        'genero',
        'estado',
        'municipio',
        'colonia',
        'codigo_postal',
        'calle',
        'numero_exterior',
        'numero_interior',
        'email',
        'telefono',
        'es_representante',
        'razon_social',
        'nombre_representante',
        'id_tipo_denuncia',
        'id_tema_denuncia',
        'tipo_denuncia',
        'hechos_denunciados',
        'latitud',
        'longitud',
        'nombre_denunciado',
        'denunciado_es_moral',
        'razon_social_denunciado',
        'municipio_denunciado',
        'colonia_denunciado',
        'calle_denunciado',
        'codigo_postal_denunciado',
        'numero_exterior_denunciado',
        'numero_interior_denunciado',
        'clave_cvv'
    ];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
