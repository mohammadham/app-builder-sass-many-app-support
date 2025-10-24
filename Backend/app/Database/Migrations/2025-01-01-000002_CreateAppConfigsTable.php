<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppConfigsTable extends Migration
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
            'app_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'config_data' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'comment' => 'JSON data from dynamic form',
            ],
            'locked_fields' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON array of field names that cannot be edited',
            ],
            'is_locked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1 if config has been saved at least once',
            ],
            'created_at' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'updated_at' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('app_id');
        $this->forge->addKey('template_id');
        $this->forge->createTable('app_configs');
    }

    public function down()
    {
        $this->forge->dropTable('app_configs');
    }
}
