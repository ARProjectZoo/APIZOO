<?php

namespace Fuel\Migrations;


class Users 
{

    function up()
    {
        \DBUtil::create_table('Users', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'userName' => array('type' => 'varchar', 'constraint' => 100),
                'email' => array('type' => 'varchar', 'constraint' => 100),
                'password' => array('type'=> 'varchar', 'constraint' => 30),
                'id_role' => array('type'=> 'int', 'constraint' => 100),
                'id_device' => array('type' => 'varchar', 'constraint'=> 100),
                'profilePicture' => array('type' => 'varchar', 'constraint' => 100, NULL),
        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
    array(
        array(
            'constraint' => 'ForeingKeyUserToRoles',
            'key' => 'id_role',
            'reference' => array(
                'table' => 'Roles',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'
            ))
        );    
    }

    function down()
    {
       \DBUtil::drop_table('Users');
    }
}