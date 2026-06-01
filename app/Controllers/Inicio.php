<?php

namespace App\Controllers;

use App\Models\ArchivosDenunciasModel;
use App\Models\DenunciasModel;
use App\Models\CentroVerificacionModel;
use App\Models\TemaDenunciaModel;
use App\Models\TipoDenunciaModel;

class Inicio extends BaseController
{
    protected DenunciasModel $denunciasModel;
    protected ArchivosDenunciasModel $archivosDenunciasModel;
    protected CentroVerificacionModel $centroVerificacionModel;
    protected TemaDenunciaModel $temaDenunciaModel;
    protected TipoDenunciaModel $tipoDenunciaModel;

    public function __construct()
    {
        $this->denunciasModel        = new DenunciasModel();
        $this->archivosDenunciasModel = new ArchivosDenunciasModel();
        $this->centroVerificacionModel = new CentroVerificacionModel();
        $this->temaDenunciaModel = new TemaDenunciaModel();
        $this->tipoDenunciaModel = new TipoDenunciaModel();
    }

    public function index()
    {
        // Cargar tipos de denuncia activos
        $data['tiposDenuncia'] = $this->tipoDenunciaModel->where('activo', 1)->findAll();

        return view('inicio/header')
            . view('inicio/index', $data)
            . view('inicio/footer');
    }

    // ─── API: Obtener temas por tipo de denuncia ─────────────────────────────────────
    public function getTemasPorTipo(): \CodeIgniter\HTTP\ResponseInterface
    {
        $idTipo = $this->request->getGet('id_tipo');

        if (!$idTipo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de tipo de denuncia no proporcionado'
            ]);
        }

        $temas = $this->temaDenunciaModel
            ->where('id_tipo_denuncia', $idTipo)
            ->where('activo', 1)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $temas
        ]);
    }

    // ─── API: Obtener centros de verificación vehicular ──────────────────────────────
    public function getCentrosVerificacion(): \CodeIgniter\HTTP\ResponseInterface
    {
        $centros = $this->centroVerificacionModel
            ->where('activo', 1)
            ->orderBy('municipio', 'ASC')
            ->orderBy('clave', 'ASC')
            ->orderBy('direccion', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $centros
        ]);
    }

    // ─── Helper: Obtener nombre del tipo de denuncia ─────────────────────────────────
    private function obtenerNombreTipoDenuncia($idTipo): string
    {
        if (!$idTipo) {
            return '';
        }

        $tipo = $this->tipoDenunciaModel->find($idTipo);
        return $tipo ? $tipo['nombre'] : '';
    }

    // ─── OTP: Generar código seguro con random_bytes ─────────────────────────────────
    private function generarCodigoOTPSeguro(int $longitud = 6): string
    {
        $bytes = random_bytes($longitud);
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= ord($bytes[$i]) % 10;
        }
        return $codigo;
    }

    // ─── OTP: Hash del código para almacenamiento seguro ─────────────────────────────
    private function hashCodigo(string $codigo): string
    {
        return password_hash($codigo, PASSWORD_DEFAULT);
    }

    // ─── OTP: Verificar código contra hash almacenado ────────────────────────────────
    private function verificarCodigoHash(string $codigoIngresado, string $hashAlmacenado): bool
    {
        return password_verify($codigoIngresado, $hashAlmacenado);
    }

    // ─── OTP: Enviar código de verificación por email ────────────────────────────────
    private function enviarCodigoVerificacion(
        string $emailDestino,
        string $nombreCompleto,
        string $folio,
        string $codigo
    ): bool {
        $email = \Config\Services::email();

        $email->setFrom(
            getenv('email.fromEmail') ?: 'noreply@denuncias-ambientales.gob.mx',
            getenv('email.fromName') ?: 'Sistema de Denuncias Ambientales'
        );
        $email->setTo($emailDestino);
        $email->setSubject('Código de Verificación - Denuncia ' . $folio);

        $mensaje = view('emails/codigo_verificacion', [
            'nombre' => $nombreCompleto,
            'folio' => $folio,
            'codigo' => $codigo,
            'expiracion' => '30 minutos',
            'fecha' => date('d/m/Y H:i:s')
        ]);

        $email->setMessage($mensaje);

        if ($email->send()) {
            log_message('info', "Código OTP enviado a {$emailDestino} para folio {$folio}");
            return true;
        } else {
            log_message('error', "Error al enviar OTP a {$emailDestino}: " . $email->printDebugger(['headers']));
            return false;
        }
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
            'id_tipo_denuncia'           => ['label' => 'Tipo de Denuncia',      'rules' => 'required|numeric|is_not_unique[tipo_denuncia.id_tipo_denuncia]'],
            'id_tema_denuncia'           => ['label' => 'Tema de Denuncia',      'rules' => 'permit_empty|numeric|is_not_unique[tema_denuncia.id_tema_denuncia]'],
            'clave_cvv'                  => ['label' => 'Centro de Verificación', 'rules' => 'permit_empty|max_length[20]|is_not_unique[centros_verificacion_vehicular.clave]'],
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
        $archivosIdentificacion = $todosArchivos['identificacion'] ?? [];
        $allowedMimes  = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxSizeKb     = 25 * 1024; // 25 MB en KB
        $maxSizeKbIdentificacion = 10 * 1024; // 10 MB en KB
        $uploadPath    = WRITEPATH . 'uploads/evidencias/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Procesar archivos de identificación
        if (!empty($archivosIdentificacion)) {
            foreach ($archivosIdentificacion as $archivo) {
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
                        'errors'  => ['identificacion' => 'Tipo de archivo no permitido: ' . esc($archivo->getClientName())],
                    ]);
                }

                if ($archivo->getSizeByUnit('kb') > $maxSizeKbIdentificacion) {
                    return $this->response->setJSON([
                        'success' => false,
                        'errors'  => ['identificacion' => 'El archivo «' . esc($archivo->getClientName()) . '» excede el límite de 10 MB.'],
                    ]);
                }

                $newName = $archivo->getRandomName();
                $archivo->move($uploadPath, $newName);

                $evidencias[] = [
                    'nombre_original' => $archivo->getClientName(),
                    'ruta_archivo'    => $newName,
                    'tipo_archivo'    => $clientMime,
                    'peso_bytes'      => $archivo->getSize(),
                    'tipo_documento'  => 'Identificación',
                ];
            }
        }

        // Procesar archivos de evidencia
        if (!empty($archivos)) {
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

        // ── Generar código OTP con random_bytes ────────────────────────────────
        $codigoOTP = $this->generarCodigoOTPSeguro(6);
        $codigoHash = $this->hashCodigo($codigoOTP);
        $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // ── Transacción de base de datos ───────────────────────────────────────
        $db = \Config\Database::connect();
        $db->transStart();

        $dataDenuncia = [
            'folio'                      => 'PENDIENTE',
            'estatus'                    => 'Pendiente de Verificación',
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
            'id_tipo_denuncia'           => $this->request->getPost('id_tipo_denuncia') ?: null,
            'id_tema_denuncia'           => $this->request->getPost('id_tema_denuncia') ?: null,
            'tipo_denuncia'              => $this->obtenerNombreTipoDenuncia($this->request->getPost('id_tipo_denuncia')),
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
            'clave_cvv'                  => $this->request->getPost('clave_cvv') ?: null,
            'codigo_verificacion'        => $codigoHash,
            'codigo_verificacion_expira' => $expiracion,
            'intentos_verificacion'      => 0,
            'ultimo_envio_codigo'        => date('Y-m-d H:i:s'),
        ];

        $this->denunciasModel->skipValidation(true)->insert($dataDenuncia);
        $id = $db->insertID();

        $folio = 'SMADSOT.SAQDE-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT). '/'. date('Y');
        $this->denunciasModel->update($id, ['folio' => $folio]);

        // Log para debug: Verificar que el estatus se guardó correctamente
        $denunciaGuardada = $this->denunciasModel->find($id);
        log_message('info', "Denuncia {$folio} guardada con estatus: '{$denunciaGuardada['estatus']}'");
        log_message('debug', "Código OTP generado para {$folio} (hash): " . substr($codigoHash, 0, 20) . "...");

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

        // ── Enviar código de verificación por email ───────────────────────────
        $emailEnviado = $this->enviarCodigoVerificacion(
            $this->request->getPost('email'),
            $this->request->getPost('nombre_completo'),
            $folio,
            $codigoOTP
        );

        if (!$emailEnviado) {
            log_message('warning', "No se pudo enviar código OTP para folio {$folio}");
        }

        return $this->response->setJSON([
            'success' => true,
            'folio' => $folio,
            'necesita_verificacion' => true,
            'mensaje' => 'Denuncia registrada. Por favor verifica tu correo electrónico.'
        ]);
    }

    // ─── POST inicio/verificarCodigo ──────────────────────────────────────────────────
    public function verificarCodigoOTP(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }

        $folio = strtoupper(trim($this->request->getPost('folio') ?? ''));
        $codigoIngresado = trim($this->request->getPost('codigo') ?? '');

        // ── Validaciones básicas ──────────────────────────────────────────────
        if (empty($folio) || empty($codigoIngresado)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Folio y código son requeridos'
            ]);
        }

        if (!preg_match('/^\d{6}$/', $codigoIngresado)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El código debe ser de 6 dígitos'
            ]);
        }

        // ── Buscar denuncia ───────────────────────────────────────────────────
        log_message('info', "Intentando verificar código para folio: {$folio}");

        // Debug: Buscar primero sin filtro de estatus para ver qué hay
        $denunciaDebug = $this->denunciasModel->where('folio', $folio)->first();
        if ($denunciaDebug) {
            log_message('debug', "Denuncia encontrada - ID: {$denunciaDebug['id_denuncia']}, Estatus actual: '{$denunciaDebug['estatus']}'");
        } else {
            log_message('warning', "No existe ninguna denuncia con folio: {$folio}");
        }

        $denuncia = $this->denunciasModel
            ->where('folio', $folio)
            ->where('estatus', 'Pendiente de Verificación')
            ->first();

        if (!$denuncia) {
            log_message('warning', "Denuncia {$folio} no encontrada con estatus 'Pendiente de Verificación'. Estatus actual: " . ($denunciaDebug['estatus'] ?? 'N/A'));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada o ya verificada'
            ]);
        }

        log_message('info', "Denuncia {$folio} encontrada, verificando código...");

        // ── Verificar límite de intentos ──────────────────────────────────────
        if ($denuncia['intentos_verificacion'] >= 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Has excedido el número máximo de intentos. Solicita un nuevo código.',
                'codigo_bloqueado' => true
            ]);
        }

        // ── Verificar expiración ──────────────────────────────────────────────
        $ahora = new \DateTime();
        $expira = new \DateTime($denuncia['codigo_verificacion_expira']);

        if ($ahora > $expira) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El código ha expirado. Solicita uno nuevo.',
                'codigo_expirado' => true
            ]);
        }

        // ── Verificar código ──────────────────────────────────────────────────
        if (!$this->verificarCodigoHash($codigoIngresado, $denuncia['codigo_verificacion'])) {
            // Incrementar intentos fallidos
            $this->denunciasModel->update($denuncia['id_denuncia'], [
                'intentos_verificacion' => $denuncia['intentos_verificacion'] + 1
            ]);

            $intentosRestantes = 3 - ($denuncia['intentos_verificacion'] + 1);

            return $this->response->setJSON([
                'success' => false,
                'message' => "Código incorrecto. Te quedan {$intentosRestantes} intentos.",
                'intentos_restantes' => $intentosRestantes
            ]);
        }

        // ── ✅ Código correcto - Activar denuncia ─────────────────────────────
        $this->denunciasModel->update($denuncia['id_denuncia'], [
            'estatus' => 'Nueva',
            'verificado_en' => date('Y-m-d H:i:s'),
            'codigo_verificacion' => null,
            'codigo_verificacion_expira' => null,
            'intentos_verificacion' => 0
        ]);

        log_message('info', "Denuncia {$folio} verificada exitosamente");

        // GENERAR ACUSE Y ENVIAR EMAIL DE CONFIRMACIÓN
        try {
            $denunciaCompleta = $this->denunciasModel->find($denuncia['id_denuncia']);

            $rutaPDF = $this->generarAcusePDF($denunciaCompleta);

            $emailEnviado = $this->enviarEmailConfirmacion($denunciaCompleta, $rutaPDF);

            if (!$emailEnviado) {
                log_message('warning', "Email de confirmación no enviado para folio {$folio}");
            }
        } catch (\Exception $e) {
            log_message('error', "Error generando acuse para {$folio}: " . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => '¡Denuncia verificada exitosamente!',
            'folio' => $folio
        ]);
    }

    // ─── POST inicio/reenviarCodigo ───────────────────────────────────────────────────
    public function reenviarCodigoOTP(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }

        $folio = strtoupper(trim($this->request->getPost('folio') ?? ''));

        if (empty($folio)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Folio requerido'
            ]);
        }

        // ── Buscar denuncia ───────────────────────────────────────────────────
        $denuncia = $this->denunciasModel
            ->where('folio', $folio)
            ->where('estatus', 'Pendiente de Verificación')
            ->first();

        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada o ya verificada'
            ]);
        }

        // ── Rate limiting: máximo 1 reenvío cada 2 minutos ────────────────────
        if ($denuncia['ultimo_envio_codigo']) {
            $ultimoEnvio = new \DateTime($denuncia['ultimo_envio_codigo']);
            $ahora = new \DateTime();
            $diferencia = $ahora->getTimestamp() - $ultimoEnvio->getTimestamp();

            if ($diferencia < 120) { // 2 minutos
                $segundosRestantes = 120 - $diferencia;
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Espera {$segundosRestantes} segundos antes de solicitar otro código"
                ]);
            }
        }

        // ── Generar nuevo código ──────────────────────────────────────────────
        $nuevoCodigoOTP = $this->generarCodigoOTPSeguro(6);
        $nuevoCodigoHash = $this->hashCodigo($nuevoCodigoOTP);
        $nuevaExpiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // ── Actualizar en BD ──────────────────────────────────────────────────
        $this->denunciasModel->update($denuncia['id_denuncia'], [
            'codigo_verificacion' => $nuevoCodigoHash,
            'codigo_verificacion_expira' => $nuevaExpiracion,
            'intentos_verificacion' => 0,
            'ultimo_envio_codigo' => date('Y-m-d H:i:s')
        ]);

        // ── Enviar email ──────────────────────────────────────────────────────
        $emailEnviado = $this->enviarCodigoVerificacion(
            $denuncia['email'],
            $denuncia['nombre_completo'],
            $folio,
            $nuevoCodigoOTP
        );

        if (!$emailEnviado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al enviar el correo. Intenta nuevamente.'
            ]);
        }

        log_message('info', "Código OTP reenviado para folio {$folio}");

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Código reenviado exitosamente. Revisa tu correo.'
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
            'Pendiente de Verificación' => ['text' => 'Pendiente de Verificación', 'icon' => 'pending', 'class' => 'pendiente-verificacion'],
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

    private function generarAcusePDF(array $denuncia): string
    {
        // Crear directorio si no existe
        $dirAcuses = WRITEPATH . 'uploads/acuses/';
        if (!is_dir($dirAcuses)) {
            mkdir($dirAcuses, 0755, true);
        }

        // Preparar datos para la vista
        $fechaRecepcion = new \DateTime($denuncia['verificado_en'] ?? $denuncia['fecha_captura']);
        $fechaActual = new \DateTime();

        // Formatear fecha estilo: "01 de junio de 2026"
        $meses = [
            'enero',
            'febrero',
            'marzo',
            'abril',
            'mayo',
            'junio',
            'julio',
            'agosto',
            'septiembre',
            'octubre',
            'noviembre',
            'diciembre'
        ];
        $fechaActualTexto = $fechaActual->format('d') . ' de ' .
            $meses[(int)$fechaActual->format('m') - 1] . ' de ' .
            $fechaActual->format('Y');

        $data = [
            'folio' => $denuncia['folio'],
            'nombreCompleto' => $denuncia['nombre_completo'],
            'fechaActual' => $fechaActualTexto,
            'fechaRecepcion' => $fechaRecepcion->format('d/m/Y H:i:s'),
            'fechaRecepcionFormato' => $fechaRecepcion->format('d/m/Y'),
            'tipoDenuncia' => $denuncia['tipo_denuncia'] ?? 'No especificado',
            'municipioDenunciado' => $denuncia['municipio_denunciado'] ?? '',
        ];

        // Renderizar vista
        $html = view('pdf/acuse_denuncia', $data);

        // Crear directorio de caché de fuentes si no existe
        $fontCacheDir = WRITEPATH . 'cache/dompdf-fonts/';
        if (!is_dir($fontCacheDir)) {
            mkdir($fontCacheDir, 0755, true);
        }

        // Configurar DOMPDF 2.0
        $options = new \Dompdf\Options();
        $options->setIsRemoteEnabled(true); // Para cargar imágenes locales
        $options->setIsHtml5ParserEnabled(true);
        $options->setChroot(FCPATH); // Permitir acceso a archivos en public/
        $options->setFontCache($fontCacheDir); // Directorio con permisos de escritura
        $options->setFontDir($fontCacheDir); // Directorio adicional para fuentes

        $dompdf = new \Dompdf\Dompdf($options);
        $this->cargarFuentesMontserrat($dompdf);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        // Guardar PDF - Sanitizar nombre del folio para evitar problemas con caracteres especiales
        $folioSanitizado = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $denuncia['folio']);
        $nombreArchivo = 'acuse_' . $folioSanitizado . '_' . time() . '.pdf';
        $rutaCompleta = $dirAcuses . $nombreArchivo;

        file_put_contents($rutaCompleta, $dompdf->output());

        log_message('info', "Acuse PDF generado: {$rutaCompleta}");

        return $rutaCompleta;
    }
    private function enviarEmailConfirmacion(array $denuncia, string $rutaPDF): bool
    {
        $email = \Config\Services::email();

        $email->setFrom(
            getenv('email.fromEmail') ?: 'noreply@denuncias-ambientales.gob.mx',
            'Sistema de Denuncias Ambientales - SMAOT'
        );

        $email->setTo($denuncia['email']);
        $email->setSubject('Confirmación de Denuncia Ambiental - Folio ' . $denuncia['folio']);

        // Renderizar vista del email
        $mensaje = view('emails/confirmacion_denuncia', [
            'folio' => $denuncia['folio'],
            'nombreCompleto' => $denuncia['nombre_completo'],
            'tipoDenuncia' => $denuncia['tipo_denuncia'],
            'fechaRecepcion' => date('d/m/Y H:i:s', strtotime($denuncia['verificado_en'] ?? $denuncia['fecha_captura']))
        ]);

        $email->setMessage($mensaje);
        $email->setMailType('html');

        // Adjuntar PDF
        $email->attach($rutaPDF);

        if ($email->send()) {
            log_message('info', "Email de confirmación enviado a {$denuncia['email']} - Folio: {$denuncia['folio']}");
            return true;
        } else {
            log_message('error', "Error al enviar confirmación - Folio {$denuncia['folio']}: " . $email->printDebugger(['headers']));
            return false;
        }
    }

    // ─── MÉTODO DE PRUEBA: Generar PDF sin formulario ────────────────────────────────
    /**
     * Método de prueba para generar el PDF del acuse sin llenar el formulario
     * URL: http://localhost/denuncias-ambientales/inicio/testPDF
     * 
     * Genera datos de prueba y muestra el PDF directamente en el navegador
     */
    public function testPDF()
    {
        // Solo permitir en desarrollo
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)->setBody('No disponible en producción');
        }

        try {
            // Datos de prueba simulados
            $denunciaPrueba = [
                'folio' => 'LIVA-2026-TEST-' . rand(1000, 9999),
                'nombre_completo' => 'Juan Pérez García',
                'email' => 'prueba@ejemplo.com',
                'tipo_denuncia' => 'Contaminación del Agua',
                'municipio_denunciado' => 'San Andrés Cholula',
                'fecha_captura' => date('Y-m-d H:i:s'),
                'verificado_en' => date('Y-m-d H:i:s'),
            ];

            // Preparar datos para la vista (mismo formato que generarAcusePDF)
            $fechaRecepcion = new \DateTime($denunciaPrueba['verificado_en']);
            $fechaActual = new \DateTime();

            $meses = [
                'enero',
                'febrero',
                'marzo',
                'abril',
                'mayo',
                'junio',
                'julio',
                'agosto',
                'septiembre',
                'octubre',
                'noviembre',
                'diciembre'
            ];

            $fechaActualTexto = $fechaActual->format('d') . ' de ' .
                $meses[(int)$fechaActual->format('m') - 1] . ' de ' .
                $fechaActual->format('Y');

            $data = [
                'folio' => $denunciaPrueba['folio'],
                'nombreCompleto' => $denunciaPrueba['nombre_completo'],
                'fechaActual' => $fechaActualTexto,
                'fechaRecepcion' => $fechaRecepcion->format('d/m/Y H:i:s'),
                'fechaRecepcionFormato' => $fechaRecepcion->format('d/m/Y'),
                'tipoDenuncia' => $denunciaPrueba['tipo_denuncia'],
                'municipioDenunciado' => $denunciaPrueba['municipio_denunciado'],
            ];

            // Renderizar vista
            $html = view('pdf/acuse_denuncia', $data);

            // Crear directorio de caché de fuentes si no existe
            $fontCacheDir = WRITEPATH . 'cache/dompdf-fonts/';
            if (!is_dir($fontCacheDir)) {
                mkdir($fontCacheDir, 0755, true);
            }

            // Configurar DOMPDF 2.0
            $options = new \Dompdf\Options();
            $options->setIsRemoteEnabled(true);
            $options->setIsHtml5ParserEnabled(true);
            $options->setChroot(FCPATH);
            $options->setFontCache($fontCacheDir);
            $options->setFontDir($fontCacheDir);

            $dompdf = new \Dompdf\Dompdf($options);
            $this->cargarFuentesMontserrat($dompdf);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            // Mostrar PDF en el navegador (no descargar)
            $dompdf->stream('acuse_prueba_' . time() . '.pdf', [
                'Attachment' => false // false = mostrar en navegador
            ]);
        } catch (\Exception $e) {
            log_message('error', "Error en testPDF: " . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setBody('<h1>Error al generar PDF</h1><pre>' . esc($e->getMessage()) . '</pre>');
        }
    }

    // ─── MÉTODO DE PRUEBA: Ver solo el HTML del acuse ────────────────────────────────
    /**
     * Método de prueba para ver el HTML del acuse sin generar PDF
     * URL: http://localhost/denuncias-ambientales/inicio/testHTML
     * 
     * Útil para depurar el diseño y CSS antes de generar el PDF
     */
    public function testHTML()
    {
        // Solo permitir en desarrollo
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)->setBody('No disponible en producción');
        }

        // Datos de prueba
        $fechaActual = new \DateTime();
        $meses = [
            'enero',
            'febrero',
            'marzo',
            'abril',
            'mayo',
            'junio',
            'julio',
            'agosto',
            'septiembre',
            'octubre',
            'noviembre',
            'diciembre'
        ];

        $fechaActualTexto = $fechaActual->format('d') . ' de ' .
            $meses[(int)$fechaActual->format('m') - 1] . ' de ' .
            $fechaActual->format('Y');

        $data = [
            'folio' => 'LIVA-2026-TEST-' . rand(1000, 9999),
            'nombreCompleto' => 'María López Hernández',
            'fechaActual' => $fechaActualTexto,
            'fechaRecepcion' => date('d/m/Y H:i:s'),
            'fechaRecepcionFormato' => date('d/m/Y'),
            'tipoDenuncia' => 'Contaminación Atmosférica',
            'municipioDenunciado' => 'Puebla',
        ];

        // Renderizar y mostrar directamente el HTML
        return view('pdf/acuse_denuncia', $data);
    }

    // Agregar Monserrat a la generacion de PDF
    private function cargarFuentesMontserrat(\Dompdf\Dompdf $dompdf)
    {
        $fontDir = FCPATH . 'fonts/montserrat/';

        $fontMetrics = $dompdf->getFontMetrics();

        // Registrar Montserrat Regular
        if (file_exists($fontDir . 'Montserrat-Regular.ttf')) {
            $fontMetrics->registerFont(
                ['family' => 'Montserrat', 'style' => 'normal', 'weight' => 'normal'],
                $fontDir . 'Montserrat-Regular.ttf'
            );
        }

        // Registrar Montserrat Bold
        if (file_exists($fontDir . 'Montserrat-Bold.ttf')) {
            $fontMetrics->registerFont(
                ['family' => 'Montserrat', 'style' => 'normal', 'weight' => 'bold'],
                $fontDir . 'Montserrat-Bold.ttf'
            );
        }
    }
}
