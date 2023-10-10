<h2><% lang:accounts:admin.title %></h2>
<form action="/accounts/_search" method="post" class="admin-search">
	<input type="hidden" name="direct" value="<?=isset(Elf::$_data['search_values']['direct'])?Elf::$_data['search_values']['direct']:1?>" />
	<div>
		<div class="admin-search-title"><% lang:accounts:search.title %>:</div> 
			<input type="text" class="nowide" size="20" name="id" autocomplete="off" value="<?=isset(Elf::$_data['search_values']['id'])?Elf::$_data['search_values']['id']:''?>" />
	</div>
	<br /><input type="submit" class="bord cntrl sbmt" value="<% lang:search %>" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="location.href='/accounts/index'" />
	<hr />
</form>
<?php if (!empty(Elf::$_data['data'])):?>
<% pagi %>
<table class="edit user-list">
	<tr class="top">
		<td width="50">#<br /><% lang:accounts:reg %></td>
		<td width="50"><% lang:accounts:email %></td>
		<td width="50"><% lang:accounts:login %></td>
		<td><% lang:accounts:name %></td>
		<td><% lang:accounts:phone %></td>
		<td><% lang:accounts:lastvisit %></td>
	</tr>
	<?php foreach (Elf::$_data['data'] as $v):?>
	<tr class="data">
		<td>
			<?=$v['id']?><div class="tm-reg" title="<% lang:accounts:datetimereg %>"><?=date('d.m.y',$v['tm_reg']).'<br />'.date('H:i:s',$v['tm_reg'])?></div>
			<a href="javascript:;" data-params="dialog=accounts/edit;uid=<?=$v['id']?>;caption=<% lang:accounts:usercard %>;offset=<% offset %>" title="<% lang:accounts:usercardopen %>" onclick="showDialog(this)"><% lang:edt %></a>
		</td>
		<td><?=$v['email']?></td>
		<td><?=$v['login']?$v['login']:'-'?></td>
		<td><?=$v['name']?$v['name']:'-'?></td>
		<td><?=$v['phone']?$v['phone']:'-'?></td>
		<td><?=$v['tm_last']?date('d.m.y H:i:s',$v['tm_last']):'-'?><br />IP:<?=$v['last_ip']?$v['last_ip']:'-'?></td>
	</tr>
	<?php endforeach;?>
</table>
<% pagi %>
<?php else:?>
<div class="alert"><% lang:datanotfound %></div>
<?php endif;?>
