<form action="javascript:;" method="post" id="catalog-frm-del">
	<table>
		<tr>
			<td><h3 class="red"><% lang:catalog:del.item.alert %></h3></td>
		</tr>
		<tr>
			<td class="cntr">
				<input type="submit" class="cntrl sbmt" value="<% lang:remove %>" onclick="rem_catalog_item(<?=(int)Elf::get_data('cid')?>,<% wid %>)" />
				<input type="button" class="cntrl cncl" value="<% lang:cancel %>" onclick="hideDialog(<% wid %>)" />
			</td>
		</tr>
	</table>
</form>