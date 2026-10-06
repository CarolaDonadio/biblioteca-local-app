<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSistemaChannelToNotificaciones extends Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE notificaciones MODIFY canal ENUM('sistema', 'telegram', 'whatsapp', 'email') NOT NULL"
        );
    }

    public function down()
    {
        $avisosInternos = $this->db->table('notificaciones')
            ->where('canal', 'sistema')
            ->countAllResults();

        if ($avisosInternos > 0) {
            throw new \RuntimeException('Cannot remove the sistema channel while internal notifications exist.');
        }

        $this->db->query(
            "ALTER TABLE notificaciones MODIFY canal ENUM('telegram', 'whatsapp', 'email') NOT NULL"
        );
    }
}
