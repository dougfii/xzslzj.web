<div class="toolbar">
	<div class="tool clear"><span class="cap">发表文章</span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
	<table class="ti">
		<tr>
			<td class="k">上级分类</td>
			<td class="v"><input type="text" id="type" class="r" readonly onclick="TreeBox(this)" value="<?php echo $type; ?>" /><div id="typebox" class="treebox"><ul id="tree1" class="ztree"></ul></div><input type="hidden" id="tid" value="<?php echo $tid; ?>" /></td>
			<td class="k">首页轮播</td>
			<td class="v"><input type="checkbox" id="roll" /> 是</td>
			<td class="k">状态</td>
			<td class="v"><input type="checkbox" id="act" checked /> 可用</td>
			<td class="k"></td>
			<td class="v"></td>
		</tr>
		<tr>
			<td class="k">标题</td>
			<td class="v" colspan="7"><input type="text" id="title" class="r" style="width:90%;"/></td>
		</tr>
		<tr>
			<td colspan="8"></td>
		</tr>
	</table>
	<script id="content" name="content" type="text/plain" style="width:100%;height:100%;"></script>
	<div class="bpan"><input type="button" id="ok" class="bok" value="确定" /><input type="button" class="bback" value="返回" /></div>
</div>
<link href="/js/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="/js/umeditor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/js/umeditor/umeditor.min.js"></script>
<script type="text/javascript" src="/js/umeditor/lang/zh-cn/zh-cn.js"></script>
<script>
	var um = UM.getEditor('content');
	var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
	var setting1={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:false},callback:{onClick:onClick1}};
	var nodes=<?php echo $tree; ?>;
	var nodes1=<?php echo $tree1; ?>;
	function onClick(e, treeId, node) {
		Redirect('?m=User&a=ArticleList&id=' + node.id);
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
			var content = UM.getEditor('content').getPlainTxt();
			var html = UM.getEditor('content').getContent()
			var available = UM.getEditor('content').hasContents() ? 1 : 0;
			var roll = $('#roll').is(':checked') ? 1 : 0;
			var act = $('#act').is(':checked') ? 1 : 0;
			$.post('?m=User&a=OnArticleAdd', {tid:$('#tid').val(),title:$('#title').val(),content:content,html:html,available:available,roll:roll,act:act}, function (ret){if(ret.code==1)layer.msg('发表成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
		});
	});
</script>