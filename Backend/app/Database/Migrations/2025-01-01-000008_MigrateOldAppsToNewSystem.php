<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateOldAppsToNewSystem extends Migration
{
    public function up()
    {
        // This migration creates app_configs entries for existing apps
        // that don't have template_id set (old system)
        
        $db = \Config\Database::connect();
        
        // Get primary template ID
        $primaryTemplate = $db->table('templates')
            ->where('is_primary', 1)
            ->get()
            ->getRowArray();
            
        if (!$primaryTemplate) {
            echo "Warning: No primary template found. Skipping migration.\n";
            return;
        }
        
        $primaryTemplateId = $primaryTemplate['id'];
        
        // Update apps without template_id to use primary template
        $db->table('apps')
            ->where('template_id', 0)
            ->orWhere('template_id IS NULL')
            ->update(['template_id' => $primaryTemplateId]);
            
        // Get all apps
        $apps = $db->table('apps')
            ->where('deleted_at', 0)
            ->get()
            ->getResultArray();
            
        foreach ($apps as $app) {
            // Check if config already exists
            $existingConfig = $db->table('app_configs')
                ->where('app_id', $app['id'])
                ->get()
                ->getRowArray();
                
            if ($existingConfig) {
                continue; // Skip if config already exists
            }
            
            // Create basic config for old apps
            // Extract config from old fields
            $configData = [
                'app_name' => $app['name'],
                'package_id' => $app['app_id'],
                'website_url' => $app['link'],
                'orientation' => $app['orientation'] ?? 0,
                'theme_color' => $app['color_theme'] ?? '#F44336',
                'pull_to_refresh' => true,
                'enable_gps' => $app['gps'] ?? false,
                'enable_camera' => $app['camera'] ?? false,
                'enable_microphone' => $app['microphone'] ?? false
            ];
            
            // Locked fields for existing apps (immutable fields)
            $lockedFields = ['package_id'];
            
            // Insert config
            $db->table('app_configs')->insert([
                'app_id' => $app['id'],
                'template_id' => $app['template_id'],
                'config_data' => json_encode($configData),
                'locked_fields' => json_encode($lockedFields),
                'is_locked' => 1,
                'created_at' => time(),
                'updated_at' => time()
            ]);
        }
        
        echo "Migration completed. Updated " . count($apps) . " apps.\n";
    }

    public function down()
    {
        // Optionally, you can remove the migrated configs
        // But it's safer to keep them
        
        // Uncomment below to remove configs created by this migration
        /*
        $db = \Config\Database::connect();
        $db->table('app_configs')->truncate();
        $db->table('apps')
            ->where('template_id IS NOT NULL')
            ->update(['template_id' => 0]);
        */
    }
}
