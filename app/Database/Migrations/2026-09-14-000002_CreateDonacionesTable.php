<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDonacionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'donante' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 30],
            'descripcion' => ['type' => 'TEXT'],
            'cantidad' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'fecha_donacion' => ['type' => 'DATE'],
            'estado' => ['type' => 'ENUM', 'constraint' => ['recibida', 'pendiente', 'rechazada'], 'default' => 'recibida'],
            'observaciones' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['estado', 'fecha_donacion']);
        $this->forge->createTable('donaciones');
    }

    public function down()
    {
        $this->forge->dropTable('donaciones', true);
    }
}