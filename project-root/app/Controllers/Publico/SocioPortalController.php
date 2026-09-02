<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;

class SocioPortalController extends BaseController
{
    public function login()
    {
        return view('publico/socio_login');
    }

    public function registro()
    {
        return view('publico/socio_registro');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }
}
