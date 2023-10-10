<% breadcrumbsline %>
<h1><?=Elf::$_data['title']?></h1>
<?=!empty(Elf::$_data['data'][1])?Elf::$_data['data'][1]:''?>
<?php if (!empty(Elf::$_data['data'][0])):?>
	<div class="content-rubric-items">
	<?php foreach (Elf::$_data['data'][0] as $v):?>
		<?=Elf::load_template('content/rubric_item_'.$v['content_type'],$v)?>
	<?php endforeach;?>
	</div>
	<% pagination.showmore %>
<?php endif;?>
