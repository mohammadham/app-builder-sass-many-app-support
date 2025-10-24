<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTemplateIdToApps extends Migration
{
    public function up()
    {
        $fields = [
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'template',
            ],
        ];
        $this->forge->addColumn('apps', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('apps', 'template_id');
    }
}
