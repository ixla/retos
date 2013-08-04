<?php

class CMedida extends w2p_Core_BaseObject {

    public $medida_id = 0;
    public $medida_name = '';
    public $medida_description = '';
    public $medida_proyecto = 0;
    public $medida_created = NULL;
    public $medida_updated = NULL;

    public function __construct() {
        parent::__construct('medidas', 'medida_id');
    }

    public function loadFull(CAppUI $AppUI, $medidasId) {
        global $AppUI;
        $q = new DBQuery;
        $q->addTable('medidas', 'pr');
        $q->addQuery('pr.*');
        $q->addQuery('rr.*');
        $q->leftJoin('reto_relaciones', 'rr', 'rr.id = pr.medida_id');
        $q->leftJoin('projects', 'p', 'p.project_id = rr.project_id');
        $q->addWhere('pr.medida_id = ' . (int) $medidasId);

        $q->loadObject($this, true, false);
    }

    public function check() {
        $errorArray = array();
        $baseErrorMsg = get_class($this) . '::Error al guardar los datos - ';

        if ('' == trim($this->medida_name)) {
            $errorArray['medida_name'] = $baseErrorMsg . 'No está definido el nombre';
        }
        if ('' == trim($this->medida_description)) {
            $errorArray['medida_description'] = $baseErrorMsg . 'No está definida la descripción';
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
        $this->medida_updated = $q->dbfnNowWithTZ();
        if ($this->medida_id && $perms->checkModuleItem('retos', 'edit', $this->medida_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->medida_id && $perms->checkModuleItem('retos', 'add')) {
            $this->medida_created = $q->dbfnNowWithTZ();
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }

        return $stored;
    }

    public function delete(CAppUI $AppUI) {
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'delete', $this->medida_id)) {
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
            $q->addTable('medidas', 'r');
            $q->leftJoin('medida_relaciones', 'rr', 'r.medida_id = rr.medida_id');
            $q->leftJoin('projects', 'p', 'rr.project_id = p.project_id');
            $q->leftJoin('medidas', 'm', 'm.medida_id = rr.medida_id');
            $q->leftJoin('medidas', 'pg', 'pg.medida_id = rr.medida_id');

            $results = $q->loadList();
        }
        return $results;
    }

    public function getNotes(CAppUI $AppUI) {
        $results = array();
        $perms = & $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'view', $this->medida_id)) {
            $q = new DBQuery();
            $q->clear();
            $q->addQuery('medida_notes.*');
            $q->addTable('medida_notes');
            $q->leftJoin('users', 'u', 'medida_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('medida_note_medida = ' . (int) $this->medida_id);
            $results = $q->loadList();
        }

        return $results;
    }

    public function storeNote(CAppUI $AppUI) {
        $perms = & $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('retos', 'edit', $this->medida_id)) {
            $q = new DBQuery;
            $this->medida_note_date = $q->dbfnNow();
            addHistory('medidas', $this->medida_id, 'update', $this->medida_name, $this->medida_id);
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
        $search['table'] = 'medidas';
        $search['table_alias'] = 'pr';
        $search['table_module'] = 'retos';
        $search['table_key'] = $search['table_alias'] . '.medida_id'; // primary key in searched table
        $search['table_link'] = 'index.php?m=retos&amp;medida_id='; // first part of link
        $search['table_title'] = 'medidas';
        $search['table_orderby'] = 'medida_name';
        $search['search_fields'] = array('medida_name', 'medida_description', 'medida_note_description');
        $search['display_fields'] = $search['search_fields'];
        $search['table_joins'] = array(array('table' => 'medida_notes',
                'alias' => 'rn', 'join' => 'pr.medida_id = rn.medida_note_medida'));

        return $search;
    }

    public function getProgramas(CAppUI $AppUI, $medida_id, $status = -1) {
        $results = array();
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'access')) {
            $q = new w2p_Database_Query();
            $q->addQuery('p.program_id, p.program_name,m.*');
            $q->addTable('medidas', 'm');
            $q->leftJoin('retos_relaciones', 'rr', 'm.medida_id = rr.medida_id');
            $q->leftJoin('programas', 'p', 'rr.programa_id = p.programa_id');
            
            $results = $q->loadList();
        }
        return $results;
    }
    
        public function getProgramasByMedida(CAppUI $AppUI, $medida_id, $status = -1) {
        $results = array();
        $perms = $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'access')) {
            $q = new w2p_Database_Query();
            $q->addQuery('p.program_id, p.program_name,m.*');
            $q->addTable('medidas', 'm');
            $q->leftJoin('retos_relaciones', 'rr', 'm.medida_id = rr.medida_id');
            $q->leftJoin('programas', 'p', 'rr.programa_id = p.programa_id');
            $q->where('m.medida_id = $medida_id');
            
            $results = $q->loadList();
        }
        return $results;
    }

}