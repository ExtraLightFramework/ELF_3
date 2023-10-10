var _ELF_installer = true;
var _frm_offset = 0;

$(function() {
	_frm_offset = $("#frm-cont").width();
	var _off = 0;
	var _h = 0;
	$("#frm-cont form").each(function() {
		$(this).css('left',_off+'px');
		_off += _frm_offset;
		_h = _h < $(this).height()?$(this).height():_h;
	});
	$("#frm-cont").height(_h+50);
	$("div.module input[type=checkbox]").click(function() {
		var _nm = $(this).attr('data-name');
		if ($(this).attr('readonly'))
			$(this).attr('checked','checked');
		else
			_chk_dep_modules(_nm);
	});
});

function _chk_dep_modules(_nm) {
	if ($("#module-"+_nm).attr('checked')) {
		$("input.dep-"+_nm).each(function() {	
			if ($("#module-"+$(this).val()).val() == 'on')
				$("#module-"+$(this).val()).attr('value','');
			$("#module-"+$(this).val()).attr('checked','checked').attr('readonly','readonly');
			if (-1 == $("#module-"+$(this).val()).val().indexOf(_nm+','))
				$("#module-"+$(this).val()).attr('value',$("#module-"+$(this).val()).val()+_nm+',');
			_chk_dep_modules($("#module-"+$(this).val()).attr('data-name'));
		});
	}
	else {
		$("input.dep-"+_nm).each(function() {
			var _v = $("#module-"+$(this).val()).val();
			if (typeof _v != 'undefined') {
				_v = _v.replace(_nm+',','');
				$("#module-"+$(this).val()).attr('value',_v);
				if (!_v) {
					$("#module-"+$(this).val()).removeAttr('checked readonly');
				}
			}
			_chk_dep_modules($("#module-"+$(this).val()).attr('data-name'));
		});
	}
	
}
function _is_json(str) {
	let _ret;
	try {
		_ret = JSON.parse(str);
	} catch (e) {
		return false;
	}
	return _ret;
}
function _step(step) {
	var _out = {};
	$("#elf-install-"+step+" input, #elf-install-"+step+" select, #elf-install-"+step+" textarea").each(function() {
		if (($(this).attr('type') == 'checkbox')||($(this).attr('type') == 'radio'))
			_out[$(this).attr('name')] = $(this).attr('checked')?1:0;
		else
			_out[$(this).attr('name')] = $(this).val();
	});
	$.post('/install/step.php',{step:step,params:JSON.stringify(_out)},function(data) {
		if (_is_json(data))
			alert(data);
		else if ((typeof data.error != 'undefined') && data.error)
			alert(data.error);
		else {
			$("#frm-cont form").stop().animate({left:'-='+_frm_offset+'px'},500);
			if (typeof data.warning != 'undefined')
				alert(data.warning);
		}
	},'json');
}