<?php namespace App\Models;

use CodeIgniter\Model;

class TranslationsModel extends Model
{
    protected $table         = 'translations';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'lang_code',
        'translation_key',
        'translation_value',
        'section'
    ];
    protected $returnType    = 'array';

    /**
     * Get all translations for a language
     * @param string $langCode
     * @param string|null $section
     * @return array
     */
    public function getByLanguage(string $langCode, ?string $section = null): array
    {
        $builder = $this->where('lang_code', $langCode);
        if ($section) {
            $builder->where('section', $section);
        }
        return $builder->findAll();
    }

    /**
     * Get translations as key-value array
     * @param string $langCode
     * @param string|null $section
     * @return array
     */
    public function getAsKeyValue(string $langCode, ?string $section = null): array
    {
        $translations = $this->getByLanguage($langCode, $section);
        $result = [];
        foreach ($translations as $trans) {
            $result[$trans['translation_key']] = $trans['translation_value'];
        }
        return $result;
    }

    /**
     * Update or create translation
     * @param string $langCode
     * @param string $key
     * @param string $value
     * @param string $section
     * @return bool
     */
    public function upsertTranslation(string $langCode, string $key, string $value, string $section = 'frontend'): bool
    {
        $existing = $this->where('lang_code', $langCode)
                         ->where('translation_key', $key)
                         ->first();

        if ($existing) {
            return $this->update($existing['id'], ['translation_value' => $value]);
        } else {
            return $this->insert([
                'lang_code' => $langCode,
                'translation_key' => $key,
                'translation_value' => $value,
                'section' => $section
            ]) !== false;
        }
    }
}
