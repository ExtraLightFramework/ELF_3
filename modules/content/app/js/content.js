function content_alias_checker(text,cid) {
	$.post('/content/alias_checker',{text:text,cid:cid},function(data) {
		if (data.status == 'error') {
			$('#content-alias-checker div.mini-alert').show();
			$('#content-edit-sbmt').attr('disabled','disabled');
		}
		else {
			$('#content-alias-checker div.mini-alert').hide();
			$('#content-edit-sbmt').removeAttr('disabled');
		}
		$('#content-alias-checker strong').text(data.alias);
		$('#content-alias-checker input[name=alias]').attr('value',data.alias);
	},'json');
}
