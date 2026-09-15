<?php

use App\Controllers\Admin\LibroController;
use App\Models\LibroModel;
use App\Models\NotificacionModel;
use App\Models\PromocionModel;
use App\Models\ReservaModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Test\CIUnitTestCase;

final class CriticalFlowsTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('Las pruebas de integración requieren la extensión PHP sqlite3.');
        }

        $this->db = db_connect('tests');
        $this->db->query('DROP TABLE IF EXISTS reservas');
        $this->db->query('DROP TABLE IF EXISTS registros');
        $this->db->query('DROP TABLE IF EXISTS notificaciones');
        $this->db->query('DROP TABLE IF EXISTS promociones');
        $this->db->query('DROP TABLE IF EXISTS libros');
        $this->db->query('DROP TABLE IF EXISTS usuarios');

        $this->db->query('CREATE TABLE usuarios (dni INTEGER PRIMARY KEY, nombre_completo VARCHAR(150) NOT NULL, mail VARCHAR(150) NOT NULL, password_hash VARCHAR(255) NOT NULL, perfil VARCHAR(20) NOT NULL, estado VARCHAR(20) NOT NULL, ultimo_login DATETIME NULL, created_at DATETIME NULL, updated_at DATETIME NULL)');
        $this->db->query('CREATE TABLE libros (id INTEGER PRIMARY KEY AUTOINCREMENT, titulo VARCHAR(255) NOT NULL, autor VARCHAR(150) NOT NULL, cantidad INTEGER NOT NULL, disponible INTEGER NOT NULL DEFAULT 1)');
        $this->db->query('CREATE TABLE registros (id INTEGER PRIMARY KEY AUTOINCREMENT, idlibro INTEGER NOT NULL, dniUsuario INTEGER NOT NULL, fechaPrestamo DATE NOT NULL, fechaVence DATE NOT NULL, fechaDevolucion DATE NULL)');
        $this->db->query('CREATE TABLE reservas (id INTEGER PRIMARY KEY AUTOINCREMENT, libro_id INTEGER NOT NULL, socio_id INTEGER NOT NULL, fecha_solicitud DATETIME NOT NULL, estado VARCHAR(20) NOT NULL, fecha_confirmacion DATETIME NULL, fecha_cancelacion DATETIME NULL, fecha_completada DATETIME NULL, procesada_por INTEGER NULL)');
        $this->db->query('CREATE TABLE notificaciones (id INTEGER PRIMARY KEY AUTOINCREMENT, dniUsuario INTEGER NOT NULL, canal VARCHAR(20) NOT NULL, tipo VARCHAR(80) NOT NULL, mensaje TEXT NOT NULL, estado_entrega VARCHAR(20) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL)');
        $this->db->query('CREATE TABLE promociones (id INTEGER PRIMARY KEY AUTOINCREMENT, titulo VARCHAR(255) NOT NULL, descripcion TEXT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, imagen_url VARCHAR(255) NULL, condiciones TEXT NULL, created_at DATETIME NULL, updated_at DATETIME NULL)');

        $password = password_hash('secreto', PASSWORD_DEFAULT);
        $this->db->table('usuarios')->insertBatch([
            ['dni' => 31001001, 'nombre_completo' => 'Bibliotecario', 'mail' => 'admin@test.local', 'password_hash' => $password, 'perfil' => 'bibliotecario', 'estado' => 'activo'],
            ['dni' => 31001002, 'nombre_completo' => 'Socio Activo', 'mail' => 'socio@test.local', 'password_hash' => $password, 'perfil' => 'socio', 'estado' => 'activo'],
            ['dni' => 31001003, 'nombre_completo' => 'Socio Suspendido', 'mail' => 'suspendido@test.local', 'password_hash' => $password, 'perfil' => 'socio', 'estado' => 'suspendido'],
        ]);
        $this->db->table('libros')->insert(['titulo' => 'Libro crítico', 'autor' => 'Autora', 'cantidad' => 2, 'disponible' => 1]);
    }

    public function testAdminAuthenticationRequiresBibliotecarioAndValidPassword(): void
    {
        $model = new UsuarioModel();

        $this->assertSame(31001001, $model->verificarCredencialesAdmin('admin@test.local', 'secreto')['dni']);
        $this->assertNull($model->verificarCredencialesAdmin('socio@test.local', 'secreto'));
        $this->assertNull($model->verificarCredencialesAdmin('admin@test.local', 'incorrecta'));
    }

    public function testReservationRejectsDuplicateActiveReservation(): void
    {
        $model = new ReservaModel();
        $reservationId = $model->solicitar(1, 31001002);

        $this->assertGreaterThan(0, $reservationId);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Ya tenés una reserva activa');
        $model->solicitar(1, 31001002);
    }

    public function testAvailabilitySubtractsOnlyUndevolvedLoans(): void
    {
        $this->db->table('registros')->insert([
            'idlibro' => 1,
            'dniUsuario' => 31001002,
            'fechaPrestamo' => date('Y-m-d'),
            'fechaVence' => date('Y-m-d', strtotime('+7 days')),
        ]);

        $this->db->table('registros')->insert([
            'idlibro' => 1,
            'dniUsuario' => 31001002,
            'fechaPrestamo' => date('Y-m-d', strtotime('-10 days')),
            'fechaVence' => date('Y-m-d', strtotime('-3 days')),
            'fechaDevolucion' => date('Y-m-d'),
        ]);

        $this->assertSame(1, (new LibroModel())->disponiblesDe(1));
    }

    public function testNotificationRetryReturnsPendingState(): void
    {
        $id = $this->db->table('notificaciones')->insert([
            'dniUsuario' => 31001002,
            'canal' => 'email',
            'tipo' => 'aviso',
            'mensaje' => 'Mensaje',
            'estado_entrega' => 'fallido',
        ]);

        $model = new NotificacionModel();
        $this->assertTrue($model->reintentar((int) $id));
        $this->assertSame('pendiente', $model->find($id)['estado_entrega']);
        $this->assertFalse($model->reintentar(999));
    }

    public function testOnlyCurrentPromotionsArePublished(): void
    {
        $this->db->table('promociones')->insertBatch([
            ['titulo' => 'Vigente', 'fecha_inicio' => date('Y-m-d', strtotime('-1 day')), 'fecha_fin' => date('Y-m-d', strtotime('+1 day'))],
            ['titulo' => 'Futura', 'fecha_inicio' => date('Y-m-d', strtotime('+1 day')), 'fecha_fin' => date('Y-m-d', strtotime('+2 days'))],
            ['titulo' => 'Vencida', 'fecha_inicio' => date('Y-m-d', strtotime('-2 days')), 'fecha_fin' => date('Y-m-d', strtotime('-1 day'))],
        ]);

        $promotions = (new PromocionModel())->vigentes();

        $this->assertCount(1, $promotions);
        $this->assertSame('Vigente', $promotions[0]['titulo']);
    }

    public function testMultimediaUploadRejectsInvalidFile(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getFile')->with('archivo')->willReturn(null);
        $controller = new LibroController();
        $property = new ReflectionProperty($controller, 'request');
        $property->setValue($controller, $request);

        $response = $controller->subirMultimedia(1);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/admin/libros/1/multimedia', (string) $response->getHeaderLine('Location'));
    }

}