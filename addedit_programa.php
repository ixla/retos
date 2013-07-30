<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}
include_once 'programas.class.php';
$programa_id = (int) w2PgetParam($_GET, 'programa_id', 0);

// check permissions for this record
$perms = &$AppUI->acl();
$canAuthor = canAdd('retos');
$canEdit = $perms->checkModuleItem('retos', 'edit', $programa_id);  

// check permissions
if (!$canAuthor && !$programa_id) {
    $AppUI->redirect('m=public&a=access_denied');
}

if (!$canEdit && $programa_id) {
    $AppUI->redirect('m=public&a=access_denied');
}
$programa = new CPrograma();
$obj = $AppUI->restoreObject();
if ($obj) {
    $programa = $obj;
    $project_id = $programa->project_id;
} else {
    $programa->loadFull($AppUI, $programa_id);
}
if (!$programa && $programa_id > 0) {
    $AppUI->setMsg('Programa');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}
$users = $perms->getPermittedUsers('retos');
$ttl = $programa_id ? 'Edit Programa' : 'Add Programa';
$titleBlock = new CTitleBlock($AppUI->_($ttl), 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCrumb('?m=' . $m, 'programas list');
$canDelete = $perms->checkModuleItem($m, 'delete', $programa_id);
if ($canDelete && $programa_id) {
    $titleBlock->addCrumbDelete('delete link', $canDelete, $msg);
}
$titleBlock->show();
$prj = new CProject();
$projects = $prj->getAllowedProjects($AppUI->user_id);
foreach ($projects as $project_id => $project_info) {
    $projects[$project_id] = $project_info['project_name'];
}

$projects = arrayMerge(array('0' => $AppUI->_('All', UI_OUTPUT_JS)), $projects);
?>
<script src="./modules/retos/addedit_programa.js" type="text/javascript"></script>

<form name="programaForm" action="?m=retos" method="post" accept-charset="utf-8">
    <input type="hidden" name="dosql" value="do_programa_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="programa_id" value="<?php echo $programa->programa_id; ?>" />
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
        <tr>
            <td align="right"><?php echo $AppUI->_('Programa Name'); ?>:</td>
            <td>
                <input type="text" class="text" size="75" name="programa_name" value="<?php echo $programa->programa_name; ?>" maxlength="50">
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Project'); ?>:</td>
            <td>
                <?php
                echo arraySelect($projects, 'programa_project', 'size="1" class="text" onChange="updateProgramas();"', $programa->programa_project);
                ?>
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description'); ?>:</td>
            <td>
                <textarea cols="73" rows="6" class="textarea" name="programa_description"><?php echo $programa->programa_description; ?></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <input class="text" type="submit" value="cancelar">
            </td>
            <td align="right">
                <input class="text" type="submit" value="enviar">
            </td>
        </tr>
    </table>
</form>