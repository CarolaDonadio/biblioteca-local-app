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

    // Reglas para altas/ediciones generales (panel admin).
    // Nota: la PK de esta tabla es "dni", no "id" -> is_unique debe ignorar por "dni".
    protected $validationRules = [
        'dni'             => 'required|numeric|is_unique[usuarios.dni,dni,{dni}]',
        'nombre_completo' => 'required|max_length[120]',
        'mail'            => 'required|valid_email|is_unique[usuarios.mail,dni,{dni}]',
        'perfil'          => 'required|in_list[socio,bibliotecario]',
    ];

    protected $validationMessages = [
        'dni' => [
            'is_unique' => 'Ya existe un usuario registrado con ese DNI.',
        ],
        'mail' => [
            'is_unique' => 'Ese email ya está registrado.',
        ],
    ];

    // Reglas específicas para el alta pública de socios desde /socio/registro.
    protected $validationRulesRegistroSocio = [
        'dni'             => 'required|numeric|min_length[7]|max_length[8]|is_unique[usuarios.dni,dni,{dni}]',
        'nombre_completo' => 'required|min_length[3]|max_length[120]',
        'mail'            => 'required|valid_email|is_unique[usuarios.mail,dni,{dni}]',
        'telefono'        => 'permit_empty|max_length[30]',
        'password'        => 'required|min_length[6]',
    ];

    /**
     * Verifica credenciales de acceso.
     *
     * @param list<string>|null $perfilesPermitidos Si se indica, sólo autentica usuarios con ese/esos perfiles
     *                                               (evita que un socio entre al panel admin o viceversa).
     */
    public function verificarCredenciales(string $email, string $password, ?array $perfilesPermitidos = null): ?array
    {
        $builder = $this->where('mail', $email)->where('estado', 'activo');

        if ($perfilesPermitidos !== null) {
            $builder = $builder->whereIn('perfil', $perfilesPermitidos);
        }

        $usuario = $builder->first();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            $this->update($usuario['dni'], ['ultimo_login' => date('Y-m-d H:i:s')]);
            return $usuario;
        }

        return null;
    }

    /**
     * Valida y crea un socio nuevo a partir de los datos del formulario de registro público.
     * Aplica password_hash() antes de guardar; nunca persiste la contraseña en texto plano.
     *
     * @return array{success: bool, errors?: array<string,string>, dni?: int}
     */
    public function crearSocio(array $datos): array
    {
        $reglasOriginales      = $this->validationRules;
        $this->validationRules = $this->validationRulesRegistroSocio;

        $valido = $this->validate($datos);

        $this->validationRules = $reglasOriginales;

        if (! $valido) {
            return ['success' => false, 'errors' => $this->errors()];
        }

        $registro = [
            'dni'             => (int) $datos['dni'],
            'nombre_completo' => trim($datos['nombre_completo']),
            'telefono'        => $datos['telefono'] ?? null,
            'mail'            => $datos['mail'],
            'password_hash'   => password_hash($datos['password'], PASSWORD_DEFAULT),
            'perfil'          => 'socio',
            'estado'          => 'activo',
        ];

        // Se saltea acá la validación del Model porque ya se validó arriba con las
        // reglas de registro (las reglas por default no conocen el campo "password").
        $this->skipValidation(true)->insert($registro);
        $this->skipValidation(false);

        return ['success' => true, 'dni' => $registro['dni']];
    }
}
