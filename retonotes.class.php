<?php

class CRetoNote extends w2p_Core_BaseObject {
    public $reto_note_id = null;
    public $reto_note_reto = null;
    public $reto_note_creator = null;
    public $reto_note_date = null;
    public $reto_note_description = '';

	public function __construct() {
		parent::__construct('reto_notes', 'reto_note_id');
	}

    public function check() {
        $errorArray = array();
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if ('' == trim($this->reto_note_description)) {
            $errorArray['reto_note_description'] = $baseErrorMsg . 'reto note description is not set';
        }

        return $errorArray;
	}

	public function store(CAppUI $AppUI)
    {
        $perms = $AppUI->acl();
        $stored = false;

        $errorMsgArray = $this->check();
        if (count($errorMsgArray) > 0) {
          return $errorMsgArray;
        }

        $q = new DBQuery;
        $this->reto_note_date = $q->dbfnNowWithTZ();
        $this->reto_note_creator = $AppUI->user_id;

        if ($this->reto_note_id && $perms->checkModuleItem('retos', 'edit', $this->reto_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->reto_note_id && $perms->checkModuleItem('retos', 'add')) {
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

    public function getNotes(CAppUI $AppUI) {
        $results = array();
        $perms =& $AppUI->acl();

        if ($perms->checkModuleItem('retos', 'view', $this->reto_id)) {
            $q = new DBQuery();
            $q->clear();
            $q->addQuery('reto_notes.*');
            $q->addQuery("CONCAT(contact_first_name, ' ', contact_last_name) as reto_note_owner");
            $q->addTable('reto_notes');
            $q->leftJoin('users', 'u', 'reto_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('reto_note_reto = ' . (int) $this->reto_id);
            $results = $q->loadList();
        }

        return $results;
    }
    public function storeNote(CAppUI $AppUI) {
        $perms =& $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('retos', 'edit', $this->reto_id)) {
        $q = new DBQuery;
        $this->reto_note_date = $q->dbfnNow();
        addHistory('retos', $this->reto_id, 'update', $this->reto_name, $this->reto_id);
        $stored = true;
}
/*
        *
        *
if ($note) {
	$q = new DBQuery();
	$q->addTable('reto_notes');
	$q->addInsert('reto_note_reto', $reto_id);
	$q->addInsert('reto_note_creator', $AppUI->user_id);
	$q->addInsert('reto_note_date', 'NOW()', false, true);
	$q->addInsert('reto_note_description', $_POST['reto_note_description']);
	$q->exec();
	$AppUI->setMsg('Note added', UI_MSG_OK);
	$AppUI->redirect('m=retos&a=view&reto_id=' . $reto_id);
}     *
 */
    }
    public function deleteNote() {

    }
}