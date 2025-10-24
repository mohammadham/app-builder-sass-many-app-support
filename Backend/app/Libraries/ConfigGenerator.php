<?php namespace App\Libraries;

use Exception;

/**
 * ConfigGenerator Library
 * 
 * Generates configuration files for different templates based on JSON schema
 */
class ConfigGenerator
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Generate config file content based on template type and config data
     * 
     * @param array $template Template data from database
     * @param array $configData Configuration data from app_configs
     * @param array $appData Additional app data
     * @return array ['event' => bool, 'content' => string, 'path' => string]
     */
    public function generate(array $template, array $configData, array $appData): array
    {
        try {
            // Determine template type and generate appropriate config
            if ($template['is_primary']) {
                // Primary template: generate config.dart (existing format)
                return $this->generatePrimaryTemplateConfig($configData, $appData);
            } else {
                // New template: generate based on template requirements
                return $this->generateCustomTemplateConfig($template, $configData, $appData);
            }
        } catch (Exception $e) {
            return [
                'event' => false,
                'message' => 'Failed to generate config: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate config for primary (default) template
     * Maintains backward compatibility
     * 
     * @param array $configData
     * @param array $appData
     * @return array
     */
    private function generatePrimaryTemplateConfig(array $configData, array $appData): array
    {
        // This maintains the existing config.dart format
        // The actual template rendering will be done in CreatePackage.php
        
        return [
            'event' => true,
            'path' => 'lib/config/config.dart',
            'type' => 'dart',
            'use_existing_method' => true // Flag to use existing upload_config method
        ];
    }

    /**
     * Generate config for custom templates
     * 
     * @param array $template
     * @param array $configData
     * @param array $appData
     * @return array
     */
    private function generateCustomTemplateConfig(array $template, array $configData, array $appData): array
    {
        $schema = json_decode($template['config_schema'], true);
        
        if (!$schema) {
            return [
                'event' => false,
                'message' => 'Invalid template schema'
            ];
        }

        // Determine config format based on template
        $configFormat = $this->getConfigFormat($template);
        
        switch ($configFormat) {
            case 'dart':
                return $this->generateDartConfig($schema, $configData, $appData, $template);
            case 'json':
                return $this->generateJSONConfig($schema, $configData, $appData);
            case 'yaml':
                return $this->generateYAMLConfig($schema, $configData, $appData);
            default:
                return $this->generateDartConfig($schema, $configData, $appData, $template);
        }
    }

    /**
     * Determine config format from template
     * 
     * @param array $template
     * @return string
     */
    private function getConfigFormat(array $template): string
    {
        // Check if template has specified config format
        // Default to dart for Flutter templates
        return 'dart';
    }

    /**
     * Generate Dart configuration file
     * 
     * @param array $schema
     * @param array $configData
     * @param array $appData
     * @param array $template
     * @return array
     */
    private function generateDartConfig(array $schema, array $configData, array $appData, array $template): array
    {
        $dartCode = "// Auto-generated configuration file\n";
        $dartCode .= "// Generated: " . date('Y-m-d H:i:s') . "\n";
        $dartCode .= "// Template: " . ($template['name_en'] ?? 'Custom') . "\n\n";
        $dartCode .= "class AppConfig {\n";
        
        // Generate static constants from schema
        foreach ($schema as $field) {
            $fieldName = $field['name'];
            $fieldType = $this->getDartType($field['type']);
            $fieldValue = $configData[$fieldName] ?? $field['default'] ?? null;
            
            if ($fieldValue !== null) {
                $dartValue = $this->formatDartValue($fieldValue, $field['type']);
                $comment = $field['label_en'] ?? $fieldName;
                
                $dartCode .= "  // {$comment}\n";
                $dartCode .= "  static const {$fieldType} {$fieldName} = {$dartValue};\n\n";
            }
        }
        
        // Add app-specific data
        $dartCode .= "  // App Identity\n";
        $dartCode .= "  static const String appUid = \"{$appData['uid']}\";\n";
        $dartCode .= "  static const String appName = \"{$appData['name']}\";\n";
        
        $dartCode .= "}\n";
        
        return [
            'event' => true,
            'content' => $dartCode,
            'path' => 'lib/config/app_config.dart',
            'type' => 'dart'
        ];
    }

    /**
     * Generate JSON configuration file
     * 
     * @param array $schema
     * @param array $configData
     * @param array $appData
     * @return array
     */
    private function generateJSONConfig(array $schema, array $configData, array $appData): array
    {
        $jsonData = [
            'generated_at' => date('Y-m-d H:i:s'),
            'app_uid' => $appData['uid'],
            'app_name' => $appData['name'],
            'config' => []
        ];
        
        foreach ($schema as $field) {
            $fieldName = $field['name'];
            $jsonData['config'][$fieldName] = $configData[$fieldName] ?? $field['default'] ?? null;
        }
        
        return [
            'event' => true,
            'content' => json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'path' => 'assets/config.json',
            'type' => 'json'
        ];
    }

    /**
     * Generate YAML configuration file
     * 
     * @param array $schema
     * @param array $configData
     * @param array $appData
     * @return array
     */
    private function generateYAMLConfig(array $schema, array $configData, array $appData): array
    {
        $yamlContent = "# Auto-generated configuration\n";
        $yamlContent .= "# Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $yamlContent .= "app:\n";
        $yamlContent .= "  uid: \"{$appData['uid']}\"\n";
        $yamlContent .= "  name: \"{$appData['name']}\"\n\n";
        $yamlContent .= "config:\n";
        
        foreach ($schema as $field) {
            $fieldName = $field['name'];
            $fieldValue = $configData[$fieldName] ?? $field['default'] ?? null;
            
            if ($fieldValue !== null) {
                $yamlValue = $this->formatYAMLValue($fieldValue, $field['type']);
                $comment = $field['label_en'] ?? $fieldName;
                $yamlContent .= "  # {$comment}\n";
                $yamlContent .= "  {$fieldName}: {$yamlValue}\n";
            }
        }
        
        return [
            'event' => true,
            'content' => $yamlContent,
            'path' => 'config.yaml',
            'type' => 'yaml'
        ];
    }

    /**************************************************************************************
     * HELPER FUNCTIONS
     **************************************************************************************/

    /**
     * Get Dart type from field type
     * 
     * @param string $fieldType
     * @return string
     */
    private function getDartType(string $fieldType): string
    {
        $typeMap = [
            'text' => 'String',
            'url' => 'String',
            'email' => 'String',
            'textarea' => 'String',
            'number' => 'int',
            'boolean' => 'bool',
            'color' => 'String',
            'select' => 'String'
        ];
        
        return $typeMap[$fieldType] ?? 'String';
    }

    /**
     * Format value for Dart code
     * 
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private function formatDartValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'number':
                return (string) $value;
            case 'text':
            case 'url':
            case 'email':
            case 'textarea':
            case 'color':
            case 'select':
                // Escape special characters for Dart strings
                $escaped = str_replace('"', '\\"', $value);
                $escaped = str_replace('$', '\\$', $escaped);
                return "\"{$escaped}\"";
            default:
                return "\"{$value}\"";
        }
    }

    /**
     * Format value for YAML
     * 
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private function formatYAMLValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'number':
                return (string) $value;
            default:
                // Quote strings if they contain special characters
                if (preg_match('/[:\{\}\[\],&*#\?|\-<>=!%@`]/', $value)) {
                    return "\"{$value}\"";
                }
                return $value;
        }
    }

    /**
     * Merge config data with defaults from schema
     * 
     * @param array $schema
     * @param array $configData
     * @return array
     */
    public function mergeWithDefaults(array $schema, array $configData): array
    {
        $merged = [];
        
        foreach ($schema as $field) {
            $fieldName = $field['name'];
            
            if (isset($configData[$fieldName])) {
                $merged[$fieldName] = $configData[$fieldName];
            } elseif (isset($field['default'])) {
                $merged[$fieldName] = $field['default'];
            }
        }
        
        return $merged;
    }
}
