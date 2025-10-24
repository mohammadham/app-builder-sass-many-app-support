<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'text',
                'comment'    => 'text, json, boolean',
            ],
            'updated_at' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('site_settings');
        
        // Insert default settings
        $data = [
            [
                'setting_key'   => 'enamad_enabled',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'updated_at'    => time(),
            ],
            [
                'setting_key'   => 'enamad_code',
                'setting_value' => '',
                'setting_type'  => 'text',
                'updated_at'    => time(),
            ],
            [
                'setting_key'   => 'zarinpal_enabled',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'updated_at'    => time(),
            ],
            [
                'setting_key'   => 'zarinpal_merchant_id',
                'setting_value' => '',
                'setting_type'  => 'text',
                'updated_at'    => time(),
            ],
            [
                'setting_key'   => 'zarinpal_sandbox',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'updated_at'    => time(),
            ],
        ];
        $this->db->table('site_settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('site_settings');
    }
}
