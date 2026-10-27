<?php

namespace App\Models;

use CodeIgniter\Model;

class RfqModel extends Model
{
    protected $table = 'rfqs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'rfq_number', 'customer_company_id', 'vendor_company_id', 'title',
        'notes', 'target_delivery_date', 'status', 'total_estimate'
    ];
    protected $useTimestamps = true;

    public function getDetailedRfqs($companyId = null, $role = null)
    {
        $builder = $this->select('rfqs.*, buyer.company_name as buyer_name, vendor.company_name as vendor_name')
                        ->join('companies as buyer', 'buyer.id = rfqs.customer_company_id', 'left')
                        ->join('companies as vendor', 'vendor.id = rfqs.vendor_company_id', 'left');

        if ($role === 'vendor' && $companyId) {
            $builder->groupStart()
                    ->where('rfqs.vendor_company_id', $companyId)
                    ->orWhere('rfqs.vendor_company_id', null)
                    ->groupEnd();
        } elseif ($role === 'customer' && $companyId) {
            $builder->where('rfqs.customer_company_id', $companyId);
        }

        return $builder->orderBy('rfqs.id', 'DESC')->findAll();
    }

    public function getRfqWithItems($rfqId)
    {
        $rfq = $this->select('rfqs.*, buyer.company_name as buyer_name, buyer.npwp as buyer_npwp, buyer.pic_name as buyer_pic, buyer.phone as buyer_phone, vendor.company_name as vendor_name')
                    ->join('companies as buyer', 'buyer.id = rfqs.customer_company_id', 'left')
                    ->join('companies as vendor', 'vendor.id = rfqs.vendor_company_id', 'left')
                    ->where('rfqs.id', $rfqId)
                    ->first();

        if ($rfq) {
            $itemModel = new RfqItemModel();
            $rfq['items'] = $itemModel->where('rfq_id', $rfq['id'])->findAll();
        }

        return $rfq;
    }
}
