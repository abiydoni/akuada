<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'email', 'password', 'role', 'phone', 'company_id',
        'position', 'avatar', 'status'
    ];
    protected $useTimestamps = true;

    public function getUserWithCompany($userId)
    {
        return $this->select('users.*, companies.company_name, companies.type as company_type, companies.legal_type, companies.npwp, companies.nib, companies.commission_rate, companies.credit_limit_top, companies.credit_used_top')
                    ->join('companies', 'companies.id = users.company_id', 'left')
                    ->where('users.id', $userId)
                    ->first();
    }
}
