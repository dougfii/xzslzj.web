<div class="toolbar">
	<div class="tool clear"><span class="cap">发表文章</span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
	<div class="panelbox">
	<table class="ti">
		<tr>
			<td class="k">上级分类</td>
			<td class="v"><input type="text" id="type" class="r" readonly onclick="TreeBox(this)" value="<?php echo $type; ?>" /><div id="typebox" class="treebox"><ul id="tree1" class="ztree"></ul></div><input type="hidden" id="tid" value="<?php echo $tid; ?>" /></td>
			<td class="k">发布时间</td>
			<td class="v"><input type="text" id="time" class="r" readonly onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" value="<?php echo $time; ?>" /></td>
			<td class="k">首页轮播</td>
			<td class="v"><input type="checkbox" id="roll" /> 是</td>
			<td class="k">状态</td>
			<td class="v"><input type="checkbox" id="act" checked /> 可用</td>
		</tr>
		<tr>
			<td class="k">标题</td>
			<td class="v" colspan="7"><input type="text" id="title" class="r" style="width:90%;"/></td>
		</tr>
		<tr>
			<td class="k"><input type="text" id="pic" class="hidden"/><span class="upbtn">上传首图<input type="file" id="upfile" name="upfile" /></span><span class="updel hidden">删除首图</span></td>
			<td class="v" colspan="5"><span id="showpic"></span></td>
			<td colspan="2">首图建议尺寸: 180*120 像素</td>
		</tr>
	</table>
	<script id="content" name="content" type="text/plain" style="width:100%;height:500px;"></script>
	</div>
	<div class="bpan"><input type="button" id="ok" class="bok" value="确定" /><input type="button" class="bback" value="返回" /></div>
</div>
<link href="/js/ueditor/themes/default/css/ueditor.css" type="text/css" rel="stylesheet">
<script src="/js/ueditor/ueditor.config.js"></script>
<script src="/js/ueditor/ueditor.all.min.js"></script>
<script src="/js/ueditor/lang/zh-cn/zh-cn.js"></script>
<script src="/js/ajaxfileupload.js"></script>
<script>
	var ue = UE.getEditor('content');
	var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
	var setting1={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:false},callback:{onClick:onClick1}};
	var nodes=<?php echo $tree; ?>;
	var nodes1=<?php echo $tree1; ?>;
	function onClick(e, treeId, node) {
		Redirect('?m=Article&a=ArticleList&id=' + node.id);
	}
	function onClick1(e, treeId, node) {
		if(node.level>0 && node.type==2){
			$('#type').val(node.name);
			$('#tid').val(node.id);
		}
		else{
			$('#type').val('');
			$('#tid').val('');
		}
	}
	function dblClickExpand(treeId, treeNode) {
		return treeNode.level > 0;
	}

	$(function(){
		$.fn.zTree.init($("#tree"), setting, nodes);
		$.fn.zTree.init($("#tree1"), setting1, nodes1);

		$('#ok').click(function(){
			var content = UE.getEditor('content').getPlainTxt();
			var html = UE.getEditor('content').getContent()
			var available = UE.getEditor('content').hasContents() ? 1 : 0;
			var roll = $('#roll').is(':checked') ? 1 : 0;
			var act = $('#act').is(':checked') ? 1 : 0;
			$.post('?m=Article&a=OnArticleAdd', {tid:$('#tid').val(),pic:$('#pic').val(),time:$('#time').val(),title:$('#title').val(),content:content,html:html,available:available,roll:roll,act:act}, function (ret){if(ret.code==1)layer.msg('发表成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
		});

		$('#upfile').change(function(){upload()});

		$('.updel').click(function(){
			$('#pic').val('');
			$('#showpic').html('');
			$('.updel').hide();
			$('.upbtn').show();
			$('#upfile').change(function(){upload()});
		});

		function upload(){
			$.ajaxFileUpload({
				url:'?m=Upload&a=ArticlePic',
				secureuri:false,
				fileElementId:'upfile',
				dataType:'json',
				success: function(ret, status) {
					$('#ok').prop('disabled', false);
					if(ret.state=='SUCCESS')layer.msg('上传完成', 2, function(){
						$('#pic').val(ret.url);
						$('#showpic').append('<img src="' + ret.url + '" />');
						$('.upbtn').hide();
						$('.updel').show();
					});
					else layer.msg(ret.state, 2, -1);
				},
				error:function(ret, status, e){
					$('#ok').prop('disabled', false);
					layer.msg('上传失败 ' + e, 2, -1);
				}
			});
		}
	});
</script>