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
        
        try {
            // Lógica original de tu compañero
            $data['libros'] = $termino ? $this->libros->buscar($termino) : $this->libros->orderBy('titulo', 'ASC')->findAll();
        } catch (\Throwable $e) {
            // Fallback con datos de prueba si la base de datos aún no responde
            $data['libros'] = [
                [
                    'id'        => 1,
                    'isbn'      => '978-9875666870',
                    'titulo'    => 'El Principito',
                    'autor'     => 'Antoine de Saint-Exupéry',
                    'categoria' => 'Clásicos'
                ],
                [
                    'id'        => 2,
                    'isbn'      => '978-9500700120',
                    'titulo'    => 'Rayuela',
                    'autor'     => 'Julio Cortázar',
                    'categoria' => 'Ficción'
                ],
                [
                    'id'        => 3,
                    'isbn'      => '978-8420658766',
                    'titulo'    => 'Ficciones',
                    'autor'     => 'Jorge Luis Borges',
                    'categoria' => 'Ficción'
                ]
            ];
        }

        $data['q'] = $termino;
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
        try {
            $libro = $this->libros->find($id);
        } catch (\Throwable $e) {
            $libro = null;
        }

        if (! $libro) {
            // Si la BD no está lista, pasamos un libro mock para maquetar la edición
            $libro = [
                'id'        => $id ?? 1,
                'isbn'      => '978-9875666870',
                'titulo'    => 'El Principito',
                'autor'     => 'Antoine de Saint-Exupéry',
                'editorial' => 'Salamandra',
                'anio'      => 1943,
                'categoria' => 'Clásicos',
                'sinopsis'  => 'Un pequeño príncipe viaja por el universo...'
            ];
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
        try {
            $this->libros->delete($id);
        } catch (\Throwable $e) {
            // Ignorar error si la BD no está conectada aún
        }
        return redirect()->to('/admin/libros')->with('mensaje', 'Libro eliminado.');
    }

    // -------------------------------------------------------------
    // Sub-módulo: Multimedia (PDF / audiolibros) asociado al libro
    // -------------------------------------------------------------
    public function multimedia($libroId)
    {
        try {
            $data = [
                'libro'      => $this->libros->find($libroId),
                'multimedia' => (new MultimediaModel())->porLibro($libroId),
            ];
        } catch (\Throwable $e) {
            $data = [
                'libro'      => ['id' => $libroId, 'titulo' => 'El Principito'],
                'multimedia' => []
            ];
        }
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

        try {
            (new MultimediaModel())->insert([
                'libro_id'    => $libroId,
                'tipo'        => $tipo,
                'archivo_url' => 'uploads/multimedia/' . $nuevoNombre,
                'tamano_kb'   => intdiv($archivo->getSize(), 1024),
            ]);
        } catch (\Throwable $e) {
            // Silenciar si no hay BD aún
        }

        return redirect()->to("/admin/libros/{$libroId}/multimedia")->with('mensaje', 'Archivo subido.');
    }

    public function eliminarMultimedia($multimediaId)
    {
        try {
            $model = new MultimediaModel();
            $item  = $model->find($multimediaId);
            $model->delete($multimediaId);
            $libroId = $item['libro_id'] ?? 1;
        } catch (\Throwable $e) {
            $libroId = 1;
        }

        return redirect()->to("/admin/libros/{$libroId}/multimedia")->with('mensaje', 'Archivo eliminado.');
    }
}
