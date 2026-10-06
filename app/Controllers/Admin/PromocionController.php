<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PromocionModel;

class PromocionController extends BaseController
{
    protected PromocionModel $promociones;

    public function __construct()
    {
        $this->promociones = new PromocionModel();
    }

    /**
     * Mostrar todas las promociones
     */
    public function index()
    {
        $data = [
            'promociones' => $this->promociones
                ->orderBy('fecha_inicio', 'DESC')
                ->findAll()
        ];

        return view('admin/promociones/index', $data);
    }

    /**
     * Mostrar formulario para crear
     */
    public function new()
    {
        return view('admin/promociones/form', [
            'promocion' => null
        ]);
    }

    /**
     * Guardar nueva promoción
     */
    public function create()
    {
        $data = [
            'titulo'       => $this->request->getPost('titulo'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin'    => $this->request->getPost('fecha_fin'),
            'condiciones'  => $this->request->getPost('condiciones')
        ];

        // Validar fechas
        if (
            !empty($data['fecha_inicio']) &&
            !empty($data['fecha_fin']) &&
            $data['fecha_fin'] < $data['fecha_inicio']
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'La fecha de finalización no puede ser anterior a la fecha de inicio.'
                ]);
        }

        // Procesar imagen
        $imagen = $this->request->getFile('imagen');

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

            $tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($imagen->getMimeType(), $tiposPermitidos)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'La imagen debe ser JPG, PNG o WEBP.'
                    ]);
            }

            $nombre = $imagen->getRandomName();

            $ruta = FCPATH . 'assets/img/promociones';

            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }

            $imagen->move($ruta, $nombre);

            $data['imagen_url'] =
                'assets/img/promociones/' . $nombre;
        }

        // Guardar en la base de datos
        if (!$this->promociones->insert($data)) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->promociones->errors());
        }

        return redirect()
            ->to('/admin/promociones')
            ->with('mensaje', 'Promoción creada correctamente.');
    }

    /**
     * Mostrar formulario para editar
     */
    public function edit($id = null)
    {
        $promocion = $this->promociones->find($id);

        if (!$promocion) {
            return redirect()
                ->to('/admin/promociones')
                ->with('mensaje', 'La promoción no existe.');
        }

        return view('admin/promociones/form', [
            'promocion' => $promocion
        ]);
    }

    /**
     * Actualizar promoción
     */
    public function update($id = null)
    {
        $promocion = $this->promociones->find($id);

        if (!$promocion) {
            return redirect()
                ->to('/admin/promociones')
                ->with('mensaje', 'La promoción no existe.');
        }

        $data = [
            'titulo'       => $this->request->getPost('titulo'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin'    => $this->request->getPost('fecha_fin'),
            'condiciones'  => $this->request->getPost('condiciones')
        ];

        // Validar fechas
        if (
            !empty($data['fecha_inicio']) &&
            !empty($data['fecha_fin']) &&
            $data['fecha_fin'] < $data['fecha_inicio']
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', [
                    'La fecha de finalización no puede ser anterior a la fecha de inicio.'
                ]);
        }

        // Procesar nueva imagen
        $imagen = $this->request->getFile('imagen');

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

            $tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($imagen->getMimeType(), $tiposPermitidos)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', [
                        'La imagen debe ser JPG, PNG o WEBP.'
                    ]);
            }

            $nombre = $imagen->getRandomName();

            $ruta = FCPATH . 'assets/img/promociones';

            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }

            $imagen->move($ruta, $nombre);

            $data['imagen_url'] =
                'assets/img/promociones/' . $nombre;
        }

        // Actualizar base de datos
        if (!$this->promociones->update($id, $data)) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->promociones->errors());
        }

        return redirect()
            ->to('/admin/promociones')
            ->with('mensaje', 'Promoción actualizada correctamente.');
    }

    /**
     * Eliminar promoción
     */
    public function delete($id = null)
    {
        $promocion = $this->promociones->find($id);

        if (!$promocion) {
            return redirect()
                ->to('/admin/promociones')
                ->with('mensaje', 'La promoción no existe.');
        }

        $this->promociones->delete($id);

        return redirect()
            ->to('/admin/promociones')
            ->with('mensaje', 'Promoción eliminada correctamente.');
    }
}