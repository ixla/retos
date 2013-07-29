<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$del = (int) w2PgetParam($_POST, 'del', 0);

$obj = new CReto();
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}
$obj->reto_medida = (int) w2PgetParam($_POST, 'new_medida', 0);
$obj->reto_programa = (int) w2PgetParam($_POST, 'new_programa', 0);


$action = ($del) ? 'deleted' : 'stored';
$result = ($del) ? $obj->delete($AppUI) : $obj->store($AppUI);

if (is_array($result)) {
    $AppUI->setMsg($result, UI_MSG_ERROR, true);
    $AppUI->holdObject($obj);
    $AppUI->redirect('m=retos&a=addedit');
}
if ($result) {
    $AppUI->setMsg('Retos '.$action, UI_MSG_OK, true);
    $AppUI->redirect('m=retos');
} else {
    $AppUI->redirect('m=public&a=access_denied');
}