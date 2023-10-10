function catalog_alias_checker(text,cid) {
	$.post('/catalog/alias_checker',{text:text,cid:cid},function(data) {
		if (data.status == 'error') {
			$('#catalog-alias-checker div.mini-alert').show();
			$('#content-edit-sbmt').attr('disabled','disabled');
		}
		else {
			$('#catalog-alias-checker div.mini-alert').hide();
			$('#content-edit-sbmt').removeAttr('disabled');
		}
		$('#catalog-alias-checker strong').text(data.alias);
		$('#catalog-alias-checker input[name=alias]').attr('value',data.alias);
		retype_uri(); // function in views/catalog/edit.php
	},'json');
}
