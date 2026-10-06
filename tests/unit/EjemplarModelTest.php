<?php

use App\Models\EjemplarModel;
use CodeIgniter\Test\CIUnitTestCase;

final class EjemplarModelTest extends CIUnitTestCase
{
    public function testReportePorEstadoCompletaEstadosFaltantes(): void
    {
        $filas = [
            ['estado' => 'disponible', 'cantidad' => 12],
            ['estado' => 'prestado', 'cantidad' => 4],
        ];

        $resultado = EjemplarModel::normalizarReportePorEstado($filas);

        $this->assertSame(
            ['disponible', 'prestado', 'reservado', 'perdido', 'danado', 'baja'],
            array_column($resultado, 'estado')
        );

        $this->assertSame(12, $resultado[0]['cantidad']);
        $this->assertSame(4, $resultado[1]['cantidad']);
        $this->assertSame(0, $resultado[2]['cantidad']);
        $this->assertSame(0, $resultado[3]['cantidad']);
    }
}
