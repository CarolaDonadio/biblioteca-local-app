<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistroModel extends Model
{
    protected $table            = 'registros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'idlibro',
        'dniUsuario',
        'fechaPrestamo',
        'fechaVence',
        'fechaDevolucion',
    ];

    /**
     * Obtiene todos los préstamos activos (sin devolución)
     */
    public function activos()
    {
        return $this->where('fechaDevolucion', null)
                    ->select('registros.*, libros.titulo, usuarios.nombre_completo as socio_nombre')
                    ->join('libros', 'libros.id = registros.idlibro', 'left')
                    ->join('usuarios', 'usuarios.dni = registros.dniUsuario', 'left')
                    ->orderBy('registros.fechaVence', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene todos los préstamos vencidos (sin devolución y fecha vencimiento pasada)
     */
    public function vencidos()
    {
        return $this->where('fechaDevolucion', null)
                    ->where('fechaVence <', date('Y-m-d'))
                    ->select('registros.*, libros.titulo, usuarios.nombre_completo as socio_nombre')
                    ->join('libros', 'libros.id = registros.idlibro', 'left')
                    ->join('usuarios', 'usuarios.dni = registros.dniUsuario', 'left')
                    ->orderBy('registros.fechaVence', 'ASC')
                    ->findAll();
    }

    /**
     * Registra un nuevo préstamo
     */
    public function registrarPrestamo($idLibro, $dniUsuario, $adminId)
    {
        $data = [
            'idlibro'       => $idLibro,
            'dniUsuario'    => $dniUsuario,
            'fechaPrestamo' => date('Y-m-d'),
            'fechaVence'    => date('Y-m-d', strtotime('+14 days')),
        ];

        if (!$this->save($data)) {
            throw new \RuntimeException('No se pudo registrar el préstamo.');
        }

        return $this->getInsertID();
    }

    /**
     * Registra la devolución de un préstamo
     */
    public function registrarDevolucion($id)
    {
        $registro = $this->find($id);
        if (!$registro) {
            throw new \RuntimeException('Préstamo no encontrado.');
        }

        if (!empty($registro['fechaDevolucion'])) {
            throw new \RuntimeException('Este préstamo ya ha sido devuelto.');
        }

        if (!$this->update($id, ['fechaDevolucion' => date('Y-m-d')])) {
            throw new \RuntimeException('No se pudo registrar la devolución.');
        }

        return true;
    }

    /**
     * Renueva un préstamo (extiende la fecha de vencimiento)
     */
    public function renovar($id)
    {
        $registro = $this->find($id);
        if (!$registro) {
            throw new \RuntimeException('Préstamo no encontrado.');
        }

        if (!empty($registro['fechaDevolucion'])) {
            throw new \RuntimeException('No se puede renovar un préstamo que ya fue devuelto.');
        }

        $nuevaFechaVence = date('Y-m-d', strtotime('+14 days', strtotime($registro['fechaVence'])));

        if (!$this->update($id, ['fechaVence' => $nuevaFechaVence])) {
            throw new \RuntimeException('No se pudo renovar el préstamo.');
        }

        return true;
    }

    /**
     * Obtiene el historial de registros (préstamos) de un socio, formateado para la vista.
     */
    public function historialPorSocio(int $dni): array
    {
        $registros = $this->select('registros.*, libros.titulo')
            ->join('libros', 'libros.id = registros.idlibro', 'left')
            ->where('registros.dniUsuario', $dni)
            ->orderBy('registros.fechaPrestamo', 'DESC')
            ->findAll();

        $totalPrestamos = count($registros);
        $vencidos = 0;
        $prestamosFormateados = [];

        foreach ($registros as $p) {
            $estado = 'devuelto';
            if (empty($p['fechaDevolucion'])) {
                $estado = ($p['fechaVence'] < date('Y-m-d')) ? 'vencido' : 'activo';
                if ($estado === 'vencido') {
                    $vencidos++;
                }
            }

            $prestamosFormateados[] = [
                'id'                  => $p['id'],
                'titulo'              => $p['titulo'] ?? 'Sin título',
                'fecha_prestamo'      => $this->formatearFecha($p['fechaPrestamo']),
                'fecha_vencimiento'   => $this->formatearFecha($p['fechaVence']),
                'fecha_devolucion'    => $p['fechaDevolucion'] ? $this->formatearFecha($p['fechaDevolucion']) : null,
                'estado'              => $estado,
            ];
        }

        return [
            'total_prestamos' => $totalPrestamos,
            'vencidos'        => $vencidos,
            'prestamos'       => $prestamosFormateados,
        ];
    }

    private function formatearFecha($fecha)
    {
        if (empty($fecha)) {
            return null;
        }

        $dt = new \DateTime($fecha);
        return $dt->format('d/m/Y');
    }
}
