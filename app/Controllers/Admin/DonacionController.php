<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DonacionModel;

class DonacionController extends BaseController
{
    protected DonacionModel $donaciones;

    public function __construct()
    {
        $this->donaciones = new DonacionModel();
    }

    public function index()
    {
        return view('admin/donaciones/index', [
            'donaciones' => $this->donaciones->orderBy('fecha_donacion', 'DESC')->findAll(),
        ]);
    }

    public function new()
    {
        return view('admin/donaciones/form', ['donacion' => null]);
    }

    public function create()
    {
        $data = $this->datosFormulario();

        if (! $this->validateDatos($data)) {
            return redirect()->back()->withInput()->with('errors', $this->errores);
        }

        if (! $this->donaciones->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->donaciones->errors());
        }

        return redirect()->to('/admin/donaciones')->with('mensaje', 'Donación registrada correctamente.');
    }

    public function edit($id = null)
    {
        $donacion = $this->donaciones->find($id);
        if (! $donacion) {
            return redirect()->to('/admin/donaciones')->with('error', 'La donación no existe.');
        }

        return view('admin/donaciones/form', ['donacion' => $donacion]);
    }

    public function update($id = null)
    {
        if (! $this->donaciones->find($id)) {
            return redirect()->to('/admin/donaciones')->with('error', 'La donación no existe.');
        }

        $data = $this->datosFormulario();
        if (! $this->validateDatos($data)) {
            return redirect()->back()->withInput()->with('errors', $this->errores);
        }

        if (! $this->donaciones->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->donaciones->errors());
        }

        return redirect()->to('/admin/donaciones')->with('mensaje', 'Donación actualizada correctamente.');
    }

    public function delete($id = null)
    {
        if (! $this->donaciones->find($id)) {
            return redirect()->to('/admin/donaciones')->with('error', 'La donación no existe.');
        }

        $this->donaciones->delete($id);
        return redirect()->to('/admin/donaciones')->with('mensaje', 'Donación eliminada correctamente.');
    }

    private array $errores = [];

    private function datosFormulario(): array
    {
        return [
            'donante'        => trim((string) $this->request->getPost('donante')),
            'tipo'           => trim((string) $this->request->getPost('tipo')),
            'descripcion'    => trim((string) $this->request->getPost('descripcion')),
            'cantidad'       => (int) $this->request->getPost('cantidad'),
            'fecha_donacion' => $this->request->getPost('fecha_donacion'),
            'estado'         => trim((string) $this->request->getPost('estado')),
            'observaciones'  => trim((string) $this->request->getPost('observaciones')),
        ];
    }

    private function validateDatos(array $data): bool
    {
        $this->errores = [];
        if ($data['donante'] === '') $this->errores[] = 'Ingresá quién realizó la donación.';
        if ($data['tipo'] === '') $this->errores[] = 'Seleccioná el tipo de donación.';
        if ($data['descripcion'] === '') $this->errores[] = 'Ingresá una descripción.';
        if ($data['cantidad'] < 1) $this->errores[] = 'La cantidad debe ser mayor que cero.';
        if (empty($data['fecha_donacion'])) $this->errores[] = 'Ingresá la fecha de donación.';
        if (! in_array($data['estado'], ['recibida', 'pendiente', 'rechazada'], true)) {
            $this->errores[] = 'El estado seleccionado no es válido.';
        }

        return $this->errores === [];
    }
}