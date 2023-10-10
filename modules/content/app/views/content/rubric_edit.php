<?php
	$cont = new \Elf\App\Models\Content;
	if ((int)Elf::$_data['rid']) {
		$rec = $cont->_get((int)Elf::$_data['rid'], true);
	}
?>
<form action="/content/_edit_rubric" method="post">
	<input type="hidden" name="id" value="<?=!empty($rec)?$rec['id']:0?>" />
	<table class="dialog">
		<tr>
			<th><% lang:content:content_type %></th>
			<td><?=$cont->content_type_selector(!empty($rec)?$rec['content_type']:'')?></td>
		</tr>
		<tr>
			<th><% lang:content:rubric.parent %></th>
			<td><?=$cont->rubric_selector(!empty($rec)?$rec['id']:0,!empty($rec)?$rec['parent_id']:0)?></td>
		</tr>
		<tr>
			<th><% lang:content:rubric.name %></th>
			<td><input type="text" name="title" value="<?=!empty($rec)?$rec['title']:''?>" required="required" autocomplete="off" /></td>
		</tr>
		<tr>
			<th><% lang:content:alias %></th>
			<td id="content-alias-checker">
				<input type="text" name="alias" value="<?=!empty($rec['alias'])?$rec['alias']:''?>" required="required"  autocomplete="off"
					onkeyup="_alias_checker(this.value,<?=!empty($rec)?$rec['id']:0?>)"/>
				<div class="mini-alert hide"><% lang:content:alias.notunique %></div>
			</td>
		</tr>
		<tr>
			<th><% lang:content:desc %></th>
			<td>
				<textarea name="description" rows="3"><?=!empty($rec)?$rec['description']:''?></textarea>
			</td>
		</tr>
		<tr>
			<th><% lang:content:keywords %></th>
			<td>
				<textarea name="keywords" rows="3"><?=!empty($rec)?$rec['keywords']:''?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="cntr">
				<center>
					<input class="sbmt cntrl" type="submit" id="content-edit-sbmt" value="<% lang:save %>" />
					<input type="button" value="<% lang:cancel %>" class="cncl cntrl" onclick="hideDialog(<% wid %>)" />
				</center>
			</td>
		</tr>
	</table>
</form>
