<script src="/js/ajaxfileupload.js"></script>
<script>
	$(function(){
		$('#upfile').change(function(){upload()});

		function upload(){
			$.ajaxFileUpload({
				url:'?m=Upload&a=AdvertiserPic',
				secureuri:false,
				fileElementId:'upfile',
				dataType:'json',
				success: function(ret, status) {
					if(ret.state=='SUCCESS') layer.msg('上传完成', 1, function(){$('#pic').val(ret.url);});
					else layer.msg(ret.state, 2, -1);
				},
				error:function(ret, status, e){
					layer.msg('上传失败 ' + e, 2, -1);
				}
			});
		};

		$('#ok').click(function(){
			$.post('?m=Article&a=OnArticleAdvertiserAdd', {pic:$('#pic').val(),sort:$('#sort').val()}, function (ret){if(ret.code==1)layer.msg('增加完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
		});

		$('.del').click(function(){
			var id=$(this).attr('did');
			layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Article&a=OnArticleAdvertiserDelete', {id:id}, function(ret){if(ret.code==1)location.reload();else layer.msg(ret.msg, 2, -1);}, 'json');});
		});
	});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">友情链接</span><span class="find clear"><input type="text" id="pic" class="hidden"/><span class="up">上传图片<input type="file" id="upfile" name="upfile" /></span><span>排序</span><input type="text" id="sort" value="1" size="1" class="center"/><span><input type="button" class="" id="ok" value="增加"/></span></div>
</div>
<div class="panel paneltool">
<?php
	echo '<table class="tl"><tr><th class="fid">序号</th><th>图片 (建议尺寸 1920 * 550 像素)</th><th class="fopr">排序</th><th class="fopr">操作</th></tr>';
	if(!empty($rs))
	{
		foreach($rs as $k=>$v)
		{
			echo '<tr><td class="fid">' . $k . '</td><td><img src="' . $v['pic'] . '" class="auto" /></td><td class="fcet">' . $v['sort'] . '</td><td class="fopr"><a href="?m=Article&a=ArticleAdvertiserEdit&id=' . $k . '">编辑</a> <a href="#" class="del" did="' . $k . '">删除</a></td></tr>';
		}
	}
	echo '</table>';
?>
</div>