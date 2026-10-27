<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'setting_group', 'setting_key', 'setting_value', 'setting_type', 'description'
    ];
    protected $useTimestamps = false;

    public function getGrouped()
    {
        $all = $this->findAll();
        $grouped = [];
        foreach ($all as $item) {
            $grouped[$item['setting_group']][$item['setting_key']] = $item;
        }
        return $grouped;
    }

    public function updateValue($key, $value, $group = 'general', $type = 'string', $description = '')
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->where('setting_key', $key)->set([
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ])->update();
        } else {
            return $this->insert([
                'setting_group' => $group,
                'setting_key'   => $key,
                'setting_value' => $value,
                'setting_type'  => $type,
                'description'   => $description,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
