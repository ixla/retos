<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}
include_once 'medidas.class.php';
$medida_id = (int) w2PgetParam($_GET, 'medida_id', 0);

// check permissions for this record
$perms = &$AppUI->acl();
$canAuthor = canAdd('retos');
$canEdit = $perms->checkModuleItem('retos', 'edit', $medida_id);  

// check permissions
if (!$canAuthor && !$medida_id) {
    $AppUI->redirect('m=public&a=access_denied');
}

if (!$canEdit && $medida_id) {
    $AppUI->redirect('m=public&a=access_denied');
}

// load the record data
$medida = new CMedida();
$obj = $AppUI->restoreObject();

if ($obj) {
    $medida = $obj;
    $programa_id = $medida->programa_id;
} else {
    $medida->loadFull($AppUI, $medida_id);
}
if (!$medida && $medida_id > 0) {
    $AppUI->setMsg('Medida');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}
$ttl = $medida_id ? 'Edit Reto' : 'Add Reto';
$titleBlock = new CTitleBlock($AppUI->_($ttl), 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCrumb('?m=' . $m, 'medidas list');
$canDelete = $perms->checkModuleItem($m, 'delete', $medida_id);
if ($canDelete && $medida_id) {
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
<script src="./modules/retos/addedit.js" type="text/javascript"></script>

<form name="medidaForm" action="?m=retos" method="post" accept-charset="utf-8">
    <input type="hidden" name="dosql" value="do_medida_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="medida_id" value="<?php echo $medida->medida_id; ?>" />
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
        <tr>
            <td align="right"><?php echo $AppUI->_('Reto Name'); ?>:</td>
            <td>
                <input type="text" class="text" size="75" name="medida_name" value="<?php echo $medida->medida_name; ?>" maxlength="50">
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Programa'); ?>:</td>
            <td>
                <?php
                echo arraySelect($programas, 'medida_programa', 'size="1" class="text" onChange="updateMedidas();"', $medida->medida_programa);
                ?>
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Medida'); ?>:</td>
            <td>
                <?php
                $medidas = array();
                if ($medida->medida_project) {
                    $medidaList = $medida->getProgramas($AppUI, $medida->medida_programa);
                    foreach ($medidaList as $id => $values) {
                        $medidas[$id] = $values['medida_name'];
                    }
                }
                $medidas = arrayMerge(array('0' => $AppUI->_('Not Specified', UI_OUTPUT_JS)), $medidas);
                echo arraySelect($medidas, 'new_medida', 'size="1" class="text"', $medida->medida_medida);
                ?>
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Programa'); ?>:</td>
            <td>
                <?php
                $programas = array();
                if ($medida->medida_programas) {
                    $programaList = $medida->getProgramas($AppUI, $medida->medida_programa);
                    foreach ($programaList as $id => $values) {
                        $programas[$id] = $values['programa_name'];
                    }
                }
                $programas = arrayMerge(array('0' => $AppUI->_('Not Specified', UI_OUTPUT_JS)), $programas);
                echo arraySelect($medidas, 'new_programa', 'size="1" class="text"', $medida->medida_programa);
                ?>
            </td>
        </tr>
        <tr>
            <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description'); ?>:</td>
            <td>
                <textarea cols="73" rows="6" class="textarea" name="medida_description"><?php echo $medida->medida_description; ?></textarea>
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