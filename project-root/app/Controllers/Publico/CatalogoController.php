<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\LibroModel;

class CatalogoController extends BaseController
{
    public function index()
    {
        $libroModel = new LibroModel();

        // Captura la búsqueda que viene de la barra del Home o del Catálogo
        $termino = $this->request->getGet('q') ?? '';

        // Consulta usando el método del LibroModel
        $data['libros']  = $libroModel->buscarLibros($termino);
        $data['termino'] = $termino;

        return view('publico/catalogo', $data);
    }
}