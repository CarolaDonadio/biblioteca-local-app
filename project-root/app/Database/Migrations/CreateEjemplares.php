<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEjemplares extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'libro_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'codigo_inventario' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'ubicacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['disponible', 'prestado', 'reservado', 'perdido', 'danado', 'baja'],
                'default'    => 'disponible',
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codigo_inventario', 'uk_ejemplares_codigo');
        $this->forge->addForeignKey('libro_id', 'libros', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('ejemplares');
    }

    public function down()
    {
        $this->forge->dropTable('ejemplares', true);
    }
}
