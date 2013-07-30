<?php

class CReto extends w2p_Core_BaseObject {

    public $reto_id = 0;
    public $reto_project = 0;
    public $reto_task = 0;
    public $reto_name = '';
    public $reto_description = '';
    public $reto_sigla = '';
    public $reto_created = NULL;
    public $reto_updated = NULL;
//    public $reto_mitigation_date = NULL;

    public function __construct() {
        parent::__construct('retos', 'reto_id');
    }

    public function loadFull(CAppUI $AppUI, $retosId) {
        global $AppUI;
        $q = new DBQuery;
        $q->addTable('retos', 'r');
        $q->addQuery('r.*');
        $q->addQuery('rr.*');
        $q->leftJoin('reto_relaciones', 'rr', 'rr.id = r.reto_id');
        $q->leftJoin('projects', 'p', 'p.project_id = rr.project_id');
        $q->addWhere('r.reto_id = ' . (int) $retosId);

        $q->loadObject($this, true, false);
    }

    public function check() {
        $errorArray = array();
        
        $baseErrorMsg = get_class($this) . '::Error al guardar los datos - ';

        if ('' == trim($this->reto_name)) {
            $errorArray['reto_name'] = $baseErrorMsg . 'No está definido el nombre';
        }
        if ('' == trim($this->reto_description)) {
            $errorArray['reto_description'] = $baseErrorMsg . 'No está definida la descripcion';
        }
        if ('' == trim( $this->reto_sigla)) {
            $errorArray['reto_sigla'] = $baseErrorMsg . 'No está definida la sigla';
        }

        return $errorArray;
    }

    public function store(CAppUI $AppUI) {
        $perms = $AppUI->acl();
        $stored = false;

        $errorMsgArray = $this->check();
        if (count($errorMsgArray) > 0) {
            return $errorMsgArray;
        }
        $q = new DBQuery;
        $this->reto_updated = $q->dbfnNowWithTZ();
//        $this->reto_mitigation_date = (2 == $this->reto_status) ? $q->dbfnNowWithTZ() : '';
        if ($this->reto_id && $perms->checkModuleItem('retos', 'edit', $this->reto_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->reto_id && $perms->checkModuleItem('retos', 'add')) {
            $this->reto_created = $q->dbfnNowWithTZ();
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }

        return $stored;
    }

    public function delete(CAppUI $AppUI) {
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'delete', $this->reto_id)) {
            if ($msg = parent::delete()) {
                return $msg;
            }
            return true;
        }
        return false;
    }

    public function getRetosByProject(CAppUI $AppUI, $project_id, $status = -1) {
        $results = array();
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'access')) {
            $q = new w2p_Database_Query();
            $q->addQuery('r.*,p.project_id, p.project_name,m.*,pg.*');
            $q->addTable('retos', 'r');
            $q->leftJoin('reto_relaciones', 'rr', 'r.reto_id = rr.reto_id');
            $q->leftJoin('projects', 'p', 'rr.project_id = p.project_id');
            $q->leftJoin('medidas', 'm', 'm.medida_id = rr.medida_id');
            $q->leftJoin('programas', 'pg', 'pg.programa_id = rr.programa_id');
    
            $results = $q->loadList();
        }
        return $results;
    }

    public function getNotes(CAppUI $AppUI) {
        $results = array();
        $perms = & $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'view', $this->reto_id)) {
            $q = new DBQuery();
            $q->clear();
            $q->addQuery('reto_notes.*');
            $q->addTable('reto_notes');
            $q->leftJoin('users', 'u', 'reto_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('reto_note_reto = ' . (int) $this->reto_id);
            $results = $q->loadList();
        }

        return $results;
    }

    public function storeNote(CAppUI $AppUI) {
        $perms = & $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('retos', 'edit', $this->reto_id)) {
            $q = new DBQuery;
            $this->reto_note_date = $q->dbfnNow();
            addHistory('retos', $this->reto_id, 'update', $this->reto_name, $this->reto_id);
            $stored = true;
        }
    }

    public function deleteNote() {
        
    }

    public function getTasks(CAppUI $AppUI, $projectId) {
        $results = array();
        $perms = $AppUI->acl();

        if ($perms->checkModule('tasks', 'view')) {
            $q = new DBQuery();
            $q->addQuery('t.task_id, t.task_name');
            $q->addTable('tasks', 't');
            $q->addWhere('task_project = ' . (int) $projectId);
            $results = $q->loadHashList('task_id');
        }
        return $results;
    }

    public function hook_search() {
        $search['table'] = 'retos';
        $search['table_alias'] = 'r';
        $search['table_module'] = 'retos';
        $search['table_key'] = $search['table_alias'] . '.reto_id'; // primary key in searched table
        $search['table_link'] = 'index.php?m=retos&amp;reto_id='; // first part of link
        $search['table_title'] = 'retos';
        $search['table_orderby'] = 'reto_name';
        $search['search_fields'] = array('reto_name', 'reto_description', 'reto_note_description','reto_sigla');
        $search['display_fields'] = $search['search_fields'];
        $search['table_joins'] = array(array('table' => 'reto_notes',
                'alias' => 'rn', 'join' => 'r.reto_id = rn.reto_note_reto'));

        return $search;
    }

}