<?php namespace App\Models;

use CodeIgniter\Model;

class SiteSettingsModel extends Model
{
    protected $table         = 'site_settings';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_type',
        'updated_at'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    /**
     * Get setting by key
     * @param string $key
     * @return mixed
     */
    public function getSetting(string $key)
    {
        $setting = $this->where('setting_key', $key)->first();
        if (!$setting) {
            return null;
        }

        // Parse based on type
        switch ($setting['setting_type']) {
            case 'boolean':
                return (bool) $setting['setting_value'];
            case 'json':
                return json_decode($setting['setting_value'], true);
            default:
                return $setting['setting_value'];
        }
    }

    /**
     * Update or create setting
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    public function updateSetting(string $key, $value, string $type = 'text'): bool
    {
        // Convert value based on type
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        $existing = $this->where('setting_key', $key)->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at' => time()
            ]);
        } else {
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => $type,
                'updated_at' => time()
            ]) !== false;
        }
    }

    /**
     * Get all settings as key-value array
     * @return array
     */
    public function getAllSettings(): array
    {
        $settings = $this->findAll();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->getSetting($setting['setting_key']);
        }
        return $result;
    }
}
