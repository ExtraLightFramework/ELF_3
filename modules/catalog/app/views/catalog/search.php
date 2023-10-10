<?php if (Elf::$_data['srch']):
	foreach (Elf::$_data['srch'] as $v):
?>
	<a href="/<?=$v['alias']?>"><?=$v['name']?> <strong><?php if ($v['type']=='rubric'):?>(<?=$v['items_cnt']?>) <span title="рубрика">&rarr;</span><?php else:?><?=$v['price']?> <% lang:catalog:currency.short %>.<?php endif;?></strong></a>
<?php endforeach;?>
	<a href="javascript:goSearch()"><% lang:catalog:show.all.results %>...</a>
<?php else:?>
<div class="not-found"><% lang:catalog:never.found %></div>
<?php endif;?>