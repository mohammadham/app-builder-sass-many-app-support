<?php namespace App\Models;

use CodeIgniter\Model;

class AppConfigsModel extends Model
{
    protected $table         = 'app_configs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'app_id',
        'template_id',
        'config_data',
        'locked_fields',
        'is_locked',
        'created_at',
        'updated_at'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get config by app ID
     * @param int $appId
     * @return array|null
     */
    public function getByAppId(int $appId): ?array
    {
        return $this->where('app_id', $appId)->first();
    }

    /**
     * Check if config is locked
     * @param int $appId
     * @return bool
     */
    public function isLocked(int $appId): bool
    {
        $config = $this->getByAppId($appId);
        return $config ? (bool) $config['is_locked'] : false;
    }

    /**
     * Get locked fields for an app
     * @param int $appId
     * @return array
     */
    public function getLockedFields(int $appId): array
    {
        $config = $this->getByAppId($appId);
        if (!$config || !$config['locked_fields']) {
            return [];
        }
        return json_decode($config['locked_fields'], true) ?? [];
    }

    /**
     * Lock the configuration
     * @param int $appId
     * @param array $fieldsToLock
     * @return bool
     */
    public function lockConfig(int $appId, array $fieldsToLock = []): bool
    {
        $config = $this->getByAppId($appId);
        if (!$config) {
            return false;
        }

        return $this->update($config['id'], [
            'is_locked' => 1,
            'locked_fields' => json_encode($fieldsToLock)
        ]);
    }
}
