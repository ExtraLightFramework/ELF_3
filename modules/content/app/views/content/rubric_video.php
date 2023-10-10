<h2><% title %></h2>
<% breadcrumbsline %>
<?php foreach (Elf::$_data['data'][0] as $v):?>
<div class="rubric-item">
	<a href="{site_url}<?=!empty(Elf::$_data['data'][2])?Elf::$_data['data'][2]['alias'].'/':''?><?=$v['alias']?>"><h3><?=$v['title']?></h3></a>
	<iframe src="https://www.youtube.com/embed/<?=$v['picture']?>" frameborder="0" allowfullscreen="1" class="video"></iframe>
	<p class="rubric-item-desc"><?=nl2br($v['text'])?></p>
</div>
<?php endforeach;?>
