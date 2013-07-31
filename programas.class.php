<?php

class CPrograma extends w2p_Core_BaseObject {

    public $programa_id = 0;
    public $programa_name = '';
    public $programa_description = '';
    public $programa_proyecto = 0;
    public $programa_created = NULL;
    public $programa_updated = NULL;
    
    public function __construct() {
        parent::__construct('programas', 'programa_id');
    }

    public function loadFull(CAppUI $AppUI, $programasId) {
        global $AppUI;
        $q = new DBQuery;
        $q->addTable('programas', 'pr');
        $q->addQuery('pr.*');
        $q->addQuery('rr.*');
        $q->leftJoin('reto_relaciones', 'rr', 'rr.id = pr.programa_id');
        $q->leftJoin('projects', 'p', 'p.project_id = rr.project_id');
        $q->addWhere('pr.programa_id = ' . (int) $programasId);

        $q->loadObject($this, true, false);
    }

    public function check() {
        $errorArray = array();
        $baseErrorMsg = get_class($this) . '::Error al guardar los datos - ';

        if ('' == trim($this->programa_name)) {
            $errorArray['programa_name'] = $baseErrorMsg . 'No está definido el nombre';
        }
        if ('' == trim($this->programa_description)) {
            $errorArray['programa_description'] = $baseErrorMsg . 'No está definida la descripción';
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
        $this->programa_updated = $q->dbfnNowWithTZ();
        if ($this->programa_id && $perms->checkModuleItem('retos', 'edit', $this->programa_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->programa_id && $perms->checkModuleItem('retos', 'add')) {
            $this->programa_created = $q->dbfnNowWithTZ();
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }

        return $stored;
    }

    public function delete(CAppUI $AppUI) {
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'delete', $this->programa_id)) {
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
            $q->addTable('programas', 'r');
            $q->leftJoin('programa_relaciones', 'rr', 'r.programa_id = rr.programa_id');
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

        if ($perms->checkModuleItem('retos', 'view', $this->programa_id)) {
            $q = new DBQuery();
            $q->clear();
            $q->addQuery('programa_notes.*');
            $q->addTable('programa_notes');
            $q->leftJoin('users', 'u', 'programa_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('programa_note_programa = ' . (int) $this->programa_id);
            $results = $q->loadList();
        }

        return $results;
    }

    public function storeNote(CAppUI $AppUI) {
        $perms = & $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('retos', 'edit', $this->programa_id)) {
            $q = new DBQuery;
            $this->programa_note_date = $q->dbfnNow();
            addHistory('programas', $this->programa_id, 'update', $this->programa_name, $this->programa_id);
            $stored = true;
        }
    }

    public function deleteNote() {
        
    }

//    public function getTasks(CAppUI $AppUI, $projectId) {
//        $results = array();
//        $perms = $AppUI->acl();
//
//        if ($perms->checkModule('tasks', 'view')) {
//            $q = new DBQuery();
//            $q->addQuery('t.task_id, t.task_name');
//            $q->addTable('tasks', 't');
//            $q->addWhere('task_project = ' . (int) $projectId);
//            $results = $q->loadHashList('task_id');
//        }
//        return $results;
//    }

    public function hook_search() {
        $search['table'] = 'programas';
        $search['table_alias'] = 'pr';
        $search['table_module'] = 'retos';
        $search['table_key'] = $search['table_alias'] . '.programa_id'; // primary key in searched table
        $search['table_link'] = 'index.php?m=retos&amp;programa_id='; // first part of link
        $search['table_title'] = 'programas';
        $search['table_orderby'] = 'programa_name';
        $search['search_fields'] = array('programa_name', 'programa_description', 'programa_note_description');
        $search['display_fields'] = $search['search_fields'];
        $search['table_joins'] = array(array('table' => 'programa_notes',
                'alias' => 'rn', 'join' => 'pr.programa_id = rn.programa_note_programa'));

        return $search;
    }

}