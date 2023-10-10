<?php if (!empty(Elf::$_data[2])):?>

	<?=Elf::$_data[2]?>

<?php endif;?>
<h1><?=Elf::$_data['title']?></h1>
<?=Elf::$_data[1]?Elf::$_data[1]:''?>
<?php if (!empty(Elf::$_data[0])):?>
<div id="cat-items">
	<?php foreach (Elf::$_data[0] as $k=>$v) {
			echo Elf::load_template('catalog/item_tmpl',['item'=>$v]);
	}?>
</div>
<?php else:?>
<div class="alert"><?=Elf::lang('catalog')->item('items.notfound',Elf::$_data['title'])?></div>
<?php endif;?>
<% pagination.showmore %>
<?=Elf::$_data[1]?Elf::$_data[1]:''?>
