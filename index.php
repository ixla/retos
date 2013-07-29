<?php

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$perms = & $AppUI->acl();
if (!$perms->checkModuleItem($m, 'access')) {
    $AppUI->redirect('m=public&a=access_denied');
}

//$tab = (int) w2PgetParam($_GET, 'tab', 0);
//$retoStatus = array(-1 => $AppUI->_('All Retos')) + w2PgetSysVal('RetoStatus');
$durnTypes = array(1 => 'Hours', 24 => 'Days', 168 => 'Weeks');
$titleBlock = new CTitleBlock('Retos', 'scales.png', $m, $m . $a);
if ($perms->checkModule($m, 'add')) {

    $titleBlock->addCell(
            '<input type="submit" class="button" value="' . $AppUI->_('Nuevo reto') . '">', '', '<form action="?m=retos&amp;a=addedit" method="post">', '</form>'
    );
    $titleBlock->addCell(
            '<input type="submit" class="button" value="' . $AppUI->_('Nueva medida') . '">', '', '<form action="?m=retos&a=addedit_medida" method="post" accept-charset="utf-8">', '</form>');

     $titleBlock->addCell(
            '<input type="submit" class="button" value="' . $AppUI->_('Nuevo programa') . '">', '', '<form action="?m=retos&a=addedit_programa" method="post" accept-charset="utf-8">', '</form>');

}
$titleBlock->show();

//$tabBox = new CTabBox("?m=$m", W2P_BASE_DIR . "/modules/$m/", $tab);
//foreach ($retoStatus as $status) {
//    $tabBox->add('vw_idx_retos', $AppUI->_($status));
//}
//$tabBox->show();