<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}
include_once('medidas.class.php');
$del = (int) w2PgetParam($_POST, 'del', 0);

$obj = new CMedida();
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}

$action = ($del) ? 'deleted' : 'stored';
$result = ($del) ? $obj->delete($AppUI) : $obj->store($AppUI);

if (is_array($result)) {
    $AppUI->setMsg($result, UI_MSG_ERROR, true);
    $AppUI->holdObject($obj);
    $AppUI->redirect('m=retos&a=addedit_medidas');
}
if ($result) {
    $AppUI->setMsg('Medidas '.$action, UI_MSG_OK, true);
    $AppUI->redirect('m=retos');
} else {
    $AppUI->redirect('m=public&a=access_denied');
}