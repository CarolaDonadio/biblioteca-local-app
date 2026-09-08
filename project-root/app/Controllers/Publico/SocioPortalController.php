<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
<<<<<<< HEAD
use App\Models\RegistroModel;
=======
>>>>>>> Rafael

class SocioPortalController extends BaseController
{
    protected UsuarioModel $usuarioModel;
    protected RegistroModel $registroModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->registroModel = new RegistroModel();
    }

    public function login()
    {
<<<<<<< HEAD
        if (session()->get('socio_dni')) {
            return redirect()->to('/socio/home');
        }
=======
        if (session()->get('socio_id')) {
            return redirect()->to('/socio/panel');
        }

>>>>>>> Rafael
        return view('publico/socio_login');
    }

    public function autenticar()
    {
<<<<<<< HEAD
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
=======
        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $model = new UsuarioModel();
        // Sólo perfil "socio": un email de bibliotecario no debe poder entrar por acá.
        $usuario = $model->verificarCredenciales($email, $password, ['socio']);

        if (! $usuario) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }

        session()->regenerate();
        session()->set([
            'socio_id'            => $usuario['dni'],
            'socio_nombre'        => $usuario['nombre_completo'],
            'socio_last_activity' => time(),
        ]);

        return redirect()->to('/socio/panel');
>>>>>>> Rafael
    }

    public function registro()
    {
        if (session()->get('socio_id')) {
            return redirect()->to('/socio/panel');
        }

        return view('publico/socio_registro');
    }

    public function guardarRegistro()
    {
        $nombre    = trim((string) $this->request->getPost('nombre'));
        $apellido  = trim((string) $this->request->getPost('apellido'));

        $datos = [
            'dni'             => $this->request->getPost('dni'),
            'nombre_completo' => trim($nombre . ' ' . $apellido),
            'mail'            => $this->request->getPost('email'),
            'telefono'        => $this->request->getPost('telefono'),
            'password'        => (string) $this->request->getPost('password'),
        ];

        $model    = new UsuarioModel();
        $resultado = $model->crearSocio($datos);

        if (! $resultado['success']) {
            return redirect()->back()->withInput()->with('errors', $resultado['errors']);
        }

        return redirect()->to('/socio/login')->with('mensaje', 'Cuenta creada. Ya podés iniciar sesión.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('mensaje', 'Sesión cerrada correctamente.');
    }
}
