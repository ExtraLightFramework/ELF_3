<?php
	$acc = new \Elf\App\Models\Accounts;
	if (Elf::get_data('uid'))
		$rec = $acc->get_by_id((int)Elf::$_data['uid']);
?>
<form action="/accounts/_edit/<% offset %>" method="post">
<input type="hidden" name="id" value="<?=isset($rec)?$rec['id']:0?>" />
	<table class="dialog">
		<tr>
			<th><% lang:accounts:regdate %>:</th>
			<td><?=isset($rec)?date('d.m.Y H:i:s',$rec['tm_reg']):'-'?></td>
		</tr>
		<tr>
			<th><% lang:accounts:lastvisit %>:</th>
			<td><?=!empty($rec['tm_last'])?date('d.m.Y H:i:s',$rec['tm_last']):'-'?></td>
		</tr>
		<tr>
			<th><% lang:accounts:lastip %>:</th>
			<td><?=isset($rec)?$rec['last_ip']:'-'?></td>
		</tr>
		<tr>
			<th><% lang:accounts:usergroup %>:</th>
			<td>
				<input type="checkbox" name="group[<?=GROUP_USER?>]" class="nowide" <?=isset($rec)&&($rec['group']&GROUP_USER)?'checked="checked"':''?> /> - <% lang:accounts:user %>,
				<input type="checkbox" name="group[<?=GROUP_ADMIN?>]" class="nowide" <?=isset($rec)&&($rec['group']&GROUP_ADMIN)?'checked="checked"':''?> /> - <% lang:accounts:admin %>,
				<input type="checkbox" name="group[<?=GROUP_TECH?>]" class="nowide" <?=isset($rec)&&($rec['group']&GROUP_TECH)?'checked="checked"':''?> /> - <% lang:accounts:tech %>
			</td>
		</tr>
		<tr>
			<th><% lang:accounts:login %>:</th>
			<td><input type="text" name="login" value="<?=isset($rec)?$rec['login']:''?>" /></td>
		</tr>
		<tr>
			<td colspan="2">
			<center><a href="javascript:;" onclick="$('#ch-user-pass').slideToggle()"><% lang:accounts:changepasswd %></a></center>
			</td>
		</tr>
		<tbody id="ch-user-pass" style="display:none;background:#ddd;">
		<tr>
			<th><% lang:accounts:newpasswd %>:</th>
			<td><input type="password" name="passwd" value="" />
		</tr>
		<tr>
			<th><% lang:accounts:repeatpasswd %>:</th>
			<td><input type="password" name="repasswd" value="" />
		</tr>
		</tbody>
		<tr>
			<th><% lang:accounts:email %>:</th>
			<td><input type="text" name="email" value="<?=isset($rec)?$rec['email']:''?>" /></td>
		</tr>
		<tr>
			<th><% lang:accounts:phone %>:</th>
			<td><input type="text" name="phone" value="<?=isset($rec)?$rec['phone']:''?>" /></td>
		</tr>
		<tr>
			<td colspan="2" class="cntr">
				<input type="submit" class="cntrl sbmt" value="OK" /> <input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog('<% wid %>')" />
			</td>
		</tr>
	</table>
</form>
