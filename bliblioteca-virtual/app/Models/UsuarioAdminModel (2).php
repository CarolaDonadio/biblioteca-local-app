<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioAdminModel extends Model
{
    protected $table            = 'usuarios_admin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nombre', 'email', 'password_hash', 'rol', 'activo', 'ultimo_login',
    ];

    protected $validationRules = [
        'nombre' => 'required|max_length[120]',
        'email'  => 'required|valid_email|is_unique[usuarios_admin.email,id,{id}]',
        'rol'    => 'in_list[superadmin,bibliotecario]',
    ];

    public function verificarCredenciales(string $email, string $password): ?array
    {
        $usuario = $this->where('email', $email)->where('activo', 1)->first();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $this->update($usuario['id'], ['ultimo_login' => date('Y-m-d H:i:s')]);
            return $usuario;
        }

        return null;
    }
}
