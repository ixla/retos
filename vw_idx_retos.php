<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $project_id, $task_id, $tab;
$tab--;
$df = $AppUI->getPref('SHDATEFORMAT');

$reto = new CReto();
$retos = $reto->getRetosByProject($AppUI, $project_id, $tab--);
?>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="tbl">
    <tr bgcolor="#99CCFF" align="center" valign="top">
        <th align="center" valign="top" class="hdr" width="200px;"><?php echo $AppUI->_('Reto'); ?></th>
        <th align="center" valign="top" class="hdr" width="25px;"><?php echo $AppUI->_('Medida'); ?></th>
        <th align="center" valign="top" class="hdr" width="25px;"><?php echo $AppUI->_('Responsable'); ?></th>
        <th align="center" valign="top" class="hdr" width="20px;"><?php echo $AppUI->_('Colaboración'); ?></th>
        <th align="center" valign="top" class="hdr" width="20px;"><?php echo $AppUI->_('Programa DGMAPIAE'); ?></th>
        <th align="center" valign="top" class="hdr" width="20px;"><?php echo $AppUI->_('Proyecto DGMAPIAE'); ?></th>
    </tr>
    <?php
    foreach ($retos as $row) {
        ?>
        <tr>
            <td>
                <a href="?m=retos&amp;a=view&amp;reto_id=<?php echo $row['reto_id']; ?>"><?php echo $row['reto_sigla'] . "." . $row['reto_name']; ?></a>
            </td>
            <td>
                <a href="?m=retos&amp;a=view&amp;reto_id=<?php echo $row['medida_id']; ?>"><?php echo $row['medida_sigla'] . "." . $row['medida_name']; ?></a>
            <td nowrap="nowrap" style="text-align: center;">
                <?php echo $row['owner_name']; ?>
            </td>
            <td>
                <?php echo $row['project_name']; ?>
            </td>
            <td>
                <?php echo $row['programa_name']; ?>
            </td>
            <td>
                <?php echo $row['project_name']; ?>
            </td>
        </tr>
        <?php } ?>
</table>