<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuyerLegalDocumentsColumns extends Migration
{
    public function up()
    {
        $fields = [
            'business_field' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'province',
            ],
            'website' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'phone',
            ],
            'npwp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nib',
            ],
            'nib_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'npwp_file',
            ],
            'deed_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nib_file',
            ],
            'id_card_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'deed_file',
            ],
            'sppkp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'id_card_file',
            ],
        ];

        $existingColumns = $this->db->getFieldNames('companies');
        $toAdd = [];
        foreach ($fields as $col => $def) {
            if (!in_array($col, $existingColumns, true)) {
                $toAdd[$col] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('companies', $toAdd);
        }
    }

    public function down()
    {
        $cols = ['business_field', 'website', 'npwp_file', 'nib_file', 'deed_file', 'id_card_file', 'sppkp_file'];
        $existingColumns = $this->db->getFieldNames('companies');
        foreach ($cols as $col) {
            if (in_array($col, $existingColumns, true)) {
                $this->forge->dropColumn('companies', $col);
            }
        }
    }
}

