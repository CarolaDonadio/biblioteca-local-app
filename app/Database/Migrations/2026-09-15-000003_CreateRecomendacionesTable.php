<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRecomendacionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'socio_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'libro_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['socio_id', 'libro_id'], 'uk_recomendaciones_socio_libro');
        $this->forge->addForeignKey('socio_id', 'usuarios', 'dni', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('libro_id', 'libros', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('recomendaciones');
    }

    public function down()
    {
        $this->forge->dropTable('recomendaciones', true);
    }
}