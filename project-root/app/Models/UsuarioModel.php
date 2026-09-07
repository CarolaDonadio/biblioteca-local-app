<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'dni';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'dni',
        'nombre_completo',
        'telefono',
        'mail',
        'password_hash',
        'perfil',
        'estado',
        'ultimo_login',
    ];
    protected $validationRules = [
        'nombre_completo' => 'required|max_length[120]',
        'mail'            => 'required|valid_email|is_unique[usuarios.mail,id,{id}]',
        'perfil'          => 'in_list[superadmin,bibliotecario]',
    ];

    public function verificarCredenciales(string $email, string $password): ?array
    {
        $usuario = $this->where('mail', $email)->where('estado', 'activo')->first();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $this->update($usuario['dni'], ['ultimo_login' => date('Y-m-d H:i:s')]);
            return $usuario;
        }

        return null;
    }
}
