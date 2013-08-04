function delIt(){
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('medidaDelete');?>" )) {
		form.del.value = "<?php echo $medida_id;?>";
		form.submit();
	}
}