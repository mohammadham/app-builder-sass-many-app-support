<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTranslationsTable extends Migration
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
            'lang_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'translation_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'translation_value' => [
                'type' => 'TEXT',
            ],
            'section' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'frontend',
                'comment'    => 'frontend, backend, email',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['lang_code', 'translation_key']);
        $this->forge->createTable('translations');
    }

    public function down()
    {
        $this->forge->dropTable('translations');
    }
}
