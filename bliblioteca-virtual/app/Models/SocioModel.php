<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioModel extends Model
{
    protected $table            = 'socios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'nombre', 'apellido', 'dni', 'email', 'password_hash',
        'telefono', 'telegram_chat_id', 'whatsapp_numero', 'estado', 'fecha_alta',
    ];

    protected $validationRules = [
        'nombre'   => 'required|max_length[120]',
        'apellido' => 'required|max_length[120]',
        'dni'      => 'required|max_length[20]|is_unique[socios.dni,id,{id}]',
        'email'    => 'required|valid_email|is_unique[socios.email,id,{id}]',
    ];

    /** Historial completo de un socio: préstamos pasados, sanciones (vencidos), libros leídos */
    public function historial(int $socioId): array
    {
        $db = \Config\Database::connect();

        $prestamos = $db->table('prestamos p')
            ->select('p.*, l.titulo, l.autor, e.codigo_inventario')
            ->join('ejemplares e', 'e.id = p.ejemplar_id')
            ->join('libros l', 'l.id = e.libro_id')
            ->where('p.socio_id', $socioId)
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResultArray();

        return [
            'prestamos'       => $prestamos,
            'total_prestamos' => count($prestamos),
            'vencidos'        => count(array_filter($prestamos, fn ($p) => $p['estado'] === 'vencido')),
        ];
    }
}
