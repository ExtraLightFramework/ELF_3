<?php
	$cont = new \Elf\App\Models\Content;
//	if ((int)Elf::get_data('cid')) {
		$rec = $cont->_get((int)Elf::get_data('cid'), true, Elf::$_data['parent_id']);
//	}
	$ctype = Elf::get_data('content_type')?Elf::get_data('content_type'):'unset';
?>

<form action="/content/_edit" method="post">
	<input type="hidden" name="id" value="<?=isset($rec['id'])?$rec['id']:0?>" />
	<input type="hidden" name="offset" value="<% offset %>" />
	<input type="hidden" name="content_type" value="<% ctype %>" />
	<input type="hidden" name="parent_alias" value="<% parent_alias %>" />
	<table class="dialog dialog-liner">
		<tr>
			<td class="wdth25">
				<div class="dialog-field-tlt"><% lang:content:date %></div>
				<input class="nowide date" size="12" type="text" name="tm"
					value="<?=isset($rec['tm'])?date('d.m.Y',$rec['tm']):date('d.m.Y')?>" onfocus="this.select();lcs(this)"
					onclick="event.cancelBubble=true;this.select();lcs(this)" readonly="readonly" />
			</td>
			<td class="wdth25"><div class="dialog-field-tlt"><% lang:content:rubric.parent %></div>
				<?=$cont->rubric_selector(0,isset($rec['parent_id'])?$rec['parent_id']:Elf::$_data['parent_id'])?>
			</td>
			<td class="wdth25"><div class="dialog-field-tlt"><% lang:content:pos %></div>
				<input type="number" name="pos" class="nowide" size="3" value="<?=isset($rec['pos'])?$rec['pos']:0?>" />
			</td>
			<td class="wdth25"><div class="dialog-field-tlt"><% lang:content:hotitem.tlt %></div>
				<input type="checkbox" name="hot" <?=isset($rec['hot'])&&$rec['hot']?'checked="checked"':''?> class="nowide" />
			</td>
		</tr>
		<tr><td colspan="4"><div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.name')?></div><input type="text" name="title" value="<?=isset($rec['title'])?$rec['title']:''?>" required="required" onkeyup="content_alias_checker(this.value,<?=isset($rec['id'])?$rec['id']:0?>)" /></td></tr>
		<tr><td class="wdth50" id="content-alias-checker" colspan="2"><!-- <div class="dialog-field-tlt"><% lang:content:alias %></div> --><strong title="<% lang:content:alias %>" class="content-alias"><?=isset($rec['alias'])?$rec['alias']:'-'?></strong><input type="hidden" name="alias" value="<?=!empty($rec['alias'])?$rec['alias']:''?>" /><div class="mini-alert mini-alert-alias hide"><% lang:content:alias.notunique %></div></td>
			<td class="wdth50" colspan="2"><!-- <div class="dialog-field-tlt">URL</div> --><?php if (isset($rec['paliases'])):?><a href="{site_url}<?=implode('/',$rec['paliases'])?>/<?=$rec['alias']?>" target="_blank" title="URL">{site_url}<?=implode('/',$rec['paliases'])?>/<?=$rec['alias']?></a><?php else:?>-<?php endif;?></td></tr>
		<tr><td colspan="4"><div class="dialog-field-tlt"><% lang:content:itemtags %></div>
			
				<input type="text" name="tags" placeholder="<% lang:content:itemtags.plc %>" /><br />
				<?php if (!empty($rec['tags'])):?>
				<a href="javascript:;" onclick="$('#content-tags').toggle()"><% lang:content:itemtags.added %></a>
				<div id="content-tags">
					<?php foreach ($rec['tags'] as $c):?>
						<div class="tag-content-item" id="ctag-<?=$c['id']?>-<?=$rec['id']?>">
							<a href="/tag/<?=urlencode($c['htag'])?>" target="_blank" title="<% lang:tags.searchrecs %>">#<?=$c['htag']?></a>
							<a href="javascript:;" title="<% lang:tags.removecontentag %>" class="del"
								onclick="rem_content_tag(<?=$c['id']?>,<?=$rec['id']?>)">x</a>
						</div>
					<?php endforeach;?>
				</div>
				<?php else:?>
				<% lang:content:itemtags.notfound %>
				<?php endif;?>
			</td>
		</tr>
		<tr><td class="wdth50" colspan="2"><div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.desc')?></div><textarea name="description" placeholder="<% lang:content:canclear %>" rows="4"><?=isset($rec['description'])?$rec['description']:''?></textarea></td>
			<td class="wdth50" colspan="2"><div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.keywords')?></div><textarea name="keywords" placeholder="<% lang:content:canclear %>" rows="4"><?=isset($rec['keywords'])?$rec['keywords']:''?></textarea></td></tr>
		<?php if (($ctype != CONTENT_TYPE_FAQ) && ($ctype != CONTENT_TYPE_VIDEO)):?>
		<tr><td class="wdth50" colspan="2">
				<div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.picture')?></div>
				<div id="upl_pictures" class="elf-uploader-cont"></div>
			</td>
			<td class="wdth50" colspan="2">
				<div class="dialog-field-tlt"><% lang:content:picturealt %></div>
				<input type="text" name="picture_alt" maxlength="70" value="<?=isset($rec['picture_alt'])?$rec['picture_alt']:''?>" />
			</td>
		</tr>
		<?php endif;?>
		<?php if (!in_array($ctype,[CONTENT_TYPE_VIDEO,CONTENT_TYPE_FAQ])):?>
		<tr>
			<td colspan="4" class="cntrl">
				<div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.first_p')?></div>
				<textarea class="ckeditor" id="contentfirstp" name="first_p" rows="5"><?=isset($rec['first_p'])?$rec['first_p']:''?></textarea>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td colspan="4" class="cntrl">
				<?php if ($ctype != CONTENT_TYPE_VIDEO):?>
				<div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.text')?></div>
				<textarea class="ckeditor pic-adder-textarea" id="contenttext" name="text" rows="5"><?=isset($rec['text'])?$rec['text']:''?></textarea>
				<?php else:?>
				<textarea name="text" rows="5" placeholder="<% lang:content:contedt.video.desc.on.page %>"><?=isset($rec['text'])?$rec['text']:''?></textarea>
				<div class="dialog-field-tlt"><?=Elf::lang('content')->item('contedt.'.$ctype.'.title.text')?></div>
				<input type="text" class="notmarg" name="picture" value="<?=isset($rec['picture'])?'https://www.youtube.com/embed/'.$rec['picture']:''?>" required="required" />
				<?php endif;?>
				<center>
					<input class="sbmt cntrl" type="submit" id="content-edit-sbmt" value="<% lang:save %>" />
					<input type="button" value="<% lang:cancel %>" class="cncl cntrl" onclick="hideDialog(<% wid %>)" />
				</center>
			</td>
		</tr>
	</table>
</form>

<script>
<?php if (($ctype != CONTENT_TYPE_FAQ) && ($ctype != CONTENT_TYPE_VIDEO)):?>
var upl_pictures = new ELF_Uploader('upl_pictures','content','picture',<?=!empty($rec['picture_icon'])?json_encode(array($rec['picture_icon'])):'false'?>,false,<?=json_encode(['rem_func'=>'/uploader/rem_file','upload_func'=>'/uploader/async_upload'])?>);
<?php endif;?>
</script>

