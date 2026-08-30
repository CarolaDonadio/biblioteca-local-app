<?php

namespace App\Controllers\Publico;

use App\Controllers\BaseController;
use App\Models\LibroModel;
use App\Models\MultimediaModel;
use App\Models\PromocionModel;

class CatalogoController extends BaseController
{
    public function index()
    {
        $libros = new LibroModel();

        $data['libros']      = $libros->orderBy('titulo', 'ASC')->findAll(24);
        $data['categorias']  = $libros->distinct()->select('categoria')->where('categoria !=', null)->findAll();
        $data['promociones'] = (new PromocionModel())->vigentes();

        return view('publico/catalogo', $data);
    }

    /** Buscador público de libros y consulta de disponibilidad (requisito MVP) */
    public function buscar()
    {
        $termino    = $this->request->getGet('q') ?? '';
        $categoria  = $this->request->getGet('categoria');

        $data['libros']    = (new LibroModel())->buscar($termino, $categoria);
        $data['termino']   = $termino;
        $data['categoria'] = $categoria;

        // Petición AJAX (usada por el buscador con Lazy Load) devuelve solo el fragmento
        if ($this->request->isAJAX()) {
            return view('publico/_fragmento_resultados', $data);
        }

        return view('publico/catalogo', $data);
    }

    public function detalle($id)
    {
        $libroModel = new LibroModel();
        $libro = $libroModel->find($id);

        if (! $libro) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['libro']          = $libro;
        $data['disponibilidad'] = $libroModel->disponibilidad((int) $id);
        $data['multimedia']     = (new MultimediaModel())->porLibro((int) $id);

        return view('publico/libro_detalle', $data);
    }
}
