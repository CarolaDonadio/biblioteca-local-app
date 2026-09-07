<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\PromocionModel;

class PromocionPublicaController extends BaseController
{
    public function index()
    {
        $data['promociones'] =
            (new PromocionModel())->vigentes();

        return view('publico/promociones', $data);
    }
}