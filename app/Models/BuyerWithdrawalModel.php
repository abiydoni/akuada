<?php

namespace App\Models;

use CodeIgniter\Model;

class BuyerWithdrawalModel extends Model
{
    protected $table = 'buyer_withdrawals';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'withdrawal_number', 'company_id', 'user_id', 'amount',
        'bank_name', 'bank_account_number', 'bank_account_holder',
        'status', 'buyer_notes', 'admin_notes', 'transfer_proof',
        'processed_by', 'processed_at'
    ];
    protected $useTimestamps = true;

    /**
     * Get list of withdrawals with company and user details
     */
    public function getWithdrawalsWithCompany(?int $companyId = null, ?string $status = null)
    {
        $builder = $this->select('buyer_withdrawals.*, companies.company_name, companies.legal_type, companies.cashback_balance, users.name as user_name, users.email as user_email')
                        ->join('companies', 'companies.id = buyer_withdrawals.company_id', 'left')
                        ->join('users', 'users.id = buyer_withdrawals.user_id', 'left');

        if ($companyId !== null) {
            $builder->where('buyer_withdrawals.company_id', $companyId);
        }

        if ($status !== null && in_array($status, ['pending', 'completed', 'rejected'])) {
            $builder->where('buyer_withdrawals.status', $status);
        }

        return $builder->orderBy('buyer_withdrawals.created_at', 'DESC')->findAll();
    }
}

