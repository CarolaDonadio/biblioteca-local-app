<?php

namespace App\Models;

use App\Libraries\AutomaticNotificationService;
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
    public const DIAS_AVISO_VENCIMIENTO = 3;

    public function proximosAVencerPorSocio(int $dni, int $dias = self::DIAS_AVISO_VENCIMIENTO): array
    {
        $fechaHoy = date('Y-m-d');
        $fechaLimite = date('Y-m-d', strtotime('+' . max(0, $dias) . ' days'));

        $prestamos = $this->select('registros.id, registros.fechaVence, libros.titulo')
            ->join('libros', 'libros.id = registros.idlibro', 'left')
            ->where('registros.dniUsuario', $dni)
            ->where('registros.fechaDevolucion', null)
            ->where('registros.fechaVence >=', $fechaHoy)
            ->where('registros.fechaVence <=', $fechaLimite)
            ->orderBy('registros.fechaVence', 'ASC')
            ->findAll();

        foreach ($prestamos as &$prestamo) {
            $prestamo['dias_restantes'] = (new \DateTimeImmutable($fechaHoy))
                ->diff(new \DateTimeImmutable($prestamo['fechaVence']))
                ->days;
        }
        unset($prestamo);

        return $prestamos;
    }

    /**
     * Obtiene todos los préstamos activos (sin devolución)
     */
    public function activos(string $busqueda = ''): array
    {
        $consulta = $this->selectConRelaciones()
            ->where('registros.fechaDevolucion', null);

        if ($busqueda !== '') {
            $consulta->groupStart()
                ->like('libros.titulo', $busqueda)
                ->orLike('registros.dniUsuario', $busqueda)
                ->groupEnd();
        }

        return $consulta->orderBy('registros.fechaVence', 'ASC')->findAll();
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
    public function registrarPrestamo($idLibro, $dniUsuario, $adminId = null, bool $notificar = true)
    {
        $idLibro    = (int) $idLibro;
        $dniUsuario = (int) $dniUsuario;
        $this->validarLibroYSocio($idLibro, $dniUsuario);

        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo registrar el préstamo.');
        }

        try {
            $ejemplares = new EjemplarModel();
            $libro = $ejemplares->bloquearLibro($idLibro);
            $this->validarPrestamoDuplicado($idLibro, $dniUsuario);

            if (! $ejemplares->tomarDisponible($idLibro, 'prestado')) {
                throw new \RuntimeException('No hay ejemplares disponibles para prestar de «' . $libro['titulo'] . '».');
            }

            $id = $this->crearRegistroPrestamo($idLibro, $dniUsuario);
            (new LibroModel())->sincronizarDisponibilidad($idLibro);

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo registrar el préstamo.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        if ($notificar) {
            (new AutomaticNotificationService())->notifyUser(
                $dniUsuario,
                'prestamo_creado',
                'Se registró el préstamo de «' . $libro['titulo'] . '». '
                    . 'La fecha de vencimiento es ' . date('d/m/Y', strtotime('+' . self::DIAS_PRESTAMO . ' days')) . '.'
            );
        }

        return $id;
    }

    /**
     * Crea el registro después de que ReservaModel convirtió su ejemplar
     * reservado a prestado dentro de la transacción que ya mantiene abierta.
     */
    public function registrarPrestamoDesdeReserva(int $idLibro, int $dniUsuario): int
    {
        $this->validarLibroYSocio($idLibro, $dniUsuario);
        $this->validarPrestamoDuplicado($idLibro, $dniUsuario);

        return $this->crearRegistroPrestamo($idLibro, $dniUsuario);
    }

    private function validarLibroYSocio(int $idLibro, int $dniUsuario): void
    {
        if (! (new LibroModel())->find($idLibro)) {
            throw new \RuntimeException('El libro seleccionado no existe.');
        }

        $socio = (new SocioModel())->where('perfil', 'socio')->where('dni', $dniUsuario)->first();
        if (! $socio) {
            throw new \RuntimeException('El socio seleccionado no existe.');
        }

        if ($socio['estado'] !== 'activo') {
            throw new \RuntimeException('El socio está suspendido y no puede retirar ejemplares.');
        }
    }

    private function validarPrestamoDuplicado(int $idLibro, int $dniUsuario): void
    {
        $yaLoTiene = $this->where('idlibro', $idLibro)
            ->where('dniUsuario', $dniUsuario)
            ->where('fechaDevolucion', null)
            ->countAllResults();

        if ($yaLoTiene > 0) {
            throw new \RuntimeException('El socio ya tiene un ejemplar de este libro en préstamo.');
        }
    }

    private function crearRegistroPrestamo(int $idLibro, int $dniUsuario): int
    {
        $data = [
            'idlibro'       => $idLibro,
            'dniUsuario'    => $dniUsuario,
            'fechaPrestamo' => date('Y-m-d'),
            'fechaVence'    => date('Y-m-d', strtotime('+' . self::DIAS_PRESTAMO . ' days')),
        ];

        if (! $this->insert($data)) {
            throw new \RuntimeException('No se pudo registrar el préstamo.');
        }

        return (int) $this->getInsertID();
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

        $idLibro = (int) $registro['idlibro'];
        if (! $this->db->transBegin()) {
            throw new \RuntimeException('No se pudo registrar la devolución.');
        }

        try {
            $ejemplares = new EjemplarModel();
            $ejemplares->bloquearLibro($idLibro);
            $registroBloqueado = $this->db->query(
                'SELECT * FROM registros WHERE id = ? FOR UPDATE',
                [$id]
            )->getRowArray();

            if (! $registroBloqueado || ! empty($registroBloqueado['fechaDevolucion'])) {
                throw new \RuntimeException('Este préstamo ya ha sido devuelto.');
            }

            if (! $ejemplares->liberarUno($idLibro, 'prestado')) {
                throw new \RuntimeException(
                    'No se pudo registrar la devolución: no hay un ejemplar marcado como prestado para este libro. Revise la consistencia del inventario.'
                );
            }

            $actualizado = $this->db->table('registros')
                ->where('id', (int) $id)
                ->where('fechaDevolucion', null)
                ->update(['fechaDevolucion' => date('Y-m-d')]);
            if (! $actualizado || $this->db->affectedRows() !== 1) {
                throw new \RuntimeException('No se pudo registrar la devolución.');
            }

            (new LibroModel())->sincronizarDisponibilidad($idLibro);

            if (! $this->db->transCommit()) {
                throw new \RuntimeException('No se pudo registrar la devolución.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        $libro = (new LibroModel())->find($idLibro);
        (new AutomaticNotificationService())->notifyUser(
            (int) $registro['dniUsuario'],
            'devolucion_registrada',
            'Se registró la devolución de «' . ($libro['titulo'] ?? 'tu libro') . '».'
        );

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

        $base = max(strtotime($registro['fechaVence']), strtotime(date('Y-m-d')));
        $nuevaFechaVence = date('Y-m-d', strtotime('+' . self::DIAS_PRESTAMO . ' days', $base));

        if (!$this->update($id, ['fechaVence' => $nuevaFechaVence])) {
            throw new \RuntimeException('No se pudo renovar el préstamo.');
        }

        $libro = (new LibroModel())->find((int) $registro['idlibro']);
        (new AutomaticNotificationService())->notifyUser(
            (int) $registro['dniUsuario'],
            'prestamo_renovado',
            'Se renovó el préstamo de «' . ($libro['titulo'] ?? 'tu libro') . '». '
                . 'La nueva fecha de vencimiento es ' . date('d/m/Y', strtotime($nuevaFechaVence)) . '.'
        );

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
