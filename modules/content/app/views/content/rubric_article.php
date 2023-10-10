<?php
//	print_r(Elf::$_data);?>
<h2><% title %></h2>
<% breadcrumbsline %>
<?php foreach (Elf::$_data['data'][0] as $v):?>
<div class="rubric-item">
	<a href="{site_url}<?=!empty(Elf::$_data['data'][2])?Elf::$_data['data'][2]['alias'].'/':''?><?=$v['alias']?>"><h3><?=$v['title']?></h3></a>
	<div class="flex">
		<?php if ($v['picture']):?>
		<img class="rubric-item-img rubric-item-img-<?=$v['picture_ornt']?>" src="<?=$v['picture_image']?>" alt="<?=$v['picture_alt']?>" />
		<?php else:?>
		<img class="rubric-item-img rubric-item-img-horizontal" src="/img/design/no-image.png" alt="картинка временно отсутствует" />
		<?php endif;?>
		<div class="rubric-item-text">
		<?=html_entity_decode($v['text'])?>
		</div>
	</div>
</div>
<?php endforeach;?>

