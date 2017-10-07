<script>
	$(function(){
		$('#ok').click(function(){
			$.post('?m=Article&a=OnArticleCopyright', {content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('保存成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
		});
	});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">页尾版权</span></div>
</div>
<div class="panel paneltool">
	<div>回车换行，每行占显示的一行，默认居中对齐；支持HTML代码；</div>
	<div><textarea id="content" class="copyright" rows="20"><?php echo $content; ?></textarea></div>
	<div class="bpan"><input type="button" id="ok" class="bok" value="保存"/><input type="button" class="bback" value="返回"/></div>
</div>