<script type="text/javascript">
	var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
	var setting1={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClick1}};
	var nodes=<?php echo $tree; ?>;
	var nodes1=<?php echo $tree1; ?>;
	function onClick(e, treeId, node) {
		if(node.type==1){
			Redirect('?m=System&a=GroupList&gid=' + node.gid);
		}
		else if(node.type==2){
			Redirect('?m=System&a=GroupList&gid=' + node.gid + '&oid=' + node.oid);
		}
		else{
			Redirect('?m=System&a=UserEdit&id=' + node.uid + '&oid='+node.oid);
		}
	}
	function onClick1(e, treeId, node) {
		if(node.level>0 && node.type==1){
			$('#group').val(node.name);
			$('#gid').val(node.gid);
			$('#fid').val('0');
		}
		else if(node.level>0 && node.type==2){
			$('#group').val(node.name);
			$('#gid').val('-1');
			$('#fid').val(node.fid);
		}
		else{
			$('#group').val('');
			$('#fid').val('');
		}
	}
	function dblClickExpand(treeId, treeNode) {
		return treeNode.level > 0;
	}

	$(function(){
		$.fn.zTree.init($("#tree"), setting, nodes);
		$.fn.zTree.init($("#tree1"), setting1, nodes1);

		$('#ok').click(function(){
			$.post('?m=System&a=OnOrgEdit', {id:$('#id').val(),fid:$('#fid').val(),gid:$('#gid').val(),name:$('#name').val()}, function (ret){if(ret.code==1)layer.msg('编辑成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
		});
	});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">编辑部门</span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
	<?php
	if(!empty($rs)):
	?>
	<table class="ti">
		<tr>
			<td class="k">上级部门</td>
			<td class="v"><input type="text" id="group" class="r" readonly onclick="TreeBox(this)" value="<?php echo $name; ?>" /><div id="groupbox" class="treebox"><ul id="tree1" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /></td>
			<td class="k">部门名称</td>
			<td class="v"><input type="text" id="name" class="r" value="<?php echo $rs['name']; ?>"/></td>
			<td class="k"></td>
			<td class="v"></td>
			<td class="k"></td>
			<td class="v"></td>
		</tr>
	</table>
	<input type="hidden" id="id" value="<?php echo $rs['id']; ?>" />
	<input type="hidden" id="gid" value="<?php echo $rs['gid']; ?>" />
	<div class="bpan"><input type="button" id="ok" class="bok" value="确定" /><input type="button" class="bback" value="返回" /></div>
	<?php
	else:
		echo HTML::AlertError();
	endif;
	?>
</div>