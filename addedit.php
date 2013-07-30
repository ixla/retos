<?php
/* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$reto_id = (int) w2PgetParam($_GET, 'reto_id', 0);

// check permissions for this record
$perms = &$AppUI->acl();
$canAuthor = canAdd('retos');
$canEdit = $perms->checkModuleItem('retos', 'edit', $reto_id);  

// check permissions
if (!$canAuthor && !$reto_id) {
    $AppUI->redirect('m=public&a=access_denied');
}

if (!$canEdit && $reto_id) {
    $AppUI->redirect('m=public&a=access_denied');
}

// load the record data
$reto = new CReto();
$obj = $AppUI->restoreObject();

if ($obj) {
    $reto = $obj;
    $project_id = $reto->reto_id;
} else {
    $reto->loadFull($AppUI, $reto_id);
}
if (!$reto && $reto_id > 0) {
    $AppUI->setMsg('Reto');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}
$retoStatus = w2PgetSysVal('RetoStatus');
$retoImpact = w2PgetSysVal('RetoImpact');
//$retoDuration = array(1 => 'Hours', 24 => 'Days', 168 => 'Weeks');
$users = $perms->getPermittedUsers('retos');
// setup the title block
$ttl = $reto_id ? 'Edit Reto' : 'Add Reto';
$titleBlock = new CTitleBlock($AppUI->_($ttl), 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCrumb('?m=' . $m, 'retos list');
$canDelete = $perms->checkModuleItem($m, 'delete', $reto_id);
if ($canDelete && $reto_id) {
    $titleBlock->addCrumbDelete('delete link', $canDelete, $msg);
}
$titleBlock->show();
?>
<script src="./modules/retos/addedit.js" type="text/javascript"></script>

<form name="form" action="?m=retos" method="post" accept-charset="utf-8">
    <input type="hidden" name="dosql" value="do_reto_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="reto_id" value="<?php echo $reto->reto_id; ?>" />
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
        <tr>
            <td align="right"><?php echo $AppUI->_('Reto Name'); ?>:</td>
            <td>
                <input type="text" class="text" size="75" name="reto_name" value="<?php echo $reto->reto_name; ?>" maxlength="50">
            </td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('Reto Sigla'); ?>:</td>
            <td>
                <input type="text" class="text" size="5" name="reto_sigla" value="<?php echo $reto->reto_sigla; ?>" maxlength="3">
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description'); ?>:</td>
            <td>
                <textarea cols="73" rows="6" class="textarea" name="reto_description"><?php echo $reto->reto_description; ?></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <input class="text" type="submit" value="Volver">
            </td>
            <td align="right">
                <input class="text" type="submit" value="Enviar">
            </td>
        </tr>
    </table>
</form>