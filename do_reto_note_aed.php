<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}
include W2P_BASE_DIR . '/modules/retos/retonotes.class.php';

$del = 0;
$obj = new CRetoNote();
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}

$action = ($del) ? 'deleted' : 'stored';
$result = ($del) ? $obj->delete($AppUI) : $obj->store($AppUI);

if (is_array($result)) {
    $AppUI->setMsg($result, UI_MSG_ERROR, true);
    $AppUI->holdObject($obj);
    $AppUI->redirect('m=retos&a=view&reto_id='.$obj->reto_note_id);
}
if ($result) {
    $AppUI->setMsg('Retoss '.$action, UI_MSG_OK, true);
    $AppUI->redirect('m=retos');
} else {
    $AppUI->redirect('m=public&a=access_denied');
}