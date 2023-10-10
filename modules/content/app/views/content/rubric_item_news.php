<div class="rubric-item <% content_type %>-item">
	<img src="<?=Elf::$_data['picture']?Elf::$_data['picture_image']:'/img/design/no-image-icon.jpg'?>" alt="<?=Elf::$_data['picture_alt']?Elf::$_data['picture_alt']:Elf::$_data['title']?>" /></td>
	<div class="date"><?=date('d.m.Y',Elf::$_data['tm'])?></div>
	<h4><a href="{site_url}<% type %>/<% alias %>" class="title"><?=Elf::$_data['title']?></a></h4>
	<?php if (Elf::$_data['tags']):?>
	<div class="rubric-item-tags">
		<?php foreach (Elf::$_data['tags'] as $v):?>
		<a href="/tag/<?=urlencode($v['htag'])?>" title="<% lang:tags.searchrecs %> <?=$v['htag']?>">#<?=$v['htag']?></a>
		<?php endforeach;?>
	</div>
	<?php endif;?>
	<div class="rubric-item-text">
		<?=strip_tags(html_entity_decode(Elf::$_data['first_p']))?>
	</div>
	<a href="{site_url}<?=!empty(Elf::$_data['paliases'])?implode('/',Elf::$_data['paliases']):''?>/<% alias %>" class="rubric-item-lnk-more lnk-more">читать...</a>
</div>