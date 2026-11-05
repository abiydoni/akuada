<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaxUnpaidNotesSetting extends Migration
{
    public function up()
    {
        $existing = $this->db->table('system_settings')->where('setting_key', 'max_unpaid_notes_limit')->get()->getRowArray();
        if (!$existing) {
            $this->db->table('system_settings')->insert([
                'setting_group' => 'payment',
                'setting_key'   => 'max_unpaid_notes_limit',
                'setting_value' => '3',
                'setting_type'  => 'number',
                'description'   => 'Batas maksimal jumlah nota/PO belum terbayar dalam sebulan untuk fasilitas kredit tempo (TOP)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->db->table('system_settings')->where('setting_key', 'max_unpaid_notes_limit')->delete();
    }
}

