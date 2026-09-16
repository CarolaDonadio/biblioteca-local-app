<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Libraries\AutomaticNotificationService;
use App\Models\UsuarioModel;
use App\Models\RegistroModel;
use App\Models\ReservaModel;
use App\Models\RecomendacionModel;
use App\Models\LibroModel;

class SocioPortalController extends BaseController
{
    protected UsuarioModel $usuarioModel;
    protected RegistroModel $registroModel;
    protected ReservaModel $reservaModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->registroModel = new RegistroModel();
        $this->reservaModel = new ReservaModel();
    }

    public function login()
    {
        if (session()->get('socio_dni')) {
            return redirect()->to('/socio/home');
        }
        return view('publico/socio_login');
    }

    public function autenticar()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $usuario = $this->usuarioModel
            ->where('mail', $email)
            ->where('perfil', 'socio')
            ->where('estado', 'activo')
            ->first();

        if (! $usuario || ! password_verify($password, $usuario['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña inválidos.');
        }

        // Actualizar último login
        $this->usuarioModel->update($usuario['dni'], ['ultimo_login' => date('Y-m-d H:i:s')]);

        session()->set([
            'socio_dni'              => $usuario['dni'],
            'socio_nombre'           => $usuario['nombre_completo'],
            'socio_email'            => $usuario['mail'],
            'socio_last_activity'    => time(),
        ]);

        return redirect()->to('/socio/home');
    }

    public function home()
    {
        $socio_dni = session()->get('socio_dni');
        $socio = $this->usuarioModel->find($socio_dni);

        if (! $socio) {
            session()->destroy();
            return redirect()->to('/socio/login')->with('error', 'Usuario no encontrado.');
        }

        $registros = $this->registroModel
            ->select('registros.*, libros.titulo, libros.autor')
            ->join('libros', 'registros.idlibro = libros.id')
            ->where('registros.dniUsuario', $socio_dni)
            ->orderBy('registros.fechaPrestamo', 'DESC')
            ->findAll();

        $data = [
            'socio'     => $socio,
            'registros' => $registros,
            'titulo'    => 'Mi cuenta',
        ];

        return view('publico/socio_home', $data);
    }

    public function panel()
    {
        $socioDni = (int) session()->get('socio_dni');
        $socio = $this->usuarioModel->find($socioDni);

        if (! $socio) {
            session()->destroy();
            return redirect()->to('/socio/login')->with('error', 'Usuario no encontrado.');
        }

        $nombre = explode(' ', trim((string) $socio['nombre_completo']), 2);
        $socio['nombre'] = $nombre[0] ?? '';
        $historial = $this->registroModel->historialPorSocio($socioDni);

        return view('publico/socio_panel', [
            'socio'    => $socio,
            'historial' => $historial,
        ]);
    }

    public function misPrestamos()
    {
        $socioDni = (int) session()->get('socio_dni');
        $historial = $this->registroModel->historialPorSocio($socioDni);

        return view('publico/socio_prestamos', [
            'prestamos' => $historial['prestamos'],
        ]);
    }

    public function renovar($id)
    {
        $registro = $this->registroModel->find((int) $id);
        $socioDni = (int) session()->get('socio_dni');

        if (! $registro || (int) $registro['dniUsuario'] !== $socioDni) {
            return redirect()->back()->with('error', 'El préstamo no pertenece a tu cuenta.');
        }

        try {
            $this->registroModel->renovar((int) $id);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/socio/panel/prestamos')->with('mensaje', 'Préstamo renovado correctamente.');
    }

    public function sugerirLibro()
    {
        $titulo = trim((string) $this->request->getPost('titulo_sugerido'));
        $autor = trim((string) $this->request->getPost('autor_sugerido'));

        if ($titulo === '') {
            return redirect()->back()->withInput()->with('error', 'Ingresá el título del libro.');
        }

        $libros = new LibroModel();
        $consulta = $libros->where('titulo', $titulo);

        if ($autor !== '') {
            $consulta->where('autor', $autor);
        }

        $libro = $consulta->first();
        if (! $libro) {
            return redirect()->back()->withInput()->with('error', 'El libro no está en el catálogo. Podés recomendar libros existentes desde el catálogo.');
        }

        $socioId = (int) session()->get('socio_dni');
        $recomendaciones = new RecomendacionModel();

        if ($recomendaciones->yaRecomendo($socioId, (int) $libro['id'])) {
            return redirect()->to('/socio/panel')->with('error', 'Ya recomendaste este libro.');
        }

        if (! $recomendaciones->insert([
            'socio_id' => $socioId,
            'libro_id' => (int) $libro['id'],
        ])) {
            return redirect()->to('/socio/panel')->with('error', 'No se pudo registrar la recomendación.');
        }

        (new AutomaticNotificationService())->notifyProfile(
            'bibliotecario',
            'sugerencia_nueva',
            'El socio ' . session()->get('socio_nombre') . ' recomendó el libro «' . $libro['titulo'] . '».'
        );

        return redirect()->to('/socio/panel')->with('mensaje', 'Recomendación registrada correctamente.');
    }

    public function reservar($id)
    {
        $socioDni = (int) session()->get('socio_dni');

        try {
            $this->reservaModel->solicitar((int) $id, $socioDni);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/socio/home')->with('mensaje', 'Reserva registrada correctamente.');
    }

    public function recomendarLibro($id)
    {
        $libroId = (int) $id;
        $socioId = (int) session()->get('socio_dni');
        $libro = (new LibroModel())->find($libroId);

        if (! $libro) {
            return redirect()->back()->with('error', 'El libro seleccionado no existe.');
        }

        $recomendaciones = new RecomendacionModel();
        if ($recomendaciones->yaRecomendo($socioId, $libroId)) {
            return redirect()->back()->with('error', 'Ya recomendaste este libro.');
        }

        if (! $recomendaciones->insert(['socio_id' => $socioId, 'libro_id' => $libroId])) {
            if ($recomendaciones->yaRecomendo($socioId, $libroId)) {
                return redirect()->back()->with('error', 'Ya recomendaste este libro.');
            }

            return redirect()->back()->with('error', 'No se pudo registrar la recomendación.');
        }

        (new AutomaticNotificationService())->notifyProfile(
            'bibliotecario',
            'sugerencia_nueva',
            'El socio ' . session()->get('socio_nombre') . ' recomendó el libro «' . $libro['titulo'] . '».'
        );

        return redirect()->back()->with('mensaje', 'Recomendación registrada correctamente.');
    }

    public function actualizarPerfil()
    {
        $socio_dni = session()->get('socio_dni');
        $nombre = $this->request->getPost('nombre_completo');
        $telefono = $this->request->getPost('telefono');

        if (! $nombre || strlen($nombre) < 3) {
            return redirect()->back()->withInput()->with('error', 'El nombre es requerido y debe tener al menos 3 caracteres.');
        }

        $this->usuarioModel->update($socio_dni, [
            'nombre_completo' => $nombre,
            'telefono'        => $telefono,
        ]);

        session()->set('socio_nombre', $nombre);

        return redirect()->to('/socio/home')->with('mensaje', 'Perfil actualizado correctamente.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('mensaje', 'Sesión cerrada correctamente.');
    }
}
