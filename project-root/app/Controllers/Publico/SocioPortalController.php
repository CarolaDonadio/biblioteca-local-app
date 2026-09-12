<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\RegistroModel;
use App\Models\ReservaModel;

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

    public function registro()
    {
        return view('publico/socio_registro');
    }
    public function guardarRegistro()
{
    $dni      = $this->request->getPost('dni');
    $nombre   = trim((string) $this->request->getPost('nombre'));
    $apellido = trim((string) $this->request->getPost('apellido'));
    $email    = trim((string) $this->request->getPost('email'));
    $telefono = trim((string) $this->request->getPost('telefono'));
    $password = (string) $this->request->getPost('password');

    if (! $dni || ! $nombre || ! $apellido || ! $email || ! $password) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Completá todos los campos obligatorios.');
    }

    $existente = $this->usuarioModel
        ->groupStart()
            ->where('dni', $dni)
            ->orWhere('mail', $email)
        ->groupEnd()
        ->first();

    if ($existente) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Ya existe un usuario con ese DNI o email.');
    }

    $datos = [
        'dni'             => $dni,
        'nombre_completo' => trim($nombre . ' ' . $apellido),
        'telefono'        => $telefono,
        'mail'            => $email,
        'password_hash'   => password_hash($password, PASSWORD_DEFAULT),
        'perfil'          => 'socio',
        'estado'          => 'activo',
    ];

    if (! $this->usuarioModel->insert($datos)) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'No se pudo crear la cuenta.');
    }

    return redirect()
        ->to('/socio/login')
        ->with('mensaje', 'Cuenta creada correctamente. Ya podés iniciar sesión.');
}
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('mensaje', 'Sesión cerrada correctamente.');
    }
}
