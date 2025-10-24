<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLanguagesTable extends Migration
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
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'direction' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'ltr',
                'comment'    => 'rtl or ltr',
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '0=inactive, 1=active',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('languages');
        
        // Insert default languages
        $data = [
            [
                'code'       => 'en',
                'name'       => 'English',
                'direction'  => 'ltr',
                'is_default' => 1,
                'status'     => 1,
            ],
            [
                'code'       => 'fa',
                'name'       => 'فارسی',
                'direction'  => 'rtl',
                'is_default' => 0,
                'status'     => 1,
            ],
        ];
        $this->db->table('languages')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('languages');
    }
}
