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
		if(node.level>0 && node.type==2){
			$('#group').val(node.name);
			$('#fid').val(node.oid);
			$('#gid').val(node.gid);
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
			var lead = $('#lead').is(':checked') ? 1 : 0;
			var act = $('#act').is(':checked') ? 1 : 0;
			var admin = $('#admin').is(':checked') ? 1 : 0;
			$.post('?m=System&a=OnUserAdd', {mobile:$('#mobile').val(),email:$('#email').val(),username:$('#username').val(),name:$('#name').val(),fid:$('#fid').val(),gid:$('#gid').val(),password:$('#password').val(),sex:$('input[name=sex]:checked').val(),lead:lead,act:act,serial:$('#serial').val(),admin:admin}, function (ret){if(ret.code==1)layer.msg('增加成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
		});
	});
</script>

<div class="toolbar">
	<div class="tool clear"><span class="cap">增加用户</span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
	<table class="ti">
		<tr>
			<td class="k">登录名称</td>
			<td class="v"><input type="text" id="username" /></td>
			<td class="k">姓名</td>
			<td class="v"><input type="text" id="name" /></td>
			<td class="k">所属部门</td>
			<td class="v"><input type="text" id="group" class="r" readonly onclick="TreeBox(this)" /><div id="groupbox" class="treebox"><ul id="tree1" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /><input type="hidden" id="gid" value="<?php echo $gid; ?>" /></td>
			<td class="k">密码</td>
			<td class="v"><input type="text" id="password" value="123456" /></td>
		</tr>
		<tr>
			<td class="k">性别</td>
			<td class="v"><input type="radio" name="sex" value="0" checked />女 <input type="radio" name="sex" value="1" />男</td>
			<td class="k">手机</td>
			<td class="v"><input type="text" id="mobile" /></td>
			<td class="k">邮箱</td>
			<td class="v"><input type="text" id="email" /></td>
			<td class="k">员工编号</td>
			<td class="v"><input type="text" id="serial" /></td>
		</tr>
		<tr>
			<td class="k">部门负责人</td>
			<td class="v"><input type="checkbox" id="lead" /></td>
			<td class="k">登录状态</td>
			<td class="v"><input type="checkbox" id="act" checked /></td>
			<td class="k">超级管理权限</td>
			<td class="v"><input type="checkbox" id="admin" /></td>
			<td class="k"></td>
			<td class="v"></td>
		</tr>
	</table>
	<div class="bpan"><input type="button" id="ok" class="bok" value="确定"/><input type="button" class="bback" value="返回"/></div>
</div>