<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$reto_id = (int) w2PgetParam($_GET, 'reto_id', 0);

// check permissions for this company
$perms = &$AppUI->acl();

$canView = $perms->checkModuleItem($m, 'view', $reto_id);
$canAdd = canAdd($m);
$canEdit = $perms->checkModuleItem($m, 'edit', $reto_id);
$canDelete = $perms->checkModuleItem($m, 'delete', $reto_id);

if (!$canView) {
	$AppUI->redirect( "m=public&a=access_denied" );
} 

$retoProbability = w2PgetSysVal( 'RetoProbability' );
$retoStatus = w2PgetSysVal( 'RetoStatus' );
$retoImpact = w2PgetSysVal( 'RetoImpact' );
$retoDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');
$tab = $AppUI->processIntState('RetoVwTab', $_GET, 'tab', 0);
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');
$format = $df . ' ' . $tf;

$reto = new CReto();
$reto->loadFull($AppUI, $reto_id);

if (!$reto) {
	$AppUI->setMsg('Reto');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// setup the title block
$titleBlock = new CTitleBlock('View Reto', 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCell();
if ($canAdd) {
    $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new reto') . '" />', '', '<form action="?m=retos&amp;a=addedit" method="post" accept-charset="utf-8">', '</form>');
}
$titleBlock->addCrumb('?m='.$m, 'retos list');
if ($canEdit) {
	$titleBlock->addCrumb('?m=retos&amp;a=addedit&amp;reto_id=' . $reto_id, 'edit this reto');
}
if ($canDelete) {
	$titleBlock->addCrumbDelete('delete reto', $canDelete, $msg);
}
$titleBlock->show();
?>
<script type="text/javascript">
function delIt(){
	var form = document.frmDelete;
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Reto', UI_OUTPUT_JS).'?';?>" )) {
		form.submit();
	}
}
</script>

<form name="frmDelete" action="?m=retos" method="post">
    <input type="hidden" name="dosql" value="do_reto_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="reto_id" value="<?php echo $reto_id; ?>" />
</form>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
    <tr>
        <td width="50%">
            <table width="100%" cellspacing="1" cellpadding="2">
                <tr>
                    <td nowrap="nowrap" colspan=2><strong><?php echo $AppUI->_('Details'); ?></strong></td>
                </tr>
                <?php if ($reto->reto_project) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
                    <td style="background-color:#<?php echo $reto->project_color_identifier; ?>">
                        <font color="<?php echo bestColor($reto->project_color_identifier); ?>">
                            <?php echo '<a href="?m=projects&amp;a=view&amp;project_id=' . $reto->reto_project . '">' . htmlspecialchars($reto->project_name, ENT_QUOTES) . '</a>'; ?>
                        </font>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($reto->reto_task) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task');?>:</td>
                    <td class="hilite">
                        <?php echo '<a href="?m=projects&amp;a=view&amp;task_id=' . $reto->reto_task . '">' . htmlspecialchars($reto->task_name, ENT_QUOTES) . '</a>'; ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Reto Name'); ?>:</td>
                    <td class="hilite"><strong><?php echo $reto->reto_name; ?></strong></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Sigla');?>:</td>
                    <td class="hilite"><?php echo $reto->reto_sigla;?></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_('Reto Sigla'); ?>:</td>
                    <td class="hilite"><?php echo $reto->reto_sigla; ?></td>
                </tr>
                 <tr>
                    <td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Dates and Targets'); ?></strong></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Create Date');?>:</td>
                    <td class="hilite">
                        <?php
                            echo intval( $reto->reto_created ) ? $AppUI->formatTZAwareTime($reto->reto_created, $format) : '-';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Update Date');?>:</td>
                    <td class="hilite">
                        <?php
                            echo intval( $reto->reto_updated ) ? $AppUI->formatTZAwareTime($reto->reto_updated, $format) : '-';
                        ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" valign="top">
            <table cellspacing="1" cellpadding="2" border="0" width="100%">
                <tr><td></td></tr>
                <tr>
                    <td>
                        <strong><?php echo $AppUI->_('Description'); ?></strong><br />
                    </td>
                </tr>
                <tr>
                    <td class="hilite">
                        <?php echo w2p_textarea($reto->reto_description); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php

// Last parameter makes tab box javascript based.
$moddir = W2P_BASE_DIR . "/modules/$m/";
$tabBox = new CTabBox( "?m=$m&amp;a=view&amp;reto_id=$reto_id", '', $tab);
$tabBox->add($moddir.'vw_notes', $AppUI->_('Reto Notes' ));
$tabBox->add($moddir.'vw_note_add', $AppUI->_('Add Reto Note'));
$tabBox->show();