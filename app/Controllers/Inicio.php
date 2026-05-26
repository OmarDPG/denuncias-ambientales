<?php

namespace App\Controllers;

use App\Models\ArchivosDenunciasModel;
use App\Models\DenunciasModel;

class Inicio extends BaseController
{
    protected DenunciasModel $denunciasModel;
    protected ArchivosDenunciasModel $archivosDenunciasModel;

    public function __construct()
    {
        $this->denunciasModel        = new DenunciasModel();
        $this->archivosDenunciasModel = new ArchivosDenunciasModel();
    }

    public function index()
    {
        return view('inicio/header')
            . view('inicio/index')
            . view('inicio/footer');
    }

    // ─── POST inicio/registrarDenuncia ────────────────────────────────────────────────
    public function registrarDenuncia(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['success' => false, 'message' => 'Método no permitido']);
        }

        $esRepresentante   = filter_var($this->request->getPost('es_representante'),   FILTER_VALIDATE_BOOLEAN);
        $denunciadoEsMoral = filter_var($this->request->getPost('denunciado_es_moral'), FILTER_VALIDATE_BOOLEAN);

        // ── Reglas de validación ───────────────────────────────────────────────
        $rules = [
            'tipo_persona'               => ['label' => 'Tipo de Persona',      'rules' => 'required|in_list[fisica,moral]'],
            'nombre_completo'            => ['label' => 'Nombre Completo',       'rules' => 'required|min_length[3]|max_length[255]'],
            'genero'                     => ['label' => 'Género',                'rules' => 'required|in_list[masculino,femenino,otro,prefiero-no-decir]'],
            'estado'                     => ['label' => 'Estado',                'rules' => 'required|max_length[100]'],
            'municipio'                  => ['label' => 'Municipio',             'rules' => 'required|max_length[100]'],
            'colonia'                    => ['label' => 'Colonia',               'rules' => 'required|max_length[150]'],
            'codigo_postal'              => ['label' => 'Código Postal',         'rules' => 'required|exact_length[5]|numeric'],
            'calle'                      => ['label' => 'Calle',                 'rules' => 'required|max_length[150]'],
            'numero_exterior'            => ['label' => 'Número Exterior',       'rules' => 'required|max_length[50]'],
            'numero_interior'            => ['label' => 'Número Interior',       'rules' => 'permit_empty|max_length[50]'],
            'email'                      => ['label' => 'Correo Electrónico',    'rules' => 'required|valid_email|max_length[150]'],
            'telefono'                   => ['label' => 'Teléfono',              'rules' => 'required|exact_length[10]|numeric'],
            'tipo_denuncia'              => ['label' => 'Tipo de Denuncia',      'rules' => 'required|in_list[Impacto Ambiental,Residuos Especiales,Contaminación Atmosférica,Contaminación Auditiva,Contaminación Visual,Ordenamiento Territorial]'],
            'hechos_denunciados'         => ['label' => 'Hechos Denunciados',    'rules' => 'required|min_length[20]|max_length[10000]'],
            'latitud'                    => ['label' => 'Latitud',               'rules' => 'permit_empty|decimal'],
            'longitud'                   => ['label' => 'Longitud',              'rules' => 'permit_empty|decimal'],
            'nombre_denunciado'          => ['label' => 'Nombre Denunciado',     'rules' => 'required|max_length[255]'],
            'municipio_denunciado'       => ['label' => 'Municipio Denunciado',  'rules' => 'required|max_length[100]'],
            'colonia_denunciado'         => ['label' => 'Colonia Denunciada',    'rules' => 'required|max_length[150]'],
            'calle_denunciado'           => ['label' => 'Calle Denunciada',      'rules' => 'required|max_length[150]'],
            'codigo_postal_denunciado'   => ['label' => 'C.P. Denunciado',       'rules' => 'required|exact_length[5]|numeric'],
            'numero_exterior_denunciado' => ['label' => 'N° Ext. Denunciado',    'rules' => 'required|max_length[50]'],
            'numero_interior_denunciado' => ['label' => 'N° Int. Denunciado',    'rules' => 'permit_empty|max_length[50]'],
        ];

        if ($esRepresentante) {
            $rules['razon_social']         = ['label' => 'Razón Social',          'rules' => 'required|max_length[255]'];
            $rules['nombre_representante'] = ['label' => 'Nombre Representante',  'rules' => 'required|max_length[255]'];
        }

        if ($denunciadoEsMoral) {
            $rules['razon_social_denunciado'] = ['label' => 'Razón Social Denunciado', 'rules' => 'required|max_length[255]'];
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        // ── Procesamiento de archivos de evidencia ─────────────────────────────
        $evidencias   = [];
        // getFiles() sin argumento devuelve la estructura completa; ['evidencias']
        // contiene el array de objetos UploadedFile cuando el campo es evidencias[]
        $todosArchivos = $this->request->getFiles();
        $archivos      = $todosArchivos['evidencias'] ?? [];
        $allowedMimes  = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxSizeKb     = 25 * 1024; // 25 MB en KB
        $uploadPath    = WRITEPATH . 'uploads/evidencias/';

        if (!empty($archivos)) {
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($archivos as $archivo) {
                if (!$archivo->isValid() || $archivo->hasMoved()) {
                    continue;
                }

                // En Windows/WAMP, finfo_file() falla al leer archivos temporales de PHP.
                // Aplicamos doble filtro: MIME reportado por el cliente + whitelist de extensiones.
                $clientMime = $archivo->getClientMimeType();
                $clientExt  = strtolower($archivo->getClientExtension());
                $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!in_array($clientMime, $allowedMimes, true) || !in_array($clientExt, $allowedExt, true)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'errors'  => ['evidencias' => 'Tipo de archivo no permitido: ' . esc($archivo->getClientName())],
                    ]);
                }

                if ($archivo->getSizeByUnit('kb') > $maxSizeKb) {
                    return $this->response->setJSON([
                        'success' => false,
                        'errors'  => ['evidencias' => 'El archivo «' . esc($archivo->getClientName()) . '» excede el límite de 25 MB.'],
                    ]);
                }

                $newName = $archivo->getRandomName();
                $archivo->move($uploadPath, $newName);

                $evidencias[] = [
                    'nombre_original' => $archivo->getClientName(),
                    'ruta_archivo'    => $newName,
                    'tipo_archivo'    => $clientMime,
                    'peso_bytes'      => $archivo->getSize(),
                    'tipo_documento'  => 'Evidencia',
                ];
            }
        }

        // ── Transacción de base de datos ───────────────────────────────────────
        $db = \Config\Database::connect();
        $db->transStart();

        $dataDenuncia = [
            'folio'                      => 'PENDIENTE',
            'estatus'                    => 'Nueva',
            'tipo_persona'               => $this->request->getPost('tipo_persona'),
            'nombre_completo'            => $this->request->getPost('nombre_completo'),
            'genero'                     => $this->request->getPost('genero'),
            'estado'                     => $this->request->getPost('estado'),
            'municipio'                  => $this->request->getPost('municipio'),
            'colonia'                    => $this->request->getPost('colonia'),
            'codigo_postal'              => $this->request->getPost('codigo_postal'),
            'calle'                      => $this->request->getPost('calle'),
            'numero_exterior'            => $this->request->getPost('numero_exterior'),
            'numero_interior'            => $this->request->getPost('numero_interior') ?: null,
            'email'                      => $this->request->getPost('email'),
            'telefono'                   => $this->request->getPost('telefono'),
            'es_representante'           => $esRepresentante ? 1 : 0,
            'razon_social'               => $esRepresentante ? $this->request->getPost('razon_social')         : null,
            'nombre_representante'       => $esRepresentante ? $this->request->getPost('nombre_representante') : null,
            'tipo_denuncia'              => $this->request->getPost('tipo_denuncia'),
            'hechos_denunciados'         => $this->request->getPost('hechos_denunciados'),
            'latitud'                    => $this->request->getPost('latitud')  ?: null,
            'longitud'                   => $this->request->getPost('longitud') ?: null,
            'nombre_denunciado'          => $this->request->getPost('nombre_denunciado'),
            'denunciado_es_moral'        => $denunciadoEsMoral ? 1 : 0,
            'razon_social_denunciado'    => $denunciadoEsMoral ? $this->request->getPost('razon_social_denunciado') : null,
            'municipio_denunciado'       => $this->request->getPost('municipio_denunciado'),
            'colonia_denunciado'         => $this->request->getPost('colonia_denunciado'),
            'calle_denunciado'           => $this->request->getPost('calle_denunciado'),
            'codigo_postal_denunciado'   => $this->request->getPost('codigo_postal_denunciado'),
            'numero_exterior_denunciado' => $this->request->getPost('numero_exterior_denunciado'),
            'numero_interior_denunciado' => $this->request->getPost('numero_interior_denunciado') ?: null,
        ];

        $this->denunciasModel->skipValidation(true)->insert($dataDenuncia);
        $id = $db->insertID();

        $folio = 'LIVA-' . date('Y') . '-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
        $this->denunciasModel->update($id, ['folio' => $folio]);

        foreach ($evidencias as $evidencia) {
            $evidencia['id_denuncia'] = $id;
            $this->archivosDenunciasModel->insert($evidencia);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => ['general' => 'Error al registrar la denuncia. Por favor intente nuevamente.'],
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'folio'   => $folio,
        ]);
    }

    // ─── GET inicio/buscarReporte ──────────────────────────────────────────────
    public function buscarReporte(): \CodeIgniter\HTTP\ResponseInterface
    {
        $folio = strtoupper(trim((string) ($this->request->getGet('folio') ?? '')));

        // Solo permite caracteres válidos de folio (evita SQLi / path traversal)
        if ($folio === '' || !preg_match('/^[A-Z0-9\-]{1,30}$/', $folio)) {
            return $this->response->setJSON(['found' => false]);
        }

        $denuncia = $this->denunciasModel
            ->select('id_denuncia, folio, estatus, tipo_denuncia, fecha_captura, fecha_resolucion, notas_internas')
            ->where('folio', $folio)
            ->first();

        if (!$denuncia) {
            return $this->response->setJSON(['found' => false]);
        }

        $estatusMap = [
            'Nueva'         => ['text' => 'Recibida',    'icon' => 'inbox',        'class' => 'recibida'],
            'En Revisión'   => ['text' => 'En Revisión', 'icon' => 'pending',      'class' => 'en-revision'],
            'Investigación' => ['text' => 'Investigación',  'icon' => 'schedule',     'class' => 'investigacion'],
            'Resuelta'      => ['text' => 'Resuelta',    'icon' => 'check_circle', 'class' => 'resuelta'],
            'Desechada'     => ['text' => 'Desechada',   'icon' => 'cancel',       'class' => 'desechada'],
        ];

        $estatusInfo = $estatusMap[$denuncia['estatus']]
            ?? ['text' => $denuncia['estatus'], 'icon' => 'info', 'class' => 'recibida'];

        // Si el estado es Resuelto, obtener el documento de resolución
        $documentoResolucion = null;
        $estatusLower = mb_strtolower($denuncia['estatus'], 'UTF-8');
        
        // Log para depuración
        log_message('debug', "Buscando documento para denuncia {$denuncia['folio']} con estatus: '{$denuncia['estatus']}' (lower: '{$estatusLower}')");
        
        if ($estatusLower === 'resuelto' || $estatusLower === 'resuelta') {
            // Obtener el documento marcado como 'Resolución' (buscar ambas variantes por si acaso)
            $documento = $this->archivosDenunciasModel
                ->where('id_denuncia', $denuncia['id_denuncia'])
                ->groupStart()
                    ->where('tipo_documento', 'Resolución')
                    ->orWhere('tipo_documento', 'resolución')
                ->groupEnd()
                ->orderBy('id_evidencia', 'DESC')
                ->first();
            
            log_message('debug', "Documentos encontrados: " . ($documento ? json_encode($documento) : 'ninguno'));
            
            if ($documento) {
                $documentoResolucion = [
                    'id'             => $documento['id_evidencia'],
                    'nombre'         => $documento['nombre_original'],
                    'tipo'           => $documento['tipo_archivo'],
                    'peso'           => $documento['peso_bytes'],
                    'fecha_subida'   => $documento['fecha_subida'],
                ];
            }
        }

        return $this->response->setJSON([
            'found'               => true,
            'folio'               => $denuncia['folio'],
            'estatus'             => $estatusInfo,
            'tipo_denuncia'       => $denuncia['tipo_denuncia'],
            'notas_internas'      => $denuncia['notas_internas'],
            'fecha_captura'       => date('d \d\e F, Y', strtotime($denuncia['fecha_captura'])),
            'fecha_actualizacion' => $denuncia['fecha_resolucion']
                ? date('d \d\e F, Y', strtotime($denuncia['fecha_resolucion']))
                : date('d \d\e F, Y', strtotime($denuncia['fecha_captura'])),
            'documento_resolucion' => $documentoResolucion,
        ]);
    }

    // ─── GET inicio/descargarDocumentoResolucion/{id} ─────────────────────────
    public function descargarDocumentoResolucion($idEvidencia = null)
    {
        if (!$idEvidencia) {
            return $this->response->setStatusCode(400)
                ->setBody('ID de documento requerido');
        }

        // Obtener información del documento
        $evidencia = $this->archivosDenunciasModel->find($idEvidencia);
        
        if (!$evidencia) {
            return $this->response->setStatusCode(404)
                ->setBody('Documento no encontrado');
        }

        // El campo ruta_archivo solo contiene el nombre del archivo
        // Los documentos de resolución se almacenan en resoluciones/
        $nombreArchivo = $evidencia['ruta_archivo'];
        $rutaArchivo = WRITEPATH . 'uploads/resoluciones/' . $nombreArchivo;
        
        // Verificar que el archivo existe
        if (!file_exists($rutaArchivo)) {
            log_message('error', "Documento de resolución no encontrado: {$rutaArchivo}");
            return $this->response->setStatusCode(404)
                ->setBody('Archivo físico no encontrado');
        }

        // Verificar que el archivo es legible
        if (!is_readable($rutaArchivo)) {
            log_message('error', "Documento de resolución no es legible: {$rutaArchivo}");
            return $this->response->setStatusCode(403)
                ->setBody('El archivo no se puede leer');
        }

        // Determinar si es para descargar o visualizar
        $esDescarga = $this->request->getGet('download') === '1';
        $disposition = $esDescarga ? 'attachment' : 'inline';
        
        // Servir el archivo
        return $this->response
            ->setHeader('Content-Type', $evidencia['tipo_archivo'])
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $evidencia['nombre_original'] . '"')
            ->setHeader('Content-Length', (string) filesize($rutaArchivo))
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setHeader('Pragma', 'public')
            ->setBody(file_get_contents($rutaArchivo));
    }
}