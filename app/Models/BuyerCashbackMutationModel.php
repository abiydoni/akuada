<?php

namespace App\Models;

use CodeIgniter\Model;

class BuyerCashbackMutationModel extends Model
{
    protected $table = 'buyer_cashback_mutations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'order_id', 'withdrawal_id', 'type',
        'amount', 'balance_after', 'description', 'created_at'
    ];
    protected $useTimestamps = false;

    /**
     * Record a cashback mutation (credit or debit)
     */
    public function recordMutation(int $companyId, string $type, float $amount, float $balanceAfter, string $description, ?int $orderId = null, ?int $withdrawalId = null)
    {
        return $this->insert([
            'company_id'    => $companyId,
            'order_id'      => $orderId,
            'withdrawal_id' => $withdrawalId,
            'type'          => $type,
            'amount'        => $amount,
            'balance_after' => $balanceAfter,
            'description'   => $description,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get mutation logs for a company
     */
    public function getCompanyMutations(int $companyId, int $limit = 50)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }
}

