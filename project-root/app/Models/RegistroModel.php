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

    /** Días de duración por defecto de un préstamo. */
    public const DIAS_PRESTAMO = 14;

    /**
     * Obtiene todos los préstamos activos (sin devolución)
     */
    public function activos(): array
    {
        return $this->selectConRelaciones()
                    ->where('registros.fechaDevolucion', null)
                    ->orderBy('registros.fechaVence', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene todos los préstamos vencidos (sin devolución y fecha vencimiento pasada)
     */
    public function vencidos(): array
    {
        return $this->selectConRelaciones()
                    ->where('registros.fechaDevolucion', null)
                    ->where('registros.fechaVence <', date('Y-m-d'))
                    ->orderBy('registros.fechaVence', 'ASC')
                    ->findAll();
    }

    private function selectConRelaciones(): self
    {
        return $this->select('registros.*, libros.titulo, libros.autor, libros.isbn,'
                . ' usuarios.dni AS socio_dni, usuarios.nombre_completo AS socio_nombre')
            ->join('libros', 'libros.id = registros.idlibro', 'left')
            ->join('usuarios', 'usuarios.dni = registros.dniUsuario', 'left');
    }

    /**
     * Cantidad de ejemplares de un libro que están prestados en este momento.
     */
    public function prestadosDeLibro(int $idLibro): int
    {
        return $this->where('idlibro', $idLibro)
                    ->where('fechaDevolucion', null)
                    ->countAllResults();
    }

    /**
     * Registra un nuevo préstamo asignando un ejemplar del libro al socio.
     */
    public function registrarPrestamo($idLibro, $dniUsuario, $adminId = null)
    {
        $idLibro    = (int) $idLibro;
        $dniUsuario = (int) $dniUsuario;

        $libro = (new LibroModel())->find($idLibro);
        if (! $libro) {
            throw new \RuntimeException('El libro seleccionado no existe.');
        }

        $socio = (new SocioModel())->where('perfil', 'socio')->where('dni', $dniUsuario)->first();
        if (! $socio) {
            throw new \RuntimeException('El socio seleccionado no existe.');
        }

        if ($socio['estado'] !== 'activo') {
            throw new \RuntimeException('El socio está suspendido y no puede retirar ejemplares.');
        }

        if ($this->prestadosDeLibro($idLibro) >= (int) $libro['cantidad']) {
            throw new \RuntimeException('No hay ejemplares disponibles de «' . $libro['titulo'] . '».');
        }

        $yaLoTiene = $this->where('idlibro', $idLibro)
                          ->where('dniUsuario', $dniUsuario)
                          ->where('fechaDevolucion', null)
                          ->countAllResults();

        if ($yaLoTiene > 0) {
            throw new \RuntimeException('El socio ya tiene un ejemplar de este libro en préstamo.');
        }

        $data = [
            'idlibro'       => $idLibro,
            'dniUsuario'    => $dniUsuario,
            'fechaPrestamo' => date('Y-m-d'),
            'fechaVence'    => date('Y-m-d', strtotime('+' . self::DIAS_PRESTAMO . ' days')),
        ];

        if (! $this->insert($data)) {
            throw new \RuntimeException('No se pudo registrar el préstamo.');
        }

        $id = $this->getInsertID();
        $this->sincronizarDisponibilidad($idLibro);

        return $id;
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

        $this->sincronizarDisponibilidad((int) $registro['idlibro']);

        return true;
    }

    /**
     * Mantiene el flag `libros.disponible` en sintonía con los ejemplares libres.
     */
    private function sincronizarDisponibilidad(int $idLibro): void
    {
        $libros = new LibroModel();
        $libro  = $libros->find($idLibro);
        if (! $libro) {
            return;
        }

        $libres = max(0, (int) $libro['cantidad'] - $this->prestadosDeLibro($idLibro));
        $libros->update($idLibro, ['disponible' => $libres > 0 ? 1 : 0]);
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

        $base = max(strtotime($registro['fechaVence']), strtotime(date('Y-m-d')));
        $nuevaFechaVence = date('Y-m-d', strtotime('+' . self::DIAS_PRESTAMO . ' days', $base));

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
