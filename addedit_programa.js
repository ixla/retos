function delIt(){
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('programasDelete');?>" )) {
		form.del.value = "<?php echo $programa_id;?>";
		form.submit();
	}
}