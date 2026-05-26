<?php

namespace App\Controllers;

use App\Models\ArchivosDenunciasModel;
use App\Models\DenunciasModel;
use App\Models\AdminModel;

class Admin extends BaseController
{
    protected DenunciasModel $denunciasModel;
    protected ArchivosDenunciasModel $archivosDenunciasModel;
    protected AdminModel $adminModel;

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
                if (password_verify($password, $datoAdministrador['password'])) {
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
                        'c_acceso' => date('Y-m-d H:i:s')
                    ];
                    $session = session();
                    $session->set($datosAdministrador);
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

        $todasDenuncias = $this->denunciasModel->findAll();
        $archivosDenuncias = $this->archivosDenunciasModel->findAll();
        
        // Filtrar denuncias: excluir las que tienen estado 'Resuelto'
        $denuncias = array_filter($todasDenuncias, function($d) {
            return mb_strtolower($d['estatus'] ?? '', 'UTF-8') !== 'resuelta';
        });
        // Reindexar el array
        $denuncias = array_values($denuncias);

        $data = [
            'currentPage'     => 'inicio',
            'denuncias'       => $denuncias,
            'archivosDenuncias' => $archivosDenuncias,
            'adminNombre'     => session()->get('nombre'),
            'totalDenuncias'  => count($todasDenuncias),
            'casosResueltos'  => count(array_filter($todasDenuncias, fn($d) => mb_strtolower($d['estatus'] ?? '', 'UTF-8') === 'resuelta')),
            'casosPendientes' => count(array_filter($todasDenuncias, fn($d) => mb_strtolower($d['estatus'] ?? '', 'UTF-8') === 'pendiente')),
            'casosCriticos'   => count(array_filter($todasDenuncias, fn($d) => mb_strtolower($d['estatus'] ?? '', 'UTF-8') === 'crítico')),
        ];

        return view('admin/header', $data)
            . view('admin/dashboard', $data)
            . view('admin/footer');
    }

    // ─── GET /admin/usuarios ──────────────────────────────────────────────────
    public function usuarios()
    {
        if (! isset($this->session->id_adm)) {
            return redirect()->to(base_url('admin'));
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
    public function archivo()
    {
        if (!isset($this->session->id_adm)) {
            return redirect()->to(base_url('admin'));
        }

        // Obtener todas las denuncias resueltas
        $todasDenuncias = $this->denunciasModel->findAll();
        $archivosDenuncias = $this->archivosDenunciasModel->findAll();
        
        // Filtrar solo denuncias con estado 'Resuelta'
        $denunciasResueltas = array_filter($todasDenuncias, function($d) {
            return mb_strtolower($d['estatus'] ?? '', 'UTF-8') === 'resuelta';
        });
        
        // Reindexar y ordenar por fecha de resolución (más recientes primero)
        $denunciasResueltas = array_values($denunciasResueltas);
        usort($denunciasResueltas, function($a, $b) {
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
        return redirect()->to('/administrador');
    }
}