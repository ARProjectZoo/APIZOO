<?php

namespace Fuel\Migrations;


class Roles
{

    function up()
    {
        \DBUtil::create_table('Roles', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'type' => array('type' => 'varchar', 'constraint' => 100),
        ), array('id'));    
    }

    function down()
    {
       \DBUtil::drop_table('Roles');
    }
}