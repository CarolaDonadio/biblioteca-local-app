<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\LibroModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CatalogoController extends BaseController
{
    public function index()
    {
        $libroModel = new LibroModel();

        $termino   = $this->request->getGet('q') ?? '';
        $categoria = $this->request->getGet('categoria') ?? null;

        $data['libros']    = $libroModel->buscarLibros($termino, $categoria);
        $data['termino']   = $termino;
        $data['categoria'] = $categoria;

        return view('publico/catalogo', $data);
    }

    // --- NUEVO MÉTODO: Detalle de Libro ---
    public function detalle($id = null)
    {
        $libroModel = new LibroModel();

        // Buscar el libro por su ID de la BD
        $libro = $libroModel->find($id);

        // Si no existe, lanza una excepción 404
        if (!$libro) {
            throw PageNotFoundException::forPageNotFound("El libro solicitado no existe.");
        }

        $data['libro'] = $libro;

        return view('publico/detalle', $data);
    }
}