<form action="javascript:;" method="post" id="catalog-frm-del">
	<table>
		<tr>
			<td>
				<label><input type="radio" name="subs" value="single" checked="checked" class="nowide" /> <% lang:catalog:rem.elem %></label>
				<div class="mini-alert"><% lang:catalog:rem.elem.tlt %></div>
			</td>
		</tr>
		<tr>
			<td>
				<label><input type="radio" name="subs" value="recursive" class="nowide" /> <% lang:catalog:rem.elem.recurs %></label>
				<div class="mini-alert"><% lang:catalog:rem.elem.recurs.tlt %></div>
			</td>
		</tr>
		<tr>
			<td class="cntr">
				<input type="button" class="cntrl sbmt" value="<% lang:remove %>" onclick="rem_catalog_item(<?=(int)Elf::get_data('cid')?>,<% wid %>)" />
				<input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>