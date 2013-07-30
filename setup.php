<?php

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$config = array();
$config['mod_name'] = 'Retos';              // name the module
$config['mod_version'] = '1.0';                // add a version number
$config['mod_directory'] = 'retos';              // tell web2project where to find this module
$config['mod_setup_class'] = 'CSetupRetos';        // the name of the PHP setup class (used below)
$config['mod_type'] = 'user';               // 'core' for modules distributed with w2p by standard, 'user' for additional modules
$config['mod_ui_name'] = $config['mod_name']; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'Retos management';   // some description of the module
$config['mod_config'] = false;           // show 'configure' link in viewmods
$config['mod_main_class'] = 'CReto';

$config['permissions_item_table'] = 'retos';
$config['permissions_item_field'] = 'reto_id';
$config['permissions_item_label'] = 'reto_name';

class CSetupRetos {

    public function install() {
        global $AppUI;

        $q = new DBQuery();
        $q->createTable('retos');
        $sql = '(
            `reto_id` int(10) unsigned NOT NULL auto_increment,
            `reto_name` varchar(255) default NULL,
            `reto_description` text,
            `reto_sigla` varchar(25) default NULL,
            `reto_created` datetime NOT NULL,
            `reto_updated` datetime NOT NULL,
            `reto_notes` text,
            PRIMARY KEY  (`reto_id`))
            ENGINE=MyISAM';
        $q->createDefinition($sql);
        $q->exec();

        $q->clear();
        $q->createTable('reto_notes');
        $sql = '(
            `reto_note_id` int(11) NOT NULL auto_increment,
            `reto_note_reto` int(11) NOT NULL default \'0\',
            `reto_note_creator` int(11) NOT NULL default \'0\',
            `reto_note_date` datetime NOT NULL default \'0000-00-00 00:00:00\',
            `reto_note_description` text NOT NULL,
            PRIMARY KEY  (`reto_note_id`))
            ENGINE=MyISAM';
        $q->createDefinition($sql);
        $q->exec();

        $q->clear();
        $q->createTable('medidas');
        $sql = '(
            `medida_id` int(10) unsigned NOT NULL auto_increment,
            `medida_name` varchar(255) default NULL,
            `medida_description` text,
            `medida_sigla` varchar(25) default NULL,
            `medida_created` datetime NOT NULL,
            `medida_updated` datetime NOT NULL,
            `medida_notes` text,
            PRIMARY KEY  (`medida_id`))
            ENGINE=MyISAM';
        $q->createDefinition($sql);
        $q->exec();

        $q->clear();
        $q->createTable('programas');
        $sql = '(
            `programa_id` int(10) unsigned NOT NULL auto_increment,
            `programa_name` varchar(255) default NULL,
            `programa_description` text,
            `programa_created` datetime NOT NULL,
            `programa_updated` datetime NOT NULL,
            `programa_notes` text,
            PRIMARY KEY  (`programa_id`))
            ENGINE=MyISAM';
        $q->createDefinition($sql);
        $q->exec();

        $q->clear();
        $q->createTable('reto_relaciones');
        $sql = '(
            `id` int(10) unsigned NOT NULL auto_increment,
            `reto_id` int(10),
            `medida_id` int(10),
            `programa_id` int(10),
            `project_id` int(10) default NULL,
            PRIMARY KEY  (`id`))
            ENGINE=MyISAM';
        $q->createDefinition($sql);
        $q->exec();


        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RetoImpact');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "Sin Especificar");
        $q->addInsert('sysval_value_id', 0);
        $q->exec();
        $q->addInsert('sysval_value', "Bajo");
        $q->addInsert('sysval_value_id', 1);
        $q->exec();
        $q->addInsert('sysval_value', "Medio");
        $q->addInsert('sysval_value_id', 2);
        $q->exec();
        $q->addInsert('sysval_value', "Alto");
        $q->addInsert('sysval_value_id', 3);
        $q->exec();
        $q->addInsert('sysval_value', "Muy Alto");
        $q->addInsert('sysval_value_id', 4);
        $q->exec();


        $q->clear();
        $q->addTable('sysvals');
        $q->addInsert('sysval_title', 'RetoStatus');
        $q->addInsert('sysval_key_id', 1);
        $q->addInsert('sysval_value', "Sin Especificar");
        $q->addInsert('sysval_value_id', 0);
        $q->exec();
        $q->addInsert('sysval_value', "Abierto");
        $q->addInsert('sysval_value_id', 1);
        $q->exec();
        $q->addInsert('sysval_value', "Cerrado");
        $q->addInsert('sysval_value_id', 2);
        $q->exec();
        $q->addInsert('sysval_value', "No Aplica");
        $q->addInsert('sysval_value_id', 3);
        $q->exec();

        $perms = $AppUI->acl();
        return $perms->registerModule('Retos', 'retos');
    }

    public function upgrade($old_version) {
        switch ($old_version) {
            default:
            //do nothing
        }
        return true;
    }

    public function remove() {
        global $AppUI;

        $q = new DBQuery;
        $q->dropTable('retos');
        $q->exec();
        $q->clear();
        $q->dropTable('reto_notes');
        $q->exec();

        $q->clear();
        $q->setDelete('sysvals');
        $q->addWhere("sysval_title LIKE 'Reto%'");
        $q->exec();

        $perms = $AppUI->acl();
        return $perms->unregisterModule('retos');
    }

}