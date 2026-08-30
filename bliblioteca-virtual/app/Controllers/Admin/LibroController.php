<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LibroModel;
use App\Models\MultimediaModel;
use App\Models\EjemplarModel;

class LibroController extends BaseController
{
    protected LibroModel $libros;

    public function __construct()
    {
        $this->libros = new LibroModel();
    }

    public function index()
    {
        $termino = $this->request->getGet('q');
        $data['libros'] = $termino ? $this->libros->buscar($termino) : $this->libros->orderBy('titulo', 'ASC')->findAll();
        return view('admin/libros/index', $data);
    }

    public function new()
    {
        return view('admin/libros/form', ['libro' => null]);
    }

    public function create()
    {
        $data = $this->request->getPost(['isbn', 'titulo', 'autor', 'editorial', 'anio', 'categoria', 'sinopsis']);

        if (! $this->libros->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->libros->errors());
        }

        return redirect()->to('/admin/libros')->with('mensaje', 'Libro creado correctamente.');
    }

    public function edit($id = null)
    {
        $libro = $this->libros->find($id);
        if (! $libro) {
            return redirect()->to('/admin/libros')->with('error', 'Libro no encontrado.');
        }
        return view('admin/libros/form', ['libro' => $libro]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost(['isbn', 'titulo', 'autor', 'editorial', 'anio', 'categoria', 'sinopsis']);

        if (! $this->libros->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->libros->errors());
        }

        return redirect()->to('/admin/libros')->with('mensaje', 'Libro actualizado.');
    }

    public function delete($id = null)
    {
        $this->libros->delete($id);
        return redirect()->to('/admin/libros')->with('mensaje', 'Libro eliminado.');
    }

    // -------------------------------------------------------------
    // Sub-módulo: Multimedia (PDF / audiolibros) asociado al libro
    // -------------------------------------------------------------
    public function multimedia($libroId)
    {
        $data = [
            'libro'      => $this->libros->find($libroId),
            'multimedia' => (new MultimediaModel())->porLibro($libroId),
        ];
        return view('admin/libros/multimedia', $data);
    }

    public function subirMultimedia($libroId)
    {
        $archivo = $this->request->getFile('archivo');

        if (! $archivo || ! $archivo->isValid()) {
            return redirect()->back()->with('error', 'Archivo inválido.');
        }

        $tipo = $this->request->getPost('tipo'); // pdf | audiolibro
        $nuevoNombre = $archivo->getRandomName();
        $archivo->move(WRITEPATH . 'uploads/multimedia', $nuevoNombre);

        (new MultimediaModel())->insert([
            'libro_id'    => $libroId,
            'tipo'        => $tipo,
            'archivo_url' => 'uploads/multimedia/' . $nuevoNombre,
            'tamano_kb'   => intdiv($archivo->getSize(), 1024),
        ]);

        return redirect()->to("/admin/libros/{$libroId}/multimedia")->with('mensaje', 'Archivo subido.');
    }

    public function eliminarMultimedia($multimediaId)
    {
        $model = new MultimediaModel();
        $item  = $model->find($multimediaId);
        $model->delete($multimediaId);

        return redirect()->to("/admin/libros/{$item['libro_id']}/multimedia")->with('mensaje', 'Archivo eliminado.');
    }
}
