<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'type', 'company_name', 'legal_type', 'npwp', 'nib', 'address',
        'city', 'province', 'phone', 'email', 'pic_name', 'pic_phone',
        'pic_email', 'bank_name', 'bank_account_number', 'bank_account_holder',
        'credit_limit_top', 'credit_used_top', 'commission_rate', 'is_verified'
    ];
    protected $useTimestamps = true;

    public function getVendors()
    {
        return $this->where('type', 'vendor')->orderBy('company_name', 'ASC')->findAll();
    }

    public function getBuyers()
    {
        return $this->where('type', 'buyer')->orderBy('company_name', 'ASC')->findAll();
    }
}
