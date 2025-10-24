<?php namespace App\Models;

use CodeIgniter\Model;

class LanguagesModel extends Model
{
    protected $table         = 'languages';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'code',
        'name',
        'direction',
        'is_default',
        'status'
    ];
    protected $returnType    = 'array';

    /**
     * Get all active languages
     * @return array
     */
    public function getActiveLanguages(): array
    {
        return $this->where('status', 1)->findAll();
    }

    /**
     * Get default language
     * @return array|null
     */
    public function getDefaultLanguage(): ?array
    {
        return $this->where('is_default', 1)->first();
    }

    /**
     * Get language by code
     * @param string $code
     * @return array|null
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }

    /**
     * Set default language
     * @param string $code
     * @return bool
     */
    public function setDefault(string $code): bool
    {
        // Remove default from all
        $this->update(null, ['is_default' => 0]);
        
        // Set new default
        $lang = $this->getByCode($code);
        if ($lang) {
            return $this->update($lang['id'], ['is_default' => 1]);
        }
        return false;
    }
}
