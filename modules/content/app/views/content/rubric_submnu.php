<div class="admin-subsubmnu" id="admin-subsubmnu-<% pid %>">
<?php foreach (Elf::$_data['items'] as $v):?>
<a href="/content/index/<?=$v['alias']?>" class="admin-subsubmnu-lnk<?=$v['alias']==Elf::$_data['alias']?' selected':''?>"><?=$v['level']?'|'.str_repeat("__",$v['level']*2):''?><?=$v['title']?></a>
<?php endforeach;?>
</div>