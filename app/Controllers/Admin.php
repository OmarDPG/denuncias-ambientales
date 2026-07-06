<?php

namespace App\Controllers;

use App\Models\ArchivosDenunciasModel;
use App\Models\DenunciasModel;
use App\Models\AdminModel;
use App\Models\AreasModel;
use App\Models\MotivoVerificacionModel;
use App\Models\DocumentosDenunciaModel;
use App\Models\EstadosDenunciaModel;
use App\Models\HistorialDenunciasModel;
use App\Models\MotivosRechazoModel;
use App\Models\PermisosRolEstadoModel;
use App\Models\RolesModel;
use App\Models\TiposDocumentoModel;
use App\Models\TiposSancionModel;
use App\Models\TurnadosModel;



class Admin extends BaseController
{
    protected DenunciasModel $denunciasModel;
    protected ArchivosDenunciasModel $archivosDenunciasModel;
    protected AreasModel $areasModel;
    protected MotivoVerificacionModel $motivoVerificacionModel;
    protected DocumentosDenunciaModel $documentosDenunciaModel;
    protected EstadosDenunciaModel $estadosDenunciaModel;
    protected HistorialDenunciasModel $historialDenunciasModel;
    protected MotivosRechazoModel $motivosRechazoModel;
    protected PermisosRolEstadoModel $permisosRolEstadoModel;
    protected RolesModel $rolesModel;
    protected TiposDocumentoModel $tiposDocumentoModel;
    protected TiposSancionModel $tiposSancionModel;
    protected TurnadosModel $turnadosModel;

    /** Máximo de intentos fallidos antes del bloqueo temporal */
    private const MAX_INTENTOS     = 5;
    /** Duración del bloqueo en segundos (5 minutos) */
    private const BLOQUEO_SEGUNDOS = 300;
    /**
     * Hash ficticio para ejecutar siempre password_verify() y así
     * evitar timing attacks cuando el correo no existe en la BD.
     */
    private const DUMMY_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    protected $administrador, $session;
    public function __construct()
    {
        $this->denunciasModel = new DenunciasModel();
        $this->archivosDenunciasModel = new ArchivosDenunciasModel();
        $this->administrador = new AdminModel();
        $this->areasModel = new AreasModel();
        $this->motivoVerificacionModel = new MotivoVerificacionModel();
        $this->documentosDenunciaModel = new DocumentosDenunciaModel();
        $this->estadosDenunciaModel = new EstadosDenunciaModel();
        $this->historialDenunciasModel = new HistorialDenunciasModel();
        $this->motivosRechazoModel = new MotivosRechazoModel();
        $this->permisosRolEstadoModel = new PermisosRolEstadoModel();
        $this->rolesModel = new RolesModel();
        $this->tiposDocumentoModel = new TiposDocumentoModel();
        $this->tiposSancionModel = new TiposSancionModel();
        $this->turnadosModel = new TurnadosModel();
        $this->session = session();
    }

    // ─── Helper privado ────────────────────────────────────────────────────────
    // private function isAuthenticated(): bool
    // {
    //     return (bool) session()->get('admin_id');
    // }

    // ─── GET /administrador ────────────────────────────────────────────────────
    public function index()
    {
        $data = [
            'currentPage'      => 'login',
            'isAuthenticated'  => isset($this->session->id_adm)
        ];

        if ($data['isAuthenticated']) {
            $data['denuncias']   = $this->denunciasModel->findAll();
            $data['adminNombre'] = $this->session->get('usuario');
        }

        return view('admin/index', $data);
    }

    public function login()
    {
        function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if ($this->request->getMethod() == "post") {
            $usuarioAdm = test_input($this->request->getPost('email'));
            $password = test_input($this->request->getPost('password'));

            $datoAdministrador = $this->administrador->where('email', $usuarioAdm)->first();

            if ($datoAdministrador != null) {
                // Verificar contraseña
                if (password_verify($password, $datoAdministrador['password'])) {

                    // Cargar datos de rol y área
                    $rol = null;
                    $area = null;

                    if (!empty($datoAdministrador['id_rol'])) {
                        $rol = $this->rolesModel->find($datoAdministrador['id_rol']);
                    }

                    if (!empty($datoAdministrador['id_area'])) {
                        $area = $this->areasModel->find($datoAdministrador['id_area']);
                    }

                    // Preparar datos completos para sesión
                    $datosAdministrador = [
                        'id_adm' => $datoAdministrador['id_adm'],
                        'email' => $datoAdministrador['email'],
                        'usuario' => $datoAdministrador['usuario'],
                        'nombre' => $datoAdministrador['nombre'],
                        'apellidoP' => $datoAdministrador['apellidoP'],
                        'apellidoM' => $datoAdministrador['apellidoM'],
                        'expediente' => $datoAdministrador['expediente'],
                        'activo' => $datoAdministrador['activo'],
                        'adm' => $datoAdministrador['adm'],
                        'fecha_ultima' => $datoAdministrador['fecha_ultima'],
                        'c_acceso' => date('Y-m-d H:i:s'),
                        // Datos de rol
                        'id_rol' => $datoAdministrador['id_rol'] ?? null,
                        'codigo_rol' => $rol['codigo'] ?? 'USR_CONSULTA',
                        'nombre_rol' => $rol['nombre'] ?? 'Usuario Consulta',
                        'nivel_acceso' => $rol['nivel_acceso'] ?? 3,
                        // Datos de área
                        'id_area' => $datoAdministrador['id_area'] ?? null,
                        'nombre_area' => $area['nombre'] ?? null,
                    ];

                    $session = session();
                    $session->set($datosAdministrador);

                    // Actualizar fecha de último acceso
                    $this->administrador->update($session->id_adm, ['fecha_ultima' => $session->c_acceso]);

                    return redirect()->to(base_url() . 'admin/inicio');
                } else {
                    $data['error'] = "Contraseña errónea.";
                    echo view('admin/index', $data);
                }
            } else {
                $data['error'] = "El usuario no existe.";
                echo view('admin/index', $data);
            }
        } else {
            $data = ['validation' => $this->validator];
            echo view('admin/index', $data);
        }
    }

    // ─── GET /admin/inicio ─────────────────────────────────────────────────────
    public function inicio()
    {
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url() . 'admin');
        }

        $usuario = $this->obtenerDatosUsuario();
        $codigoRol = $usuario['codigo_rol'] ?? 'USR_CONSULTA';

        // Datos comunes para todas las vistas
        $data = [
            'currentPage' => 'inicio',
            'adminNombre' => $usuario['nombre_completo'],
            'rol' => $codigoRol,
            'nombreRol' => $this->session->nombre_rol ?? 'Usuario',
            'nombreArea' => $this->session->nombre_area ?? 'Sin asignar',
            'esAdmin' => $usuario['es_admin']
        ];

        // Cargar datos específicos según rol
        $datosPorRol = $this->obtenerDatosPorRol($codigoRol);
        $data = array_merge($data, $datosPorRol);

        // Calcular estadísticas generales (si el rol las necesita)
        if (in_array($codigoRol, ['ADM_GENERAL', 'ADM_DA', 'ADM'])) {
            $todasDenuncias = $this->denunciasModel->where('verificado_en IS NOT NULL', null, false)->findAll();
            $data['totalDenuncias'] = count($todasDenuncias);
            $data['casosResueltos'] = count(array_filter(
                $todasDenuncias,
                fn($d) =>
                isset($d['id_estado_actual']) && in_array($d['id_estado_actual'], [11, 20, 21, 22])
            ));
            $data['casosPendientes'] = count(array_filter(
                $todasDenuncias,
                fn($d) =>
                isset($d['id_estado_actual']) && !in_array($d['id_estado_actual'], [11, 20, 21, 22])
            ));
            $data['casosCriticos'] = count(array_filter(
                $todasDenuncias,
                fn($d) =>
                isset($d['prioridad']) && mb_strtoupper($d['prioridad']) === 'ALTA'
            ));
        }

        // Seleccionar vista según rol (fusionando ADM_GENERAL y ADM_DA)
        switch ($codigoRol) {
            case 'ADM_GENERAL':
            case 'ADM_DA':
            case 'ADM':
                $vistaDashboard = 'admin/dashboard_admin';
                break;

            case 'USR_DNS':
                $vistaDashboard = 'admin/dashboard_dns';
                break;

            case 'USR_DS':
                $vistaDashboard = 'admin/dashboard_ds';
                break;

            default:
                $vistaDashboard = 'admin/dashboard_consulta';
                break;
        };

        return view('admin/header', $data)
            . view($vistaDashboard, $data)
            . view('admin/footer');
    }

    // ─── GET /admin/denuncias-asignadas ───────────────────────────────────────
    public function denunciasAsignadas()
    {
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url() . 'admin');
        }

        // Verificar que el usuario tenga permisos de administrador
        $usuario = $this->obtenerDatosUsuario();
        $codigoRol = $usuario['codigo_rol'] ?? 'USR_CONSULTA';

        if (!in_array($codigoRol, ['ADM_GENERAL', 'ADM_DA', 'ADM'])) {
            return redirect()->to(base_url('admin/inicio'))
                ->with('error', 'No tiene permisos para acceder a esta sección');
        }

        // Datos comunes para el header
        $data = [
            'currentPage' => 'denuncias-asignadas',
            'adminNombre' => $usuario['nombre_completo'],
            'rol' => $codigoRol,
            'nombreRol' => $this->session->nombre_rol ?? 'Administrador',
            'nombreArea' => $this->session->nombre_area ?? 'Sin asignar',
            'esAdmin' => $usuario['es_admin']
        ];

        // Obtener denuncias que ya fueron asignadas/turnadas (no en estado RECIBIDA)
        $denunciasAsignadas = $this->denunciasModel
            ->where('id_estado_actual !=', 1) // Excluir estado RECIBIDA
            ->where('verificado_en IS NOT NULL', null, false)
            ->orderBy('fecha_ultimo_cambio_estado', 'DESC')
            ->paginate(15);

        $pager = $this->denunciasModel->pager;

        $data['denunciasAsignadas'] = $denunciasAsignadas;
        $data['totalAsignadas'] = $this->denunciasModel
            ->where('id_estado_actual !=', 1)
            ->where('verificado_en IS NOT NULL', null, false)
            ->countAllResults();
        $data['pager'] = $pager;

        return view('admin/header', $data)
            . view('admin/denuncias_asignadas', $data)
            . view('admin/footer');
    }

    // ─── GET /admin/usuarios ──────────────────────────────────────────────────
    public function usuarios()
    {
        if (! isset($this->session->id_adm)) {
            return redirect()->to(base_url('admin'));
        }

        // Validar que solo administradores accedan a gestión de usuarios
        $usuario = $this->obtenerDatosUsuario();
        $codigoRol = $usuario['codigo_rol'] ?? 'USR_CONSULTA';

        if (!in_array($codigoRol, ['ADM_GENERAL', 'ADM_DA', 'ADM'])) {
            $this->session->setFlashdata('error', 'No tiene permisos para acceder a esta sección');
            return redirect()->to(base_url('admin/inicio'));
        }

        $usuarios = $this->administrador->findAll();

        $data = [
            'currentPage'   => 'usuarios',
            'usuarios'      => $usuarios,
            'totalUsuarios' => count($usuarios),
        ];

        return view('admin/header', $data)
            . view('admin/usuarios', $data)
            . view('admin/footer');
    }

    // ─── GET /admin/archivo ───────────────────────────────────────────────────
    /**
     * Vista de archivo de denuncias resueltas
     * 
     * Política de acceso: Todos los roles autenticados pueden acceder (solo lectura)
     * No se requiere validación de rol específico ya que es consulta histórica
     */
    public function archivo()
    {
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url('admin'));
        }

        // Obtener todas las denuncias resueltas
        $todasDenuncias = $this->denunciasModel->findAll();
        $archivosDenuncias = $this->archivosDenunciasModel->findAll();

        // Filtrar solo denuncias con estado 'Resuelta'
        $denunciasResueltas = array_filter($todasDenuncias, function ($d) {
            return mb_strtolower($d['estatus'] ?? '', 'UTF-8') === 'resuelta';
        });

        // Reindexar y ordenar por fecha de resolución (más recientes primero)
        $denunciasResueltas = array_values($denunciasResueltas);
        usort($denunciasResueltas, function ($a, $b) {
            $fechaA = $a['fecha_resolucion'] ?? $a['fecha_actualizacion'] ?? '';
            $fechaB = $b['fecha_resolucion'] ?? $b['fecha_actualizacion'] ?? '';
            return strcmp($fechaB, $fechaA); // Orden descendente
        });

        $data = [
            'currentPage'        => 'archivo',
            'denuncias'          => $denunciasResueltas,
            'archivosDenuncias'  => $archivosDenuncias,
            'adminNombre'        => session()->get('nombre'),
            'totalArchivadas'    => count($denunciasResueltas),
        ];

        return view('admin/header', $data)
            . view('admin/archivo', $data)
            . view('admin/footer');
    }

    // ─── POST /admin/actualizarEstado ────────────────────────────────────────────
    public function actualizarEstado()
    {
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url() . 'admin');
        }

        $idDenuncia      = $this->request->getPost('id_denuncia');
        $nuevoEstatus    = $this->request->getPost('estatus');
        $notasInternas   = $this->request->getPost('notas_internas');
        $fechaResolucion = $this->request->getPost('fecha_resolucion') ?: null;

        $estatusValidos = ['En Revisión', 'Investigación', 'Desechada', 'Resuelta'];

        if (empty($idDenuncia) || !in_array($nuevoEstatus, $estatusValidos, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Datos inválidos']);
        }

        // Si el estado es Resuelta, validar que se adjuntó documentación
        if ($nuevoEstatus === 'Resuelta') {
            $archivos = $this->request->getFiles()['documentos_resolucion'] ?? [];
            if (empty($archivos) || !$archivos[0]->isValid()) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['error' => 'Debe adjuntar documentación de resolución']);
            }
        }

        $datos = ['estatus' => $nuevoEstatus];

        if (!empty($notasInternas)) {
            $datos['notas_internas'] = $notasInternas;
        }

        if (!empty($fechaResolucion)) {
            $datos['fecha_resolucion'] = $fechaResolucion;
        }

        // Procesar archivos de resolución si el estado es Resuelta
        $documentosResolucion = [];
        if ($nuevoEstatus === 'Resuelta') {
            $archivos = $this->request->getFiles()['documentos_resolucion'] ?? [];
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
            $maxSizeKb = 25 * 1024; // 25 MB
            $uploadPath = WRITEPATH . 'uploads/resoluciones/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($archivos as $archivo) {
                if (!$archivo->isValid() || $archivo->hasMoved()) {
                    continue;
                }

                $clientMime = $archivo->getClientMimeType();
                $clientExt = strtolower($archivo->getClientExtension());
                $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!in_array($clientMime, $allowedMimes, true) || !in_array($clientExt, $allowedExt, true)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'error' => 'Tipo de archivo no permitido: ' . esc($archivo->getClientName())
                    ]);
                }

                if ($archivo->getSizeByUnit('kb') > $maxSizeKb) {
                    return $this->response->setJSON([
                        'success' => false,
                        'error' => 'El archivo «' . esc($archivo->getClientName()) . '» excede el límite de 25 MB.'
                    ]);
                }

                $newName = $archivo->getRandomName();
                $archivo->move($uploadPath, $newName);

                $documentosResolucion[] = [
                    'id_denuncia' => $idDenuncia,
                    'nombre_original' => $archivo->getClientName(),
                    'ruta_archivo' => $newName,
                    'tipo_archivo' => $clientMime,
                    'peso_bytes' => $archivo->getSize(),
                    'tipo_documento' => 'Resolución',
                ];
            }
        }

        $actualizado = $this->denunciasModel->update((int) $idDenuncia, $datos);

        // Guardar documentos de resolución
        if ($actualizado && !empty($documentosResolucion)) {
            foreach ($documentosResolucion as $doc) {
                $this->archivosDenunciasModel->insert($doc);
            }
        }

        if ($actualizado) {
            return $this->response->setJSON([
                'success'     => true,
                'mensaje'     => 'Estado actualizado correctamente',
                'id_denuncia' => $idDenuncia,
                'estatus'     => $nuevoEstatus,
            ]);
        }

        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'No se pudo actualizar el registro']);
    }

    // ─── POST /admin/crearUsuario ─────────────────────────────────────────────
    public function crearUsuario()
    {
        if (!isset($this->session->id_adm) || !$this->session->get('adm')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'No autorizado']);
        }

        $rules = [
            'nombre'           => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[admin.email]',
            'expediente'       => 'required|exact_length[18]|alpha_numeric',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'adm'              => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Datos inválidos', 'validation' => $this->validator->getErrors()]);
        }

        $nombre = $this->request->getPost('nombre');
        $partes = explode(' ', trim($nombre), 3);

        $datos = [
            'email'      => $this->request->getPost('email'),
            'usuario'    => explode('@', $this->request->getPost('email'))[0],
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nombre'     => $partes[0] ?? '',
            'apellidoP'  => $partes[1] ?? '',
            'apellidoM'  => $partes[2] ?? '',
            'expediente' => strtoupper($this->request->getPost('expediente')),
            'adm'        => (int) $this->request->getPost('adm'),
            'activo'     => 1,
            'fecha_alta' => date('Y-m-d H:i:s')
        ];

        if ($this->administrador->insert($datos)) {
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Usuario creado exitosamente'
            ]);
        }

        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'No se pudo crear el usuario']);
    }

    // ─── GET /admin/obtenerUsuario/{id} ───────────────────────────────────────
    public function obtenerUsuario($id = null)
    {
        if (!isset($this->session->id_adm)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'No autorizado']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'ID requerido']);
        }

        $usuario = $this->administrador->find($id);

        if (!$usuario) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Usuario no encontrado']);
        }

        // No enviar el password
        unset($usuario['password']);

        return $this->response->setJSON([
            'success' => true,
            'usuario' => $usuario
        ]);
    }

    // ─── POST /admin/actualizarUsuario ────────────────────────────────────────
    public function actualizarUsuario()
    {
        if (!isset($this->session->id_adm) || !$this->session->get('adm')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id_adm');

        if (!$id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'ID requerido']);
        }

        $rules = [
            'nombre'     => 'required|min_length[3]|max_length[100]',
            'email'      => "required|valid_email|is_unique[admin.email,id_adm,{$id}]",
            'expediente' => 'required|exact_length[18]|alpha_numeric',
            'adm'        => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Datos inválidos', 'validation' => $this->validator->getErrors()]);
        }

        $nombre = $this->request->getPost('nombre');
        $partes = explode(' ', trim($nombre), 3);

        $datos = [
            'email'      => $this->request->getPost('email'),
            'usuario'    => explode('@', $this->request->getPost('email'))[0],
            'nombre'     => $partes[0] ?? '',
            'apellidoP'  => $partes[1] ?? '',
            'apellidoM'  => $partes[2] ?? '',
            'expediente' => strtoupper($this->request->getPost('expediente')),
            'adm'        => (int) $this->request->getPost('adm')
        ];

        if ($this->administrador->update($id, $datos)) {
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Usuario actualizado exitosamente'
            ]);
        }

        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'No se pudo actualizar el usuario']);
    }

    // ─── POST /admin/cambiarEstadoUsuario ─────────────────────────────────────
    public function cambiarEstadoUsuario()
    {
        if (!isset($this->session->id_adm) || !$this->session->get('adm')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'No autorizado']);
        }

        $id = $this->request->getPost('id_adm');
        $activo = $this->request->getPost('activo');

        if (!$id || !in_array($activo, ['0', '1'], true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Datos inválidos']);
        }

        if ($this->administrador->update($id, ['activo' => (int) $activo])) {
            $accion = $activo === '1' ? 'activado' : 'desactivado';
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => "Usuario {$accion} exitosamente"
            ]);
        }

        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'No se pudo cambiar el estado del usuario']);
    }

    // ─── POST /admin/restablecerPassword ──────────────────────────────────────
    public function restablecerPassword()
    {
        if (!isset($this->session->id_adm) || !$this->session->get('adm')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'No autorizado']);
        }

        $rules = [
            'id_adm'               => 'required|numeric',
            'new_password'         => 'required|min_length[8]',
            'new_password_confirm' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Datos inválidos', 'validation' => $this->validator->getErrors()]);
        }

        $id = $this->request->getPost('id_adm');
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);

        if ($this->administrador->update($id, ['password' => $newPassword])) {
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Contraseña restablecida exitosamente'
            ]);
        }

        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'No se pudo restablecer la contraseña']);
    }

    // ─── GET /admin/verEvidencia/{id} ─────────────────────────────────────────
    public function verEvidencia($idEvidencia = null)
    {
        // Verificar autenticación
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url('admin'))
                ->with('error', 'Debe iniciar sesión para acceder a este recurso');
        }

        if (!$idEvidencia) {
            return $this->response->setStatusCode(400)
                ->setBody('ID de evidencia requerido');
        }

        // Obtener información de la evidencia
        $evidencia = $this->archivosDenunciasModel->find($idEvidencia);

        if (!$evidencia) {
            return $this->response->setStatusCode(404)
                ->setBody('Evidencia no encontrada en la base de datos');
        }

        // Construir ruta completa del archivo según el tipo de documento
        $nombreArchivo = basename($evidencia['ruta_archivo']);

        // Determinar la carpeta según el tipo de documento (comparación case-insensitive)
        if (isset($evidencia['tipo_documento']) && strcasecmp($evidencia['tipo_documento'], 'Resolución') === 0) {
            $rutaArchivo = WRITEPATH . 'uploads/resoluciones/' . $nombreArchivo;
        } else {
            $rutaArchivo = WRITEPATH . 'uploads/evidencias/' . $nombreArchivo;
        }

        // Verificar que el archivo existe físicamente
        if (!file_exists($rutaArchivo)) {
            log_message('error', "Archivo de evidencia no encontrado: {$rutaArchivo}");
            return $this->response->setStatusCode(404)
                ->setBody('Archivo físico no encontrado');
        }

        // Verificar que el archivo es legible
        if (!is_readable($rutaArchivo)) {
            log_message('error', "Archivo de evidencia no es legible: {$rutaArchivo}");
            return $this->response->setStatusCode(403)
                ->setBody('El archivo no se puede leer');
        }

        // Determinar si es para descargar o visualizar
        $esDescarga = $this->request->getGet('download') === '1';

        // Configurar headers según el tipo de visualización
        $disposition = $esDescarga ? 'attachment' : 'inline';

        // Servir el archivo de forma segura
        return $this->response
            ->setHeader('Content-Type', $evidencia['tipo_archivo'])
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $evidencia['nombre_original'] . '"')
            ->setHeader('Content-Length', (string) filesize($rutaArchivo))
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setBody(file_get_contents($rutaArchivo));
    }

    // ─── GET /administrador/logout ─────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin');
    }

    // ════════════════════════════════════════════════════════════════════════════
    // MÉTODOS HELPER PRIVADOS - FASE 1
    // ════════════════════════════════════════════════════════════════════════════

    /**
     * Obtiene datos estructurados del usuario en sesión
     * 
     * @return array|null Array con datos de usuario o null si no hay sesión
     */
    private function obtenerDatosUsuario(): ?array
    {
        if (!isset($this->session->id_adm)) {
            return null;
        }

        return [
            'id_adm' => $this->session->id_adm,
            'id_rol' => $this->session->id_rol,
            'codigo_rol' => $this->session->codigo_rol ?? 'USR_CONSULTA',
            'nivel_acceso' => $this->session->nivel_acceso ?? 3,
            'id_area' => $this->session->id_area,
            'nombre_completo' => trim(
                ($this->session->nombre ?? '') . ' ' .
                    ($this->session->apellidoP ?? '') . ' ' .
                    ($this->session->apellidoM ?? '')
            ),
            'email' => $this->session->email,
            'es_admin' => (bool) $this->session->adm,
        ];
    }

    /**
     * Valida si un rol puede realizar una transición de estado
     * 
     * @param int $idRol ID del rol
     * @param int|null $idEstadoOrigen ID del estado origen (null = cualquiera)
     * @param int $idEstadoDestino ID del estado destino
     * @return bool True si tiene permiso
     */
    private function validarPermisoTransicion(int $idRol, ?int $idEstadoOrigen, int $idEstadoDestino): bool
    {
        $permiso = $this->permisosRolEstadoModel
            ->where('id_rol', $idRol)
            ->where('id_estado_destino', $idEstadoDestino)
            ->groupStart()
            ->where('id_estado_origen', $idEstadoOrigen)
            ->orWhere('id_estado_origen', null)
            ->groupEnd()
            ->where('puede_transicionar', 1)
            ->first();

        return $permiso !== null;
    }

    /**
     * Valida si un estado requiere documento y si se adjuntó
     * 
     * @param int $idEstado ID del estado
     * @param array|null $archivos Archivos subidos
     * @return array ['valido' => bool, 'mensaje' => string]
     */
    private function validarDocumentoRequerido(int $idEstado, ?array $archivos): array
    {
        $estado = $this->estadosDenunciaModel->find($idEstado);

        if (!$estado) {
            return ['valido' => false, 'mensaje' => 'Estado no encontrado'];
        }

        if ($estado['requiere_documento']) {
            if (empty($archivos) || !isset($archivos[0]) || !$archivos[0]->isValid()) {
                return [
                    'valido' => false,
                    'mensaje' => 'Este estado requiere adjuntar un documento oficial'
                ];
            }
        }

        return ['valido' => true, 'mensaje' => 'OK'];
    }

    /**
     * Valida que el usuario pertenece al área responsable de la denuncia
     * 
     * @param int $idUsuario ID del usuario
     * @param int $idDenuncia ID de la denuncia
     * @return bool True si es del área responsable o es admin general
     */
    private function validarAreaResponsable(int $idUsuario, int $idDenuncia): bool
    {
        $usuario = $this->obtenerDatosUsuario();

        // Admin general puede todo
        if ($usuario && $usuario['es_admin'] && $usuario['nivel_acceso'] == 1) {
            return true;
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);

        if (!$denuncia) {
            return false;
        }

        // Validar que el área del usuario coincide con el área responsable de la denuncia
        return $usuario && $usuario['id_area'] == $denuncia['id_area_responsable'];
    }

    /**
     * Calcula hash SHA-256 de un archivo
     * 
     * @param object $archivo Archivo subido
     * @return string Hash SHA-256
     */
    private function calcularHashArchivo($archivo): string
    {
        return hash_file('sha256', $archivo->getTempName());
    }

    /**
     * Genera nombre único para archivo
     * 
     * @param string $nombreOriginal Nombre original del archivo
     * @return string Nombre único con extensión
     */
    private function generarNombreUnico(string $nombreOriginal): string
    {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = bin2hex(random_bytes(8));

        return $timestamp . '_' . $random . '.' . $extension;
    }

    /**
     * Obtiene ruta de subida según tipo de documento
     * 
     * @param string $codigoTipo Código del tipo de documento
     * @return string Ruta absoluta
     */
    private function obtenerRutaSubida(string $codigoTipo): string
    {
        $rutas = [
            'TURNADO' => 'turnados',
            'ACTA_INSPECCION' => 'inspecciones',
            'ACTA_SANCION' => 'sanciones',
            'IDENTIFICACION' => 'identificaciones',
            'EVIDENCIA_DENUNCIA' => 'evidencias',
            'EVIDENCIA_INSPECCION' => 'evidencias',
            'NOTIFICACION' => 'notificaciones',
            'RESOLUCION' => 'resoluciones',
            'APROBACION_INSPECCION' => 'dictamenes',
            'OFICIO_RECHAZO' => 'rechazos',
            'JUSTIFICACION_FLUJO' => 'justificaciones',
        ];

        $carpeta = $rutas[$codigoTipo] ?? 'evidencias';

        return WRITEPATH . 'uploads/' . $carpeta . '/';
    }

    /**
     * Registra una acción en el historial de denuncias
     * 
     * @param int $idDenuncia ID de la denuncia
     * @param string $accion Tipo de acción
     * @param string|null $estadoAnterior Estado anterior
     * @param string|null $estadoNuevo Estado nuevo
     * @param int|null $idAreaOrigen ID del área origen
     * @param int|null $idAreaDestino ID del área destino
     * @param string|null $observaciones Observaciones
     * @return bool True si se registró correctamente
     */
    private function registrarHistorial(
        int $idDenuncia,
        string $accion,
        ?string $estadoAnterior = null,
        ?string $estadoNuevo = null,
        ?int $idAreaOrigen = null,
        ?int $idAreaDestino = null,
        ?string $observaciones = null
    ): bool {
        $usuario = $this->obtenerDatosUsuario();

        if (!$usuario) {
            return false;
        }

        $datos = [
            'id_denuncia' => $idDenuncia,
            'id_usuario' => $usuario['id_adm'],
            'id_area_origen' => $idAreaOrigen,
            'id_area_destino' => $idAreaDestino,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'accion' => $accion,
            'observaciones' => $observaciones,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'fecha_accion' => date('Y-m-d H:i:s')
        ];

        return $this->historialDenunciasModel->insert($datos) !== false;
    }

    /**
     * Obtiene denuncias filtradas según el rol del usuario
     * 
     * @param string $codigoRol Código del rol (ADM, USR_DNS, USR_DS, etc)
     * @return array Datos específicos para el dashboard del rol
     */
    private function obtenerDatosPorRol(string $codigoRol): array
    {
        $usuario = $this->obtenerDatosUsuario();

        switch ($codigoRol) {
            case 'ADM_GENERAL':
            case 'ADM_DA':
            case 'ADM':
                // Administradores ven denuncias recibidas (sin paginar, pocas) y todas (paginadas)
                $denunciasRecibidas = $this->denunciasModel
                    ->where('id_estado_actual', 1) // RECIBIDA
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                $totalRecibidas = count($denunciasRecibidas);

                // Solo paginamos la tabla principal
                $todasDenuncias = $this->denunciasModel
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->paginate(10);
                $pager = $this->denunciasModel->pager;

                return [
                    'denunciasRecibidas' => $denunciasRecibidas,
                    'totalRecibidas' => $totalRecibidas,
                    'todasDenuncias' => $todasDenuncias,
                    'pager' => $pager
                ];

            case 'USR_DNS':
                // DNS ve denuncias en estados DNS - Solo pagina tabla principal
                $denunciasTurnadas = $this->denunciasModel
                    ->whereIn('id_estado_actual', [2]) // TURNADA_DNS
                    ->where('id_area_responsable', 2) // Área DNS
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->paginate(10);
                $pager = $this->denunciasModel->pager;

                // Resto sin paginar (generalmente pocas)
                $misRevisiones = $this->denunciasModel
                    ->whereIn('id_estado_actual', [3, 4]) // EN_REVISION_DNS, APROBADA_INSPECCION
                    ->where('id_usuario_asignado', $usuario['id_adm'])
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                $regresadasDS = $this->denunciasModel
                    ->whereIn('id_estado_actual', [8]) // REGRESADA_DNS
                    ->where('id_area_responsable', 2)
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                $enSancion = $this->denunciasModel
                    ->whereIn('id_estado_actual', [9, 10]) // EN_ELABORACION_SANCION, SANCIONADA
                    // ->where('id_usuario_asignado', $usuario['id_adm'])
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                // Cargar motivos de rechazo para Flujo B
                $motivosRechazo = $this->motivosRechazoModel
                    ->where('activo', 1)
                    ->orderBy('codigo', 'ASC')
                    ->findAll();

                return [
                    'denunciasTurnadas' => $denunciasTurnadas,
                    'pager' => $pager,
                    'misRevisiones' => $misRevisiones,
                    'regresadasDS' => $regresadasDS,
                    'enSancion' => $enSancion,
                    'motivosRechazo' => $motivosRechazo
                ];

            case 'USR_DS':
                // DS ve denuncias en estados DS - Solo pagina tabla principal
                $denunciasTurnadas = $this->denunciasModel
                    ->whereIn('id_estado_actual', [5]) // TURNADA_DS
                    ->where('id_area_responsable', 1) // Área DS
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->paginate(10);
                $pager = $this->denunciasModel->pager;

                // Inspecciones sin paginar (generalmente pocas por usuario)
                $inspeccionesActivas = $this->denunciasModel
                    ->where('id_estado_actual', 6) // EN_INSPECCION
                    ->where('id_usuario_asignado', $usuario['id_adm'])
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                $inspeccionesConcluidas = $this->denunciasModel
                    ->where('id_estado_actual', 7) // INSPECCION_CONCLUIDA
                    ->where('id_usuario_asignado', $usuario['id_adm'])
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->findAll();

                return [
                    'denunciasTurnadas' => $denunciasTurnadas,
                    'pager' => $pager,
                    'inspeccionesActivas' => $inspeccionesActivas,
                    'inspeccionesConcluidas' => $inspeccionesConcluidas
                ];

            default:
                // Usuario consulta: todas las denuncias en solo lectura
                $todasDenuncias = $this->denunciasModel
                    ->where('verificado_en IS NOT NULL', null, false)
                    ->orderBy('verificado_en', 'DESC')
                    ->paginate(10);
                $pager = $this->denunciasModel->pager;

                return [
                    'todasDenuncias' => $todasDenuncias,
                    'pager' => $pager
                ];
        }
    }

    // ========================================================================
    // MÉTODOS DE TRANSICIÓN DE ESTADOS - FASE 2
    // ========================================================================

    /**
     * Turnar denuncia entre departamentos
     * POST /admin/turnarDenuncia
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function turnarDenuncia()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $idAreaDestino = $this->request->getPost('id_area_destino');
        $observaciones = $this->request->getPost('observaciones');
        $razonFlujoExcepcional = $this->request->getPost('razon_flujo_excepcional');
        $archivo = $this->request->getFile('archivo');

        // Validar campos requeridos
        if (!$idDenuncia || !$idAreaDestino) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ]);
        }

        // Obtener denuncia
        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar permisos del rol para turnar
        $estadoActual = $denuncia['id_estado_actual'];
        $estadoDestino = ($idAreaDestino == 2) ? 2 : 5; // TURNADA_DNS o TURNADA_DS

        // FLUJO A: Detectar turnado directo excepcional (RECIBIDA → TURNADA_DS)
        $esFlujoExcepcional = ($estadoActual == 1 && $estadoDestino == 5); // RECIBIDA → TURNADA_DS

        if ($esFlujoExcepcional) {
            // Validar justificación obligatoria para flujo excepcional
            if (!$razonFlujoExcepcional || strlen(trim($razonFlujoExcepcional)) < 50) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El turnado directo a Supervisión requiere justificación de al menos 50 caracteres explicando por qué omite la revisión normativa.'
                ]);
            }

            // Documento obligatorio para flujo excepcional
            if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El turnado directo excepcional requiere documento de justificación obligatorio.'
                ]);
            }
        }

        if (!$this->validarPermisoTransicion($usuario['id_rol'], $estadoActual, $estadoDestino)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para realizar esta transición'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Actualizar denuncia
            $datosActualizacion = [
                'id_estado_actual' => $estadoDestino,
                'id_area_responsable' => $idAreaDestino,
                'id_usuario_asignado' => null, // Sin asignar aún
                'fecha_turnado' => date('Y-m-d H:i:s'),
                'fecha_ultimo_cambio_estado' => date('Y-m-d H:i:s')
            ];

            // Marcar flujo excepcional si aplica (Flujo A)
            if ($esFlujoExcepcional) {
                $datosActualizacion['flujo_excepcional'] = 1;
                $datosActualizacion['razon_flujo_excepcional'] = $razonFlujoExcepcional;
                $datosActualizacion['fecha_flujo_excepcional'] = date('Y-m-d H:i:s');
            }

            $this->denunciasModel->update($idDenuncia, $datosActualizacion);

            // Procesar archivo (obligatorio para flujo excepcional, opcional para normal)
            $idDocumento = null;
            if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
                // Calcular hash ANTES de mover el archivo
                $hashArchivo = $this->calcularHashArchivo($archivo);
                $nombreOriginal = $archivo->getName();
                $tamañoBytes = $archivo->getSize();
                $mimeType = $archivo->getMimeType();

                $nombreUnico = $this->generarNombreUnico($nombreOriginal);
                $rutaDestino = $this->obtenerRutaSubida($esFlujoExcepcional ? 'JUSTIFICACION_FLUJO' : 'TURNADO');

                // Mover archivo
                $archivo->move($rutaDestino, $nombreUnico);

                // Registrar documento
                $idTipoDoc = $esFlujoExcepcional ? 10 : 1; // JUSTIFICACION_FLUJO o TURNADO
                $datosDoc = [
                    'id_denuncia' => $idDenuncia,
                    'id_tipo_documento' => $idTipoDoc,
                    'nombre_original' => $nombreOriginal,
                    'nombre_almacenado' => $nombreUnico,
                    'ruta_archivo' => $rutaDestino . $nombreUnico,
                    'hash_sha256' => $hashArchivo,
                    'peso_bytes' => $tamañoBytes,
                    'tipo_mime' => $mimeType,
                    'id_usuario_subida' => $usuario['id_adm'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);
            }

            // Registrar en historial
            $estadoAnterior = $this->estadosDenunciaModel->find($estadoActual)['nombre_estado'] ?? null;
            $estadoNuevo = $this->estadosDenunciaModel->find($estadoDestino)['nombre_estado'] ?? null;

            $accionHistorial = $esFlujoExcepcional ? 'TURNADO_DIRECTO_EXCEPCIONAL' : 'TURNADO';
            $observacionesHistorial = $esFlujoExcepcional
                ? "FLUJO EXCEPCIONAL - " . $razonFlujoExcepcional . ($observaciones ? " | Observaciones: " . $observaciones : "")
                : $observaciones;

            $this->registrarHistorial(
                $idDenuncia,
                $accionHistorial,
                $estadoAnterior,
                $estadoNuevo,
                $usuario['id_area'],
                $idAreaDestino,
                $observacionesHistorial
            );

            // Registrar turnado
            $this->turnadosModel->insert([
                'id_denuncia' => $idDenuncia,
                'id_area_origen' => $usuario['id_area'],
                'id_area_destino' => $idAreaDestino,
                'id_usuario_turna' => $usuario['id_adm'],
                'observaciones' => $observaciones,
                'id_documento' => $idDocumento,
                'fecha_turnado' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al turnar la denuncia'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Denuncia turnada correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'area_destino' => $this->areasModel->find($idAreaDestino)['nombre_area'] ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al turnar denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor al turnar denuncia'
            ]);
        }
    }

    /**
     * Tomar caso (asignar denuncia al usuario actual)
     * POST /admin/tomarCaso
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function tomarCaso()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');

        if (!$idDenuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falta el ID de denuncia'
            ]);
        }

        // Obtener denuncia
        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar que la denuncia esté en el área del usuario
        if (!$this->validarAreaResponsable($usuario['id_adm'], $idDenuncia)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta denuncia no pertenece a su área'
            ]);
        }

        // Validar que no esté ya asignada
        if ($denuncia['id_usuario_asignado']) {
            $usuarioAsignado = $this->administrador->find($denuncia['id_usuario_asignado']);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este caso ya está asignado a ' . ($usuarioAsignado['nombre'] ?? 'otro usuario')
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $estadoActual = $denuncia['id_estado_actual'];
            $estadoNuevo = null;

            // Determinar nuevo estado según área
            if ($denuncia['id_area_responsable'] == 2) {
                // DNS: TURNADA_DNS -> EN_REVISION_DNS
                $estadoNuevo = 3;
            } elseif ($denuncia['id_area_responsable'] == 1) {
                // DS: TURNADA_DS -> EN_INSPECCION
                $estadoNuevo = 6;
            }

            // Actualizar denuncia
            $this->denunciasModel->update($idDenuncia, [
                'id_usuario_asignado' => $usuario['id_adm'],
                'id_estado_actual' => $estadoNuevo ?? $estadoActual,
                'fecha_asignacion' => date('Y-m-d H:i:s')
            ]);

            // Registrar en historial
            $estadoAnteriorNombre = $this->estadosDenunciaModel->find($estadoActual)['nombre_estado'] ?? null;
            $estadoNuevoNombre = $estadoNuevo ? $this->estadosDenunciaModel->find($estadoNuevo)['nombre_estado'] : null;

            $this->registrarHistorial(
                $idDenuncia,
                'ASIGNACION',
                $estadoAnteriorNombre,
                $estadoNuevoNombre,
                null,
                null,
                'Usuario tomó el caso para revisión'
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al tomar el caso'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Caso asignado correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al tomar caso: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor al tomar caso'
            ]);
        }
    }

    /**
     * Obtener detalle completo de una denuncia para modal
     * GET /admin/obtenerDenunciaDetalle/{id}
     * 
     * @param int|string|null $id ID de la denuncia
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function obtenerDenunciaDetalle($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        // Validar que el ID no sea null, 'null', 'undefined', vacío, etc.
        if ($id === null || $id === 'null' || $id === 'undefined' || trim((string)$id) === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de denuncia no proporcionado'
            ]);
        }

        // Convertir ID a entero y validar
        $id = (int) $id;
        if ($id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de denuncia inválido'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        try {
            // Obtener denuncia con TODAS las relaciones
            $denuncia = $this->denunciasModel
                ->select('denuncias.*, 
                         estados_denuncia.nombre_estado,
                         estados_denuncia.codigo as codigo_estado,
                         areas.nombre_area,
                         admin.nombre as nombre_responsable,
                         admin.apellidoP as apellidoP_responsable,
                         admin.apellidoM as apellidoM_responsable,
                         tipo_denuncia.nombre as nombre_tipo_denuncia,
                         tema_denuncia.nombre as nombre_tema_denuncia,
                         cvv.clave as clave_cvv_detalle,
                         cvv.municipio as municipio_cvv,
                         cvv.direccion as direccion_cvv,
                         cvv.telefono as telefono_cvv,
                         motivo_verificacion.descripcion as motivo_verificacion_texto,
                         motivo_rechazo.nombre as motivo_rechazo_texto,
                         tipo_sancion.nombre as nombre_tipo_sancion')
                ->join('estados_denuncia', 'estados_denuncia.id_estado = denuncias.id_estado_actual', 'left')
                ->join('areas', 'areas.id_area = denuncias.id_area_responsable', 'left')
                ->join('admin', 'admin.id_adm = denuncias.id_usuario_asignado', 'left')
                ->join('tipo_denuncia', 'tipo_denuncia.id_tipo_denuncia = denuncias.id_tipo_denuncia', 'left')
                ->join('tema_denuncia', 'tema_denuncia.id_tema_denuncia = denuncias.id_tema_denuncia', 'left')
                ->join('centros_verificacion_vehicular cvv', 'cvv.clave = denuncias.clave_cvv', 'left')
                ->join('motivo_verificacion', 'motivo_verificacion.id_motivo_verificacion = denuncias.id_motivo_verificacion', 'left')
                ->join('motivos_rechazo_dns motivo_rechazo', 'motivo_rechazo.id_motivo = denuncias.id_motivo_rechazo', 'left')
                ->join('tipos_sancion tipo_sancion', 'tipo_sancion.id_tipo_sancion = denuncias.id_tipo_sancion', 'left')
                ->find($id);

            if (!$denuncia) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Denuncia no encontrada'
                ]);
            }

            // Obtener historial
            $historial = $this->historialDenunciasModel
                ->select('historial_denuncias.*, 
                         admin.nombre as nombre_usuario,
                         admin.apellidoP as apellidoP_usuario,
                         admin.apellidoM as apellidoM_usuario,
                         area_origen.nombre_area as area_origen_nombre,
                         area_destino.nombre_area as area_destino_nombre')
                ->join('admin', 'admin.id_adm = historial_denuncias.id_usuario', 'left')
                ->join('areas as area_origen', 'area_origen.id_area = historial_denuncias.id_area_origen', 'left')
                ->join('areas as area_destino', 'area_destino.id_area = historial_denuncias.id_area_destino', 'left')
                ->where('historial_denuncias.id_denuncia', $id)
                ->orderBy('historial_denuncias.fecha_accion', 'DESC')
                ->findAll();

            // Obtener evidencias subidas por el usuario final
            $evidenciasUsuario = $this->archivosDenunciasModel
                ->where('id_denuncia', $id)
                ->orderBy('fecha_subida', 'DESC')
                ->findAll();

            // Normalizar estructura agregando campo 'origen'
            foreach ($evidenciasUsuario as &$doc) {
                $doc['origen'] = 'usuario';
                $doc['id_documento_unico'] = 'ev_' . $doc['id_evidencia'];
            }
            unset($doc);

            // Obtener documentos oficiales subidos por personal operativo
            $documentosOficiales = $this->documentosDenunciaModel
                ->select('documentos_denuncia.*, 
                         tipos_documento.nombre as tipo_documento_nombre,
                         tipos_documento.codigo as tipo_documento_codigo,
                         admin.nombre as nombre_usuario_subida,
                         admin.apellidoP as apellidoP_usuario_subida,
                         areas.nombre_area as area_nombre,
                         estados_denuncia.nombre_estado as estado_denuncia_nombre')
                ->join('tipos_documento', 'tipos_documento.id_tipo_documento = documentos_denuncia.id_tipo_documento', 'left')
                ->join('admin', 'admin.id_adm = documentos_denuncia.id_usuario_subida', 'left')
                ->join('areas', 'areas.id_area = documentos_denuncia.id_area', 'left')
                ->join('estados_denuncia', 'estados_denuncia.id_estado = documentos_denuncia.id_estado_denuncia', 'left')
                ->where('documentos_denuncia.id_denuncia', $id)
                ->orderBy('documentos_denuncia.fecha_subida', 'DESC')
                ->findAll();

            // Normalizar estructura agregando campo 'origen'
            foreach ($documentosOficiales as &$doc) {
                $doc['origen'] = 'operativo';
                $doc['id_documento_unico'] = 'doc_' . $doc['id_documento'];
            }
            unset($doc);

            // Combinar ambos arrays
            $documentos = array_merge($evidenciasUsuario, $documentosOficiales);

            // Organizar documentos por categoría
            $documentosOrganizados = [
                'identificacion' => [],
                'evidencias' => [],
                'inspeccion' => [],
                'turnado' => [],
                'sancion' => [],
                'otros' => []
            ];

            foreach ($documentos as $doc) {
                // Determinar categoría según origen
                if ($doc['origen'] === 'usuario') {
                    // evidencias_denuncia: tipo_documento es texto libre
                    $tipo = strtolower(trim($doc['tipo_documento'] ?? 'otros'));

                    if (stripos($tipo, 'identificacion') !== false || stripos($tipo, 'identificación') !== false) {
                        $documentosOrganizados['identificacion'][] = $doc;
                    } elseif (stripos($tipo, 'evidencia') !== false) {
                        $documentosOrganizados['evidencias'][] = $doc;
                    } else {
                        $documentosOrganizados['otros'][] = $doc;
                    }
                } else {
                    // documentos_denuncia: usa códigos de tipos_documento
                    $codigo = $doc['tipo_documento_codigo'] ?? 'OTRO';

                    if ($codigo === 'IDENTIFICACION' || $codigo === 'EVIDENCIA_INICIAL') {
                        $documentosOrganizados['identificacion'][] = $doc;
                    } elseif ($codigo === 'EVIDENCIA' || $codigo === 'FOTO_INSPECCION' || $codigo === 'FOTOGRAFIA' || $codigo === 'VIDEO') {
                        $documentosOrganizados['evidencias'][] = $doc;
                    } elseif ($codigo === 'ACTA_INSPECCION' || $codigo === 'INSPECCION' || $codigo === 'DOCUMENTO_TECNICO') {
                        $documentosOrganizados['inspeccion'][] = $doc;
                    } elseif ($codigo === 'TURNADO' || $codigo === 'OFICIO_TURNADO' || $codigo === 'OFICIO_REMISION') {
                        $documentosOrganizados['turnado'][] = $doc;
                    } elseif (
                        $codigo === 'ACTA_SANCION' || $codigo === 'SANCION' || $codigo === 'RESOLUCION' ||
                        $codigo === 'DICTAMEN_DNS' || $codigo === 'OFICIO_RECHAZO'
                    ) {
                        $documentosOrganizados['sancion'][] = $doc;
                    } else {
                        $documentosOrganizados['otros'][] = $doc;
                    }
                }
            }

            log_message('debug', "Denuncia ID {$id}: " . count($historial) . " registros de historial, " .
                count($evidenciasUsuario) . " evidencias usuario, " .
                count($documentosOficiales) . " documentos oficiales");

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'denuncia' => $denuncia,
                    'historial' => $historial,
                    'documentos' => $documentos,
                    'documentos_organizados' => $documentosOrganizados
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener detalle de denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Aprobar inspección (DNS aprueba que continúe a DS)
     * POST /admin/aprobarInspeccion
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function aprobarInspeccion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario || $usuario['codigo_rol'] !== 'USR_DNS') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para aprobar inspecciones'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $observaciones = $this->request->getPost('observaciones');

        if (!$idDenuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falta el ID de denuncia'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar que el usuario sea el asignado
        if ($denuncia['id_usuario_asignado'] != $usuario['id_adm']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este caso no está asignado a usted'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar documento de aprobación (opcional)
            $archivo = $this->request->getFile('archivo');
            if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
                log_message('info', "Procesando archivo de aprobación: {$archivo->getName()}");
                
                $hashArchivo = $this->calcularHashArchivo($archivo);
                $nombreOriginal = $archivo->getName();
                $tamañoBytes = $archivo->getSize();
                $mimeType = $archivo->getMimeType();

                $nombreUnico = $this->generarNombreUnico($nombreOriginal);
                $rutaDestino = $this->obtenerRutaSubida('APROBACION_INSPECCION');

                // Crear directorio si no existe
                if (!is_dir($rutaDestino)) {
                    mkdir($rutaDestino, 0755, true);
                }

                // Mover archivo
                $archivo->move($rutaDestino, $nombreUnico);
                
                log_message('info', "Archivo guardado en: {$rutaDestino}{$nombreUnico}");

                // Guardar en base de datos
                $datosDoc = [
                    'id_denuncia' => $idDenuncia,
                    'id_tipo_documento' => 3, // DICTAMEN_DNS (documento de aprobación)
                    'nombre_original' => $nombreOriginal,
                    'nombre_almacenado' => $nombreUnico,
                    'ruta_archivo' => $rutaDestino . $nombreUnico,
                    'hash_sha256' => $hashArchivo,
                    'peso_bytes' => $tamañoBytes,
                    'tipo_mime' => $mimeType,
                    'id_usuario_subida' => $usuario['id_adm'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);
                
                log_message('info', "Documento guardado en BD con ID: {$idDocumento}");
            } else {
                if ($archivo) {
                    log_message('warning', "Archivo no válido o ya movido. Error: " . $archivo->getErrorString());
                } else {
                    log_message('info', "No se adjuntó documento de aprobación (es opcional)");
                }
            }

            // Actualizar a APROBADA_INSPECCION y turnar a DS
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 4, // APROBADA_INSPECCION
                'id_area_responsable' => 1, // Área DS
                'id_usuario_asignado' => null, // Sin asignar en DS aún
                'fecha_aprobacion_inspeccion' => date('Y-m-d H:i:s')
            ]);

            // Cambiar a TURNADA_DS inmediatamente
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 5 // TURNADA_DS
            ]);

            // Registrar historial de aprobación
            $this->registrarHistorial(
                $idDenuncia,
                'APROBACION_INSPECCION',
                'EN_REVISION_DNS',
                'TURNADA_DS',
                2, // DNS (área origen)
                1, // DS (área destino) - CORREGIDO: era 4 (no existe)
                $observaciones
            );

            // Registrar turnado a DS
            $this->turnadosModel->insert([
                'id_denuncia' => $idDenuncia,
                'id_area_origen' => 2, // DNS
                'id_area_destino' => 1, // DS
                'id_usuario_turna' => $usuario['id_adm'],
                'observaciones' => 'Aprobado para inspección en campo',
                'fecha_turnado' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al aprobar inspección'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inspección aprobada y turnada a Supervisión',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al aprobar inspección: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Rechazar denuncia por falta de competencia
     * POST /admin/rechazarDenuncia
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function rechazarDenuncia()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $idMotivoRechazo = $this->request->getPost('id_motivo_rechazo');
        $observaciones = $this->request->getPost('observaciones');
        $archivo = $this->request->getFile('archivo');

        if (!$idDenuncia || !$idMotivoRechazo || !$observaciones) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos (motivo, observaciones)'
            ]);
        }

        // Flujo B: Documento obligatorio para rechazos
        if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El oficio de rechazo es obligatorio'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar que el usuario sea el asignado
        if ($denuncia['id_usuario_asignado'] != $usuario['id_adm']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este caso no está asignado a usted'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar oficio de rechazo (obligatorio)
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida('OFICIO_RECHAZO');

            // Mover archivo
            $archivo->move($rutaDestino, $nombreUnico);

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => 11, // OFICIO_RECHAZO (según tipos_documento)
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            // Actualizar a CONCLUIDA_NO_COMPETENTE (estado 20 - Flujo B)
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 20, // CONCLUIDA_NO_COMPETENTE
                'id_motivo_rechazo' => $idMotivoRechazo,
                'fecha_rechazo' => date('Y-m-d H:i:s'),
                'observaciones_rechazo' => $observaciones,
                'fecha_ultimo_cambio_estado' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $estadoAnterior = $this->estadosDenunciaModel->find($denuncia['id_estado_actual']);
            $estadoNuevo = $this->estadosDenunciaModel->find(20); // CONCLUIDA_NO_COMPETENTE
            $motivo = $this->motivosRechazoModel->find($idMotivoRechazo);

            $this->registrarHistorial(
                $idDenuncia,
                'RECHAZO_DNS',
                $estadoAnterior['nombre_estado'] ?? 'DESCONOCIDO',
                $estadoNuevo['nombre_estado'] ?? 'CONCLUIDA_NO_COMPETENTE',
                $usuario['id_area'],
                null,
                'Motivo: ' . ($motivo['nombre'] ?? 'N/A') . ' - ' . $observaciones
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al rechazar denuncia'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Denuncia rechazada correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al rechazar denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Concluir inspección (DS sube acta y concluye)
     * POST /admin/concluirInspeccion
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function concluirInspeccion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario || $usuario['codigo_rol'] !== 'USR_DS') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para concluir inspecciones'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $observaciones = $this->request->getPost('observaciones');
        $resultadoInspeccion = $this->request->getPost('resultado_inspeccion');
        $hallazgos = $this->request->getPost('hallazgos');
        $recomendaciones = $this->request->getPost('recomendaciones');
        $archivo = $this->request->getFile('archivo');

        // Validaciones
        if (!$idDenuncia || !$archivo || !$archivo->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Se requiere el acta de inspección'
            ]);
        }

        if (!$resultadoInspeccion || !in_array($resultadoInspeccion, ['INFRACCION_DETECTADA', 'SIN_INFRACCIONES'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debe seleccionar el resultado de la inspección'
            ]);
        }

        if (empty($hallazgos) || strlen($hallazgos) < 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Los hallazgos deben tener al menos 100 caracteres'
            ]);
        }

        if (empty($recomendaciones)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Las recomendaciones son requeridas'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar que el usuario sea el asignado
        if ($denuncia['id_usuario_asignado'] != $usuario['id_adm']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este caso no está asignado a usted'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Subir acta de inspección
            // Calcular hash ANTES de mover el archivo
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida('ACTA_INSPECCION');

            // Mover archivo
            $archivo->move($rutaDestino, $nombreUnico);

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => 2, // ACTA_INSPECCION
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            // Paso 1: Actualizar a INSPECCION_CONCLUIDA (temporal)
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 7, // INSPECCION_CONCLUIDA
                'fecha_conclusion_inspeccion' => date('Y-m-d H:i:s'),
                'observaciones_inspeccion' => $observaciones,
                'resultado_inspeccion' => $resultadoInspeccion,
                'hallazgos_inspeccion' => $hallazgos,
                'recomendaciones_inspeccion' => $recomendaciones
            ]);

            // Registrar historial de conclusión
            $this->registrarHistorial(
                $idDenuncia,
                'CONCLUSION_INSPECCION',
                'EN_INSPECCION',
                'INSPECCION_CONCLUIDA',
                1, // DS (área origen)
                null,
                "Inspección concluida - Resultado: {$resultadoInspeccion}"
            );

            // Paso 2: Transición automática a REGRESADA_DNS
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 8,           // REGRESADA_DNS
                'id_area_responsable' => 2,        // DNS
                'id_usuario_asignado' => null,     // Sin asignar
                'fecha_regreso_dns' => date('Y-m-d H:i:s')
            ]);

            // Registrar turnado a DNS
            $observacionesTurnado = "Inspección concluida - Resultado: {$resultadoInspeccion}. " .
                                   "Hallazgos: " . substr($hallazgos, 0, 200) . 
                                   (strlen($hallazgos) > 200 ? '...' : '');
            
            $this->turnadosModel->insert([
                'id_denuncia' => $idDenuncia,
                'id_area_origen' => 1,              // DS
                'id_area_destino' => 2,             // DNS
                'id_usuario_turna' => $usuario['id_adm'],
                'observaciones' => $observacionesTurnado,
                'fecha_turnado' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial de regreso a DNS
            $this->registrarHistorial(
                $idDenuncia,
                'REGRESO_DNS',
                'INSPECCION_CONCLUIDA',
                'REGRESADA_DNS',
                1,  // DS (área origen)
                2,  // DNS (área destino)
                'Caso regresado automáticamente a DNS para análisis post-inspección'
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al concluir inspección'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inspección concluida y enviada automáticamente al DNS',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio'],
                    'id_documento' => $idDocumento,
                    'nuevo_estado' => 'REGRESADA_DNS',
                    'area_responsable' => 'DNS'
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al concluir inspección: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Regresar a DNS después de inspección
     * POST /admin/regresarADNS
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function regresarADNS()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario || $usuario['codigo_rol'] !== 'USR_DS') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para esta acción'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $observaciones = $this->request->getPost('observaciones');

        if (!$idDenuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falta el ID de denuncia'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia || $denuncia['id_estado_actual'] != 7) { // INSPECCION_CONCLUIDA
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La inspección debe estar concluida'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Actualizar a REGRESADA_DNS
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 8, // REGRESADA_DNS
                'id_area_responsable' => 2, // DNS
                'id_usuario_asignado' => null, // Sin asignar en DNS
                'fecha_regreso_dns' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $this->registrarHistorial(
                $idDenuncia,
                'REGRESO_DNS',
                'INSPECCION_CONCLUIDA',
                'REGRESADA_DNS',
                1, // DS (área origen) - CORREGIDO: era 4 (no existe)
                2, // DNS (área destino)
                $observaciones
            );

            // Registrar turnado
            $this->turnadosModel->insert([
                'id_denuncia' => $idDenuncia,
                'id_area_origen' => 1, // DS
                'id_area_destino' => 2, // DNS
                'id_usuario_turna' => $usuario['id_adm'],
                'observaciones' => 'Inspección concluida - Para análisis de sanción',
                'fecha_turnado' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al regresar a DNS'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Caso regresado a Normativa y Sanciones',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al regresar a DNS: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Emitir sanción
     * POST /admin/emitirSancion
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function emitirSancion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario || $usuario['codigo_rol'] !== 'USR_DNS') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para emitir sanciones'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $idTipoSancion = $this->request->getPost('id_tipo_sancion') ?? 1; // Valor por defecto
        $montoSancion = $this->request->getPost('monto_sancion') ?? 0;
        $observaciones = $this->request->getPost('observaciones') ?? '';
        $archivo = $this->request->getFile('archivo');

        if (!$idDenuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falta el ID de denuncia'
            ]);
        }

        if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Se requiere el acta de sanción'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            log_message('info', "Procesando acta de sanción: {$archivo->getName()}");

            // Calcular hash ANTES de mover el archivo
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida('ACTA_SANCION');

            // Crear directorio si no existe
            if (!is_dir($rutaDestino)) {
                mkdir($rutaDestino, 0755, true);
            }

            // Mover archivo
            $archivo->move($rutaDestino, $nombreUnico);

            log_message('info', "Archivo guardado en: {$rutaDestino}{$nombreUnico}");

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => 7, // ACTA_SANCION
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            log_message('info', "Documento guardado en BD con ID: {$idDocumento}");

            // Actualizar a SANCIONADA
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 10, // SANCIONADA
                'id_tipo_sancion' => $idTipoSancion,
                'monto_sancion' => $montoSancion,
                'fecha_sancion' => date('Y-m-d H:i:s'),
                'observaciones_sancion' => $observaciones
            ]);

            // Registrar historial
            $estadoAnterior = $this->estadosDenunciaModel->find($denuncia['id_estado_actual'])['nombre_estado'] ?? null;

            $this->registrarHistorial(
                $idDenuncia,
                'EMISION_SANCION',
                $estadoAnterior,
                'SANCIONADA',
                2, // DNS
                null,
                $observaciones
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al emitir sanción'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sanción emitida correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio'],
                    'id_documento' => $idDocumento
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al emitir sanción: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Finalizar caso (cerrar sin sanción)
     * POST /admin/finalizarCaso
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function finalizarCaso()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario || $usuario['codigo_rol'] !== 'USR_DNS') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para finalizar casos'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $observaciones = $this->request->getPost('observaciones');
        $archivo = $this->request->getFile('archivo');

        if (!$idDenuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falta el ID de denuncia'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar resolución si se adjuntó
            $idDocumento = null;
            if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
                // Calcular hash ANTES de mover el archivo
                $hashArchivo = $this->calcularHashArchivo($archivo);
                $nombreOriginal = $archivo->getName();
                $tamañoBytes = $archivo->getSize();
                $mimeType = $archivo->getMimeType();

                $nombreUnico = $this->generarNombreUnico($nombreOriginal);
                $rutaDestino = $this->obtenerRutaSubida('RESOLUCION');

                // Mover archivo
                $archivo->move($rutaDestino, $nombreUnico);

                $datosDoc = [
                    'id_denuncia' => $idDenuncia,
                    'id_tipo_documento' => 7, // RESOLUCION
                    'nombre_original' => $nombreOriginal,
                    'nombre_almacenado' => $nombreUnico,
                    'ruta_archivo' => $rutaDestino . $nombreUnico,
                    'hash_sha256' => $hashArchivo,
                    'peso_bytes' => $tamañoBytes,
                    'tipo_mime' => $mimeType,
                    'id_usuario_subida' => $usuario['id_adm'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);
            }

            // Actualizar a FINALIZADA
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 15, // FINALIZADA
                'fecha_finalizacion' => date('Y-m-d H:i:s'),
                'observaciones_finalizacion' => $observaciones
            ]);

            // Registrar historial
            $estadoAnterior = $this->estadosDenunciaModel->find($denuncia['id_estado_actual'])['nombre_estado'] ?? null;

            $this->registrarHistorial(
                $idDenuncia,
                'FINALIZACION',
                $estadoAnterior,
                'FINALIZADA',
                2, // DNS
                null,
                $observaciones
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al finalizar caso'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Caso finalizado correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al finalizar caso: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Desechar denuncia (Flujo D - Solo administradores)
     * POST /admin/desecharDenuncia
     * 
     * Permite desechar una denuncia por motivos administrativos o legales.
     * Solo usuarios ADM_GENERAL o ADM_DA pueden ejecutar esta acción.
     * Cambia el estado a 21 (DESECHADA) o 22 (ARCHIVADA_SIN_SEGUIMIENTO).
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function desecharDenuncia()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();

        // Solo administradores pueden desechar denuncias
        if (!$usuario || !in_array($usuario['codigo_rol'], ['ADM_GENERAL', 'ADM_DA', 'ADM'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para desechar denuncias. Esta acción requiere privilegios de administrador.'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $motivoDesechamiento = $this->request->getPost('motivo_desechamiento');
        $justificacion = $this->request->getPost('justificacion');
        $archivo = $this->request->getFile('archivo');

        // Validar datos requeridos
        if (!$idDenuncia || !$motivoDesechamiento || !$justificacion) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos (motivo y justificación obligatorios)'
            ]);
        }

        // Validar justificación mínima
        if (strlen(trim($justificacion)) < 50) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La justificación debe tener al menos 50 caracteres'
            ]);
        }

        // Validar documento obligatorio
        if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El oficio de desechamiento es obligatorio'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // No permitir desechar casos ya terminados
        $estadosTerminales = [11, 20, 21, 22]; // FINALIZADA, CONCLUIDA_NO_COMPETENTE, DESECHADA, ARCHIVADA_SIN_SEGUIMIENTO
        if (in_array($denuncia['id_estado_actual'], $estadosTerminales)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se puede desechar una denuncia que ya está en estado terminal'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar oficio de desechamiento (obligatorio)
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida('OFICIO_DESECHAMIENTO');

            // Mover archivo
            $archivo->move($rutaDestino, $nombreUnico);

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => 12, // OFICIO_DESECHAMIENTO
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            // Determinar estado final según motivo
            $idEstadoFinal = 21; // DESECHADA (por defecto)
            if ($motivoDesechamiento === 'ARCHIVO_SIN_SEGUIMIENTO') {
                $idEstadoFinal = 22; // ARCHIVADA_SIN_SEGUIMIENTO
            }

            // Actualizar denuncia
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => $idEstadoFinal,
                'fecha_desechamiento' => date('Y-m-d H:i:s'),
                'motivo_desechamiento' => $motivoDesechamiento,
                'justificacion_desechamiento' => $justificacion,
                'id_usuario_desecha' => $usuario['id_adm'],
                'fecha_ultimo_cambio_estado' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $estadoAnterior = $this->estadosDenunciaModel->find($denuncia['id_estado_actual']);
            $estadoNuevo = $this->estadosDenunciaModel->find($idEstadoFinal);

            $this->registrarHistorial(
                $idDenuncia,
                'DESECHAMIENTO',
                $estadoAnterior['nombre_estado'] ?? 'DESCONOCIDO',
                $estadoNuevo['nombre_estado'] ?? 'DESECHADA',
                $usuario['id_area'],
                null,
                'Motivo: ' . $motivoDesechamiento . ' - ' . $justificacion
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al desechar denuncia'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Denuncia desechada correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio'],
                    'estado_final' => $idEstadoFinal
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al desechar denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Suspender denuncia temporalmente
     * POST /admin/suspenderDenuncia
     * 
     * Permite suspender temporalmente el trámite de una denuncia por causas legales o administrativas.
     * Cambia el estado a 30 (SUSPENDIDA) y guarda el estado anterior para poder reanudar.
     * Solo usuarios ADM_GENERAL, ADM_DA o USR_DNS pueden ejecutar esta acción.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function suspenderDenuncia()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();

        // Solo administradores y DNS pueden suspender denuncias
        if (!$usuario || !in_array($usuario['codigo_rol'], ['ADM_GENERAL', 'ADM_DA', 'ADM', 'USR_DNS'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para suspender denuncias'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $motivoSuspension = $this->request->getPost('motivo_suspension');
        $observaciones = $this->request->getPost('observaciones');
        $archivo = $this->request->getFile('archivo');

        // Validar datos requeridos
        if (!$idDenuncia || !$motivoSuspension || !$observaciones) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos (motivo y observaciones obligatorios)'
            ]);
        }

        // Documento obligatorio
        if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El acta de suspensión es obligatoria'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // No permitir suspender casos ya terminados
        $estadosTerminales = [11, 20, 21, 22, 30]; // FINALIZADA, CONCLUIDA_NO_COMPETENTE, DESECHADA, ARCHIVADA, SUSPENDIDA
        if (in_array($denuncia['id_estado_actual'], $estadosTerminales)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se puede suspender una denuncia en estado terminal o ya suspendida'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar acta de suspensión (obligatoria)
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida('ACTA_SUSPENSION');

            $archivo->move($rutaDestino, $nombreUnico);

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => 13, // ACTA_SUSPENSION
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];
            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            // Guardar estado anterior para poder reanudar
            $estadoAnterior = $denuncia['id_estado_actual'];

            // Actualizar a SUSPENDIDA
            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => 30, // SUSPENDIDA
                'estado_antes_suspension' => $estadoAnterior,
                'motivo_suspension' => $motivoSuspension,
                'observaciones_suspension' => $observaciones,
                'fecha_suspension' => date('Y-m-d H:i:s'),
                'id_usuario_suspende' => $usuario['id_adm'],
                'fecha_ultimo_cambio_estado' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $estadoAnteriorNombre = $this->estadosDenunciaModel->find($estadoAnterior);

            $this->registrarHistorial(
                $idDenuncia,
                'SUSPENSION',
                $estadoAnteriorNombre['nombre_estado'] ?? 'DESCONOCIDO',
                'SUSPENDIDA',
                $usuario['id_area'],
                null,
                'Motivo: ' . $motivoSuspension . ' - ' . $observaciones
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al suspender denuncia'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Denuncia suspendida correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al suspender denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reanudar denuncia suspendida
     * POST /admin/reanudarDenuncia
     * 
     * Permite reanudar una denuncia que fue suspendida temporalmente.
     * Restaura el estado anterior a la suspensión y registra el acta de reanudación.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function reanudarDenuncia()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();

        // Solo administradores y DNS pueden reanudar denuncias
        if (!$usuario || !in_array($usuario['codigo_rol'], ['ADM_GENERAL', 'ADM_DA', 'ADM', 'USR_DNS'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tiene permisos para reanudar denuncias'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $observaciones = $this->request->getPost('observaciones');
        $archivo = $this->request->getFile('archivo');

        if (!$idDenuncia || !$observaciones) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Verificar que esté suspendida
        if ($denuncia['id_estado_actual'] != 30) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La denuncia no está suspendida'
            ]);
        }

        // Verificar que tenga estado anterior guardado
        if (!$denuncia['estado_antes_suspension']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se puede reanudar: no se encontró el estado anterior'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Procesar acta de reanudación (opcional)
            $idDocumento = null;
            if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
                $hashArchivo = $this->calcularHashArchivo($archivo);
                $nombreOriginal = $archivo->getName();
                $tamañoBytes = $archivo->getSize();
                $mimeType = $archivo->getMimeType();

                $nombreUnico = $this->generarNombreUnico($nombreOriginal);
                $rutaDestino = $this->obtenerRutaSubida('ACTA_REANUDACION');

                $archivo->move($rutaDestino, $nombreUnico);

                $datosDoc = [
                    'id_denuncia' => $idDenuncia,
                    'id_tipo_documento' => 14, // ACTA_REANUDACION
                    'nombre_original' => $nombreOriginal,
                    'nombre_almacenado' => $nombreUnico,
                    'ruta_archivo' => $rutaDestino . $nombreUnico,
                    'hash_sha256' => $hashArchivo,
                    'peso_bytes' => $tamañoBytes,
                    'tipo_mime' => $mimeType,
                    'id_usuario_subida' => $usuario['id_adm'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);
            }

            // Restaurar al estado anterior
            $estadoRestaurar = $denuncia['estado_antes_suspension'];

            $this->denunciasModel->update($idDenuncia, [
                'id_estado_actual' => $estadoRestaurar,
                'estado_antes_suspension' => null,
                'observaciones_reanudacion' => $observaciones,
                'fecha_reanudacion' => date('Y-m-d H:i:s'),
                'id_usuario_reanuda' => $usuario['id_adm'],
                'fecha_ultimo_cambio_estado' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $estadoNuevoNombre = $this->estadosDenunciaModel->find($estadoRestaurar);

            $this->registrarHistorial(
                $idDenuncia,
                'REANUDACION',
                'SUSPENDIDA',
                $estadoNuevoNombre['nombre_estado'] ?? 'DESCONOCIDO',
                $usuario['id_area'],
                null,
                $observaciones
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al reanudar denuncia'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Denuncia reanudada correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'folio' => $denuncia['folio'],
                    'estado_restaurado' => $estadoRestaurar
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al reanudar denuncia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Asignar caso a otro usuario del área
     * POST /admin/asignarCaso
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function asignarCaso()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $idUsuarioDestino = $this->request->getPost('id_usuario_destino');
        $observaciones = $this->request->getPost('observaciones');

        if (!$idDenuncia || !$idUsuarioDestino) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos requeridos'
            ]);
        }

        $denuncia = $this->denunciasModel->find($idDenuncia);
        if (!$denuncia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Denuncia no encontrada'
            ]);
        }

        // Validar que el usuario destino pertenezca al área responsable
        $usuarioDestino = $this->administrador->find($idUsuarioDestino);
        if (!$usuarioDestino || $usuarioDestino['id_area'] != $denuncia['id_area_responsable']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El usuario no pertenece al área responsable'
            ]);
        }

        try {
            $estadoActual = $denuncia['id_estado_actual'];
            $estadoNuevo = null;

            // Determinar nuevo estado si es necesario
            if ($estadoActual == 2) $estadoNuevo = 3; // TURNADA_DNS -> EN_REVISION_DNS
            if ($estadoActual == 5) $estadoNuevo = 6; // TURNADA_DS -> EN_INSPECCION

            $this->denunciasModel->update($idDenuncia, [
                'id_usuario_asignado' => $idUsuarioDestino,
                'id_estado_actual' => $estadoNuevo ?? $estadoActual,
                'fecha_asignacion' => date('Y-m-d H:i:s')
            ]);

            // Registrar historial
            $estadoAnteriorNombre = $this->estadosDenunciaModel->find($estadoActual)['nombre_estado'] ?? null;
            $estadoNuevoNombre = $estadoNuevo ? $this->estadosDenunciaModel->find($estadoNuevo)['nombre_estado'] : null;

            $this->registrarHistorial(
                $idDenuncia,
                'ASIGNACION',
                $estadoAnteriorNombre,
                $estadoNuevoNombre,
                null,
                null,
                $observaciones ?? 'Asignado a ' . $usuarioDestino['nombre']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Caso asignado correctamente',
                'data' => [
                    'id_denuncia' => $idDenuncia,
                    'usuario_asignado' => $usuarioDestino['nombre'] . ' ' . $usuarioDestino['apellidoP']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al asignar caso: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Obtener usuarios del área para asignación
     * GET /admin/obtenerUsuariosArea/{idArea}
     * 
     * @param int $idArea ID del área
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function obtenerUsuariosArea(int $idArea)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        try {
            $usuarios = $this->administrador
                ->select('id_adm, nombre, apellidoP, apellidoM, email')
                ->where('id_area', $idArea)
                ->where('activo', 1)
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener usuarios: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor'
            ]);
        }
    }

    /**
     * Subir documento adicional
     * POST /admin/subirDocumento
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function subirDocumento()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petición inválida'
            ]);
        }

        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }

        $idDenuncia = $this->request->getPost('id_denuncia');
        $idTipoDocumento = $this->request->getPost('id_tipo_documento');
        $descripcion = $this->request->getPost('descripcion');
        $archivo = $this->request->getFile('archivo');

        if (!$idDenuncia || !$idTipoDocumento || !$archivo || !$archivo->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos o archivo inválido'
            ]);
        }

        try {
            // Obtener código de tipo de documento
            $tipoDoc = $this->tiposDocumentoModel->find($idTipoDocumento);
            if (!$tipoDoc) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de documento no válido'
                ]);
            }

            // Calcular hash ANTES de mover el archivo
            $hashArchivo = $this->calcularHashArchivo($archivo);
            $nombreOriginal = $archivo->getName();
            $tamañoBytes = $archivo->getSize();
            $mimeType = $archivo->getMimeType();

            $nombreUnico = $this->generarNombreUnico($nombreOriginal);
            $rutaDestino = $this->obtenerRutaSubida($tipoDoc['codigo_tipo']);

            // Mover archivo
            $archivo->move($rutaDestino, $nombreUnico);

            $datosDoc = [
                'id_denuncia' => $idDenuncia,
                'id_tipo_documento' => $idTipoDocumento,
                'nombre_original' => $nombreOriginal,
                'nombre_almacenado' => $nombreUnico,
                'ruta_archivo' => $rutaDestino . $nombreUnico,
                'hash_sha256' => $hashArchivo,
                'peso_bytes' => $tamañoBytes,
                'tipo_mime' => $mimeType,
                'descripcion' => $descripcion,
                'id_usuario_subida' => $usuario['id_adm'],
                'fecha_subida' => date('Y-m-d H:i:s')
            ];

            $idDocumento = $this->documentosDenunciaModel->insert($datosDoc);

            // Registrar en historial
            $denuncia = $this->denunciasModel->find($idDenuncia);
            $this->registrarHistorial(
                $idDenuncia,
                'SUBIDA_DOCUMENTO',
                null,
                null,
                null,
                null,
                'Documento subido: ' . $tipoDoc['nombre']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documento subido correctamente',
                'data' => [
                    'id_documento' => $idDocumento,
                    'nombre_almacenado' => $nombreUnico
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al subir documento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor al subir documento'
            ]);
        }
    }

    /**
     * Descargar documento
     * GET /admin/descargarDocumento/{id}
     * 
     * @param int $id ID del documento
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function descargarDocumento(int $id)
    {
        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return redirect()->to('/admin/login');
        }

        try {
            $documento = $this->documentosDenunciaModel->find($id);
            if (!$documento) {
                return redirect()->back()->with('error', 'Documento no encontrado');
            }

            $rutaArchivo = $documento['ruta_archivo'];
            if (!file_exists($rutaArchivo)) {
                return redirect()->back()->with('error', 'Archivo no encontrado en el servidor');
            }

            return $this->response->download($rutaArchivo, null)
                ->setFileName($documento['nombre_original']);
        } catch (\Exception $e) {
            log_message('error', 'Error al descargar documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al descargar documento');
        }
    }

    /**
     * Ver documento en navegador
     * GET /admin/verDocumento/{id}
     * 
     * @param int $id ID del documento
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function verDocumento(int $id)
    {
        $usuario = $this->obtenerDatosUsuario();
        if (!$usuario) {
            return redirect()->to('/admin/login');
        }

        try {
            $documento = $this->documentosDenunciaModel->find($id);
            if (!$documento) {
                return $this->response->setStatusCode(404, 'Documento no encontrado');
            }

            $rutaArchivo = $documento['ruta_archivo'];
            if (!file_exists($rutaArchivo)) {
                return $this->response->setStatusCode(404, 'Archivo no encontrado');
            }

            $mimeType = $documento['tipo_mime'];

            return $this->response
                ->setHeader('Content-Type', $mimeType)
                ->setHeader('Content-Disposition', 'inline; filename="' . $documento['nombre_original'] . '"')
                ->setBody(file_get_contents($rutaArchivo));
        } catch (\Exception $e) {
            log_message('error', 'Error al ver documento: ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'Error del servidor');
        }
    }
}
