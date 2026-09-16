<?php namespace App\Controllers;

use App\Models\PromocionModel;

class Inicio extends BaseController
{
    public function index(): string
    {
        return view('home', [
            'promociones' => (new PromocionModel())->vigentes(),
        ]);
    }
}