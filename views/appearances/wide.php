	<script> 
	$(function() {
		modals_cnt.push('<% wid %>'); //.attr('data-id','<% wid %>')
		$("#modal").css('display','block').css('opacity',0).animate({opacity:0.7},500);
		$("#<% wid %>").css('top',$("body").scrollTop()+30);
		$(window).scrollTop(0);
		_help_tooltip_creator();
	});
	</script> 
	<div class="dialog dialog-wide" id="<% wid %>" data-app="appearance /views/appearances/wide">
		<div class="top">
			<?=Elf::get_data('caption')?Elf::get_data('caption'):'<% lang:systemdialog %>'?>
			<i class="fas fa-times-circle dialog-close-btn sim-common-dialog-close" title="<% lang:closeunsave %>" <?=Elf::get_data('close_func')?'onclick="'.Elf::get_data('close_func').';hideDialog(<% wid %>)"':'onclick="hideDialog(<% wid %>)"'?> data-id="<% wid %>"></i>
		</div>
		<div class="text">
		<?=Elf::load_template(Elf::get_data('dialog'))?>
		</div>
	</div>
	<script>
	// Slider Form Init
	$(".slider-frm form").each(function() {
		let _w = $(this).closest('.slider-frm').width();
		$(this).width(_w);
	});

	// INIT advanced inputs
	$('.elf-advs-input').each(function() {
		if ($(this).hasClass('elf-clever-selector'))
			new ELF_CleverSelector($(this));
		if ($(this).hasClass('elf-enumenator-selector'))
			new ELF_EnumenatorSelector($(this));
	});
	</script>

<!--
	<div class="auth-dialog wide-dialog" id="<% wid %>" data-app="appearance /views/appearances/wide">
		<div class="top">
			<?=''//Elf::get_data('caption')?Elf::get_data('caption'):'<% lang:systemdialog %>'?>
			<i class="fas fa-times-circle close-btn" title="<% lang:closeunsave %>" onclick="<?=Elf::get_data('close_func')?Elf::get_data('close_func').';':''?>hideDialog(<% wid %>)" data-id="<% wid %>"></i>
		</div>
		<div class="text">
		<?=''//Elf::load_template(Elf::get_data('dialog'))?>
		</div>
	</div>
-->