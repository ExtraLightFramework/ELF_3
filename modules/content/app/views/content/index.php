<script>
function setNewPos(id) {
	showWW();
	$.post('/content/set_new_pos',{id:id,pos:$('#cpos'+id).val(),ctype:'<% ctype %>'},function(data) {
		hideWW();
		if (data == 'ok') {
			location.href='{site_url}content/index/<% ctype %>';
		}
	});
}
function _sw_content_visible(id, sw) {
	showWW();
	$.post('/content/sw_visible',{id:id,visible:sw?1:0},function(data) {
		hideWW();
		if (data == 'ok') {
			if (sw)
				$('#visible-'+id).text('видима');
			else
				$('#visible-'+id).text('невидима');
		}
	});
}
</script>
<h2 class="adm-tlt"><% title %></h2>
<?=Elf::load_template('content/menu')?>
<% breadcrumbs %>

<h5><a href="javascript:;" data-params="dialog=content/edit;appearance=appearances/wide;cid=0;parent_alias=<% alias %>;parent_id=<% parent_id %>;content_type=<% ctype %>;caption=<% lang:content:edit.newitem %>" onclick="showDialog(this)" title="<% lang:content:newitem.tlt %>"><% lang:content:newitem %></a></h5>
<% pagi %>
<?php if (Elf::get_data('alias') && !empty(Elf::$_data['content'])):
		$v = Elf::$_data['content'][0];
?>
	<table class="edit">
		<tr class="top">
			<td><a href="/content/set_order_type/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>/<?=$v['parent_id']?>/<?=!$v['content_order_type']?base64_encode("`id` ASC"):($v['content_order_type']=="`id` DESC"?base64_encode("`id` ASC"):($v['content_order_type']=="`id` ASC"?base64_encode("`id` DESC"):base64_encode("`id` ASC")))?>">#<?=$v['content_order_type']=="`id` DESC"?' ▼':($v['content_order_type']=="`id` ASC"?' ▲':'')?></a></td>
			<td><% lang:content:actions %></td>
			<td><a href="/content/set_order_type/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>/<?=$v['parent_id']?>/<?=!$v['content_order_type']?base64_encode("`title` ASC"):($v['content_order_type']=="`title` DESC"?base64_encode("`title` ASC"):($v['content_order_type']=="`title` ASC"?base64_encode("`title` DESC"):base64_encode("`title` ASC")))?>"><% lang:content:name %><?=$v['content_order_type']=="`title` DESC"?' ▼':($v['content_order_type']=="`title` ASC"?' ▲':'')?></a></td>
			<td>URL</td>
			<td><% lang:content:desc %></td>
			<?php if (Elf::$_data['ctype']!=CONTENT_TYPE_REVIEWS):?>
			<td><% lang:content:activelnk %></td>
			<?php else:?>
			<td><% lang:content:keywords %></td>
			<?php endif;?>
			<?php if (Elf::$_data['ctype']!=CONTENT_TYPE_FAQ):?>
			<td><% lang:content:picture %></td>
			<?php endif;?>
			<td><a href="/content/set_order_type/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>/<?=$v['parent_id']?>/<?=!$v['content_order_type']||($v['content_order_type']=="`tm` DESC")?base64_encode("`tm` ASC"):base64_encode("`tm` DESC")?>"><% lang:content:date %><?=!$v['content_order_type']||($v['content_order_type']=="`tm` DESC")?' ▼':($v['content_order_type']=="`tm` ASC"?' ▲':'')?></a></td>
			<td><a href="/content/set_order_type/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>/<?=$v['parent_id']?>/<?=!$v['content_order_type']?base64_encode("`tm_edit` ASC"):($v['content_order_type']=="`tm_edit` DESC"?base64_encode("`tm_edit` ASC"):($v['content_order_type']=="`tm_edit` ASC"?base64_encode("`tm_edit` DESC"):base64_encode("`tm_edit` ASC")))?>"><% lang:content:tmedit %><?=$v['content_order_type']=="`tm_edit` DESC"?' ▼':($v['content_order_type']=="`tm_edit` ASC"?' ▲':'')?></a></td>
			<td><a href="/content/set_order_type/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>/<?=$v['parent_id']?>/<?=!$v['content_order_type']?base64_encode("`pos` ASC"):($v['content_order_type']=="`pos` DESC"?base64_encode("`pos` ASC"):($v['content_order_type']=="`pos` ASC"?base64_encode("`pos` DESC"):base64_encode("`pos` ASC")))?>"><% lang:content:pos %><?=$v['content_order_type']=="`pos` DESC"?' ▼':($v['content_order_type']=="`pos` ASC"?' ▲':'')?></a></td>
		</tr>
	<?php foreach (Elf::$_data['content'] as $v):?>
		<tr class="data">
			<td>
				<?=$v['id']?>
				<?php if ($v['hot']):?><div class="hot" title="<% lang:content:hotitem %>"></div><?php endif;?>
			</td>
			<td>
				<a href="javascript:;" data-params="dialog=content/edit;appearance=appearances/wide;cid=<?=$v['id']?>;parent_alias=<% alias %>;parent_id=<% parent_id %>;content_type=<% ctype %>;offset=<% offset %>;caption=<% lang:content:edit.item %>" onclick="showDialog(this)" title="<% lang:content:edtitem.tlt %>"><% lang:edt %></a><br />
				<a href="/content/_del/<?=$v['id']?>/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>" onclick="return confirm('<% lang:content:delitem.cnfrm %>')" title="<% lang:content:delitem.tlt %>"><% lang:del %></a><br />
				<input type="checkbox" <?=$v['visible']?'checked="checked"':''?> title="<% lang:content:visible.tlt %>" onclick="_sw_content_visible(<?=$v['id']?>,$(this).prop('checked'))" /> - <span id="visible-<?=$v['id']?>"><?=Elf::lang('content')->item('visible.'.(int)$v['visible'])?></span>
			</td>
			<td><a href="javascript:;" data-params="dialog=content/edit;appearance=appearances/wide;cid=<?=$v['id']?>;parent_alias=<% alias %>;parent_id=<% parent_id %>;content_type=<% ctype %>;offset=<% offset %>" onclick="showDialog(this)" title="<% lang:content:edtitem.tlt %>"><?=$v['title']?></a></td>
			<td><a href="<?=Elf::site_url().($v['paliases']?implode('/',$v['paliases']):'').'/'.$v['alias']?>" target="_blank" title="<% lang:content:openinnewtab %>"><?=Elf::site_url().($v['paliases']?implode('/',$v['paliases']):'').'/'.$v['alias']?></a><br />
				<strong><% lang:content:anons %>:</strong><br />
				<div class="show-top show-top-min" id="show-anons-<?=$v['id']?>">
					<a href="javascript:;" onclick="$('#show-anons-<?=$v['id']?>').toggleClass('show-top-min show-top-max')" title="">...</a>
					<?=Elf::show_words(strip_tags(html_entity_decode($v['text'])),20)?>
					</div>
				</td>
			<td><div class="show-top show-top-min" id="show-top-<?=$v['id']?>">
				<a href="javascript:;" onclick="$('#show-top-<?=$v['id']?>').toggleClass('show-top-min show-top-max')" title="<% lang:content:showall %>">...</a>
				<?=Elf::show_words(strip_tags(html_entity_decode($v['description'])),50)?>
				</div>
			</td>
			<td><?=$v['keywords']?></td>
			<?php if (Elf::$_data['ctype']!=CONTENT_TYPE_FAQ):?>
			<td>
				<?php if ($v['content_type'] != CONTENT_TYPE_VIDEO):?>
					<?php if ($v['picture']):?>
						<img src="<?=$v['picture_icon']?>?<?=rand()?>" alt="<?=$v['picture_alt']?$v['picture_alt']:Elf::lang('content')->item('default.alt')?>" class="icon" />
						<div><?=$v['picture_alt']?$v['picture_alt']:Elf::lang('content')->item('no.alt')?></div>
					<?php else:?>
						-
					<?php endif;?>
				<?php else:?>
				<iframe width="120" height="71" src="https://www.youtube.com/embed/<?=$v['picture']?>" frameborder="0" allowfullscreen="1"></iframe>
				<?php endif;?>
			</td>
			<?php endif;?>
			<td><?=date('d.m.Y',$v['tm'])?><br /><?=date('H:i:s',$v['tm'])?></td>
			<td><?=date('d.m.Y',$v['tm_edit'])?><br /><?=date('H:i:s',$v['tm_edit'])?></td>
			<td>
				<input type="text" name="pos" id="cpos<?=$v['id']?>" value="<?=$v['pos']?>" size="2" class="newpos" />
				<a href="/content/ch_pos/<?=$v['id']?>/up/<?=$v['parent_id']?>/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>" title="<% lang:content:chposprev %>">&uarr;</a> |
				<a href="/content/ch_pos/<?=$v['id']?>/dn/<?=$v['parent_id']?>/<?=$v['paliases']?end($v['paliases']):''?>/<% offset %>" title="<% lang:content:chposnext %>">&darr;</a><br />
				<input type="button" class="newpos" value="<% lang:save %>" title="<% lang:content:setnewpos %>" onclick="setNewPos(<?=$v['id']?>)" />
			</td>
		</tr>
	<?php endforeach;?>
	</table>
<% pagi %>
<?php elseif (!empty(Elf::$_data['content'])):?>
	<div class="flex flex-wrp">
		<?php foreach (Elf::$_data['content'] as $v):?>
		<div class="content-index-item">
			<h3><a href="/content/index/<?=$v['alias']?>" title="<% lang:content:rubric.open %>"><?=$v['title']?> <span title="<% lang:content:rubric.cnt.items %>">(<?=$v['childs_cnt']?>)</span></a>
				<a href="javascript:;" data-params="dialog=content/edit;appearance=appearances/wide;cid=0;parent_alias=<?=$v['alias']?>;parent_id=<?=$v['id']?>;content_type=<?=$v['content_type']?>;caption=<% lang:content:edit.newitem %>" onclick="showDialog(this)" title="<% lang:content:newitem.tlt %>"><i class="fas fa-plus-circle"></i></a></h3>
			<?php if ($v['childs_cnt']):?>
			<div class="content-index-item-blck">
				<?php foreach ($v['items'] as $itm):?>
				<div class="content-index-item-lnk">
				<a href="javascript:;" data-params="dialog=content/edit;appearance=appearances/wide;cid=<?=$itm['id']?>;parent_alias=<?=$v['alias']?>;parent_id=<?=$itm['parent_id']?>;content_type=<?=$itm['content_type']?>;offset=<% offset %>;caption=<% lang:content:edit.item %>" onclick="showDialog(this)" title="<% lang:content:edtitem.tlt %>"><?=$itm['title']?></a>
				<a href="<?=Elf::site_url().($itm['paliases']?$itm['paliases'].'/':'').$itm['alias']?>" target="_blank" title="<% lang:content:openinnewtab %>"><i class="fas fa-external-link-alt"></i></a>
				</div>
				<?php endforeach;?>
				<div class="content-index-item-lnk">
				<a href="/content/index/<?=$v['alias']?>" title="<% lang:content:rubric.open %>">...</a>
				</div>
			</div>
			<?php endif;?>
		</div>
		<?php endforeach;?>
	</div>
<?php else:?>
<div class="alert">
<% lang:datanotfound %>
</div>
<?php endif;?>	