function delIt(){
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('retosDelete');?>" )) {
		form.del.value = "<?php echo $reto_id;?>";
		form.submit();
	}
}