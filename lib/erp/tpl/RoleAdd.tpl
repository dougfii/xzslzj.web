<script type="text/javascript">
	var sgrouplist = new Array(); //起源组织
	var sorglist = new Array();   //起源部门
	var suserlist = new Array();  //起源用户
	var tgrouplist = new Array(); //目标组织
	var torglist = new Array();   //目标部门
	var tuserlist = new Array();  //目标用户

	var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};

	var settingSGroup={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickSGroup}};
	var settingSOrg={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickSOrg}};
	var settingSUser={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickSUser}};
	var settingTGroup={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickTGroup}};
	var settingTOrg={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickTOrg}};
	var settingTUser={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClickTUser}};


	var nodesSGroup=<?php echo $treeSGroup; ?>;
	var nodesSOrg=<?php echo $treeSOrg; ?>;
	var nodesSUser=<?php echo $treeSUser; ?>;
	var nodesTGroup=<?php echo $treeTGroup; ?>;
	var nodesTOrg=<?php echo $treeTOrg; ?>;
	var nodesTUser=<?php echo $treeTUser; ?>;


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
	function onClickSGroup(e, treeId, node) {
		if(node.level>0 && node.type==1){
			var exist = false;
			for(var i=0; i<sgrouplist.length; i++){
				if(sgrouplist[i] == node.gid){
					exist = true;
					break;
				}
			}
			if(!exist){
				sgrouplist.push(node.gid);
				$('#sgrouplist').append('<span class="uitem" did="' + node.gid + '">' + node.name + '</span> ');
			}
		}
	}
	function onClickSOrg(e, treeId, node) {
		if(node.level>0 && node.type==2){
			var exist = false;
			for(var i=0; i<sorglist.length; i++){
				if(sorglist[i] == node.oid){
					exist = true;
					break;
				}
			}
			if(!exist){
				sorglist.push(node.oid);
				$('#sorglist').append('<span class="uitem" did="' + node.oid + '">' + node.name + '</span> ');
			}
		}
	}
	function onClickSUser(e, treeId, node) {
		if(node.level>0 && node.type==3){
			var exist = false;
			for(var i=0; i<suserlist.length; i++){
				if(suserlist[i] == node.uid){
					exist = true;
					break;
				}
			}
			if(!exist){
				suserlist.push(node.uid);
				$('#suserlist').append('<span class="uitem" did="' + node.uid + '">' + node.name + '</span> ');
			}
		}
	}
	function onClickTGroup(e, treeId, node) {
		if(node.level>0 && node.type==1){
			var exist = false;
			for(var i=0; i<tgrouplist.length; i++){
				if(tgrouplist[i] == node.gid){
					exist = true;
					break;
				}
			}
			if(!exist){
				tgrouplist.push(node.gid);
				$('#tgrouplist').append('<span class="uitem" did="' + node.gid + '">' + node.name + '</span> ');
			}
		}
	}
	function onClickTOrg(e, treeId, node) {
		if(node.level>0 && node.type==2){
			var exist = false;
			for(var i=0; i<torglist.length; i++){
				if(torglist[i] == node.oid){
					exist = true;
					break;
				}
			}
			if(!exist){
				torglist.push(node.oid);
				$('#torglist').append('<span class="uitem" did="' + node.oid + '">' + node.name + '</span> ');
			}
		}
	}
	function onClickTUser(e, treeId, node) {
		if(node.level>0 && node.type==3){
			var exist = false;
			for(var i=0; i<tuserlist.length; i++){
				if(tuserlist[i] == node.uid){
					exist = true;
					break;
				}
			}
			if(!exist){
				tuserlist.push(node.uid);
				$('#tuserlist').append('<span class="uitem" did="' + node.uid + '">' + node.name + '</span> ');
			}
		}
	}

	function dblClickExpand(treeId, treeNode) {
		return treeNode.level > 0;
	}

	$(function(){
		$.fn.zTree.init($("#treeSGroup"), settingSGroup, nodesSGroup);
		$.fn.zTree.init($("#treeSOrg"), settingSOrg, nodesSOrg);
		$.fn.zTree.init($("#treeSUser"), settingSUser, nodesSUser);
		$.fn.zTree.init($("#treeTGroup"), settingTGroup, nodesTGroup);
		$.fn.zTree.init($("#treeTOrg"), settingTOrg, nodesTOrg);
		$.fn.zTree.init($("#treeTUser"), settingTUser, nodesTUser);


		$('#ok').click(function(){
			var sgroupi = $('#sgroupi').is(':checked') ? 1 : 0;
			var sorgi = $('#sorgi').is(':checked') ? 1 : 0;
			var tgroupi = $('#tgroupi').is(':checked') ? 1 : 0;
			var torgi = $('#torgi').is(':checked') ? 1 : 0;

			var act = $('#act').is(':checked') ? 1 : 0;
			var sgroup = sgrouplist.join(',');
			var sorg = sorglist.join(',');
			var suser = suserlist.join(',');
			var tgroup = tgrouplist.join(',');
			var torg = torglist.join(',');
			var tuser = tuserlist.join(',');

			var s = '';
			$('[name=permission]:checked').each(function(){ s += $(this).val() + ','; });
			$.post('?m=System&a=OnRoleAdd', {name:$('#name').val(),sgroupi:sgroupi,sorgi:sorgi,sgroup:sgroup,sorg:sorg,suser:suser,tgroupi:tgroupi,torgi:torgi,tgroup:tgroup,torg:torg,tuser:tuser,s:s,act:act}, function (ret){if(ret.code==1)layer.msg('增加成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
		});

		$('#sgrouplist').on('dblclick', 'uitem', function(){
			for(var i=0; i<sgrouplist.length; i++){
				if(sgrouplist[i] == $(this).attr('did')){
					sgrouplist.splice(i, 1);
				}
			}
			$(this).remove();
		});
		$('#sorglist').on('dblclick', 'uitem', function(){
			for(var i=0; i<sorglist.length; i++){
				if(sorglist[i] == $(this).attr('did')){
					sorglist.splice(i, 1);
				}
			}
			$(this).remove();
		});
		$('#suserlist').on('dblclick', 'uitem', function(){
			for(var i=0; i<suserlist.length; i++){
				if(suserlist[i] == $(this).attr('did')){
					suserlist.splice(i, 1);
				}
			}
			$(this).remove();
		});
		$('#tgrouplist').on('dblclick', 'uitem', function(){
			for(var i=0; i<tgrouplist.length; i++){
				if(tgrouplist[i] == $(this).attr('did')){
					tgrouplist.splice(i, 1);
				}
			}
			$(this).remove();
		});
		$('#torglist').on('dblclick', 'uitem', function(){
			for(var i=0; i<torglist.length; i++){
				if(torglist[i] == $(this).attr('did')){
					torglist.splice(i, 1);
				}
			}
			$(this).remove();
		});
		$('#tuserlist').on('dblclick', 'uitem', function(){
			for(var i=0; i<tuserlist.length; i++){
				if(tuserlist[i] == $(this).attr('did')){
					tuserlist.splice(i, 1);
				}
			}
			$(this).remove();
		});
	});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">增加角色</span></div>
</div>

<div class="panel paneltool">
<table class="ti">
	<tr>
		<td class="k">角色名称</td>
		<td class="v"><input type="text" id="name" class="r" /></td>
		<td class="k">启用状态</td>
		<td class="v"><input type="checkbox" id="act" checked /></td>
	</tr>
	<tr>
		<td class="k">起源组织</td>
		<td class="v"><input type="text" id="sgroup" class="r" readonly onclick="TreeBox(this)" /><div id="sgroupbox" class="treebox"><ul id="treeSGroup" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /><input type="checkbox" id="sgroupi" /> 子组织继承权限</td>
		<td class="k">起源组织列表</td>
		<td class="v"><div id="sgrouplist" class="sgrouplist"></div></td>
		<td class="k">目标组织</td>
		<td class="v"><input type="text" id="tgroup" class="r" readonly onclick="TreeBox(this)" /><div id="tgroupbox" class="treebox"><ul id="treeTGroup" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /><input type="checkbox" id="tgroupi" /> 子组织继承权限</td>
		<td class="k">目标组织列表</td>
		<td class="v"><div id="tgrouplist" class="tgrouplist"></div></td>
	</tr>
	<tr>
		<td class="k">起源部门</td>
		<td class="v"><input type="text" id="sorg" class="r" readonly onclick="TreeBox(this)" /><div id="sorgbox" class="treebox"><ul id="treeSOrg" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /><input type="checkbox" id="sorgi" /> 子部门继承权限</td>
		<td class="k">起源部门列表</td>
		<td class="v"><div id="sorglist" class="sorglist"></div></td>
		<td class="k">目标部门</td>
		<td class="v"><input type="text" id="torg" class="r" readonly onclick="TreeBox(this)" /><div id="torgbox" class="treebox"><ul id="treeTOrg" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /><input type="checkbox" id="torgi" /> 子部门继承权限</td>
		<td class="k">目标部门列表</td>
		<td class="v"><div id="torglist" class="torglist"></div></td>
	</tr>
	<tr>
		<td class="k">起源用户</td>
		<td class="v"><input type="text" id="suser" class="r" readonly onclick="TreeBox(this)" /><div id="suserbox" class="treebox"><ul id="treeSUser" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /></td>
		<td class="k">起源用户列表</td>
		<td class="v"><div id="suserlist" class="suserlist"></div></td>
		<td class="k">目标用户</td>
		<td class="v"><input type="text" id="tuser" class="r" readonly onclick="TreeBox(this)" /><div id="tuserbox" class="treebox"><ul id="treeTUser" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /></td>
		<td class="k">目标用户列表</td>
		<td class="v"><div id="tuserlist" class="tuserlist"></div></td>
	</tr>
</table>
</div>
<div class="bpan"><input type="button" id="ok" class="bok" value="确定" />　　<input type="button" class="bback" value="返回" /></div>
<div class="panel paneltool">
<?php echo $permission; ?>
</div>
