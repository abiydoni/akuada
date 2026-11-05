<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuyerCashbackAndWithdrawals extends Migration
{
    public function up()
    {
        // 1. Add cashback_balance column to companies table if not exists
        if (!$this->db->fieldExists('cashback_balance', 'companies')) {
            $this->forge->addColumn('companies', [
                'cashback_balance' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                    'after'      => 'credit_used_top',
                ],
            ]);
        }

        // 2. Add cashback columns to orders table
        $orderFields = [];
        if (!$this->db->fieldExists('cashback_percent', 'orders')) {
            $orderFields['cashback_percent'] = [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'after'      => 'total_amount',
            ];
        }
        if (!$this->db->fieldExists('cashback_amount', 'orders')) {
            $orderFields['cashback_amount'] = [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'after'      => 'cashback_percent',
            ];
        }
        if (!$this->db->fieldExists('is_cashback_awarded', 'orders')) {
            $orderFields['is_cashback_awarded'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'cashback_amount',
            ];
        }
        if (!empty($orderFields)) {
            $this->forge->addColumn('orders', $orderFields);
        }

        // 3. Create buyer_withdrawals table
        if (!$this->db->tableExists('buyer_withdrawals')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'withdrawal_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'unique'     => true,
                ],
                'company_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                ],
                'bank_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'bank_account_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'bank_account_holder' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'completed', 'rejected'],
                    'default'    => 'pending',
                ],
                'buyer_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'admin_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'transfer_proof' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'processed_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'processed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('company_id');
            $this->forge->createTable('buyer_withdrawals', true);
        }

        // 4. Create buyer_cashback_mutations table
        if (!$this->db->tableExists('buyer_cashback_mutations')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'company_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'order_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'withdrawal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['credit', 'debit'],
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                ],
                'balance_after' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                ],
                'description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('company_id');
            $this->forge->createTable('buyer_cashback_mutations', true);
        }

        // 5. Ensure system_settings has buyer_cashback_percent
        $existingSetting = $this->db->table('system_settings')->where('setting_key', 'buyer_cashback_percent')->get()->getRowArray();
        if (!$existingSetting) {
            $this->db->table('system_settings')->insert([
                'setting_group' => 'fee',
                'setting_key'   => 'buyer_cashback_percent',
                'setting_value' => '1.0',
                'setting_type'  => 'number',
                'description'   => 'Persentase Cashback Saldo Pembeli (%) dari transaksi lunas (Paid)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('cashback_balance', 'companies')) {
            $this->forge->dropColumn('companies', 'cashback_balance');
        }
        if ($this->db->fieldExists('cashback_percent', 'orders')) {
            $this->forge->dropColumn('orders', 'cashback_percent');
        }
        if ($this->db->fieldExists('cashback_amount', 'orders')) {
            $this->forge->dropColumn('orders', 'cashback_amount');
        }
        if ($this->db->fieldExists('is_cashback_awarded', 'orders')) {
            $this->forge->dropColumn('orders', 'is_cashback_awarded');
        }
        $this->forge->dropTable('buyer_cashback_mutations', true);
        $this->forge->dropTable('buyer_withdrawals', true);
    }
}

