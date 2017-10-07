<script type="text/javascript">
ShowApproveCondition();

var box_auditor = null;

var odlist = new Array();
var pdlist = new Array();
var oulist = new Array();
var pulist = new Array();
var odsetting = {data:{simpleData:{enable: true,idKey: 'id',pIdKey: 'fid'}},view: {dblClickExpand: dblClickExpand},callback: {onDblClick: onDblClickOD}};
var pdsetting = {data: {simpleData: {enable: true,idKey: 'id',pIdKey: 'fid'}},view: {dblClickExpand: dblClickExpand},callback: {onDblClick: onDblClickPD}};
var ousetting = {data: {simpleData: {enable: true,idKey: 'id',pIdKey: 'fid'}},view: {dblClickExpand: dblClickExpand},callback: {onDblClick: onDblClickOU}};
var pusetting = {data: {simpleData: {enable: true,idKey: 'id',pIdKey: 'fid'}},view: {dblClickExpand: dblClickExpand},callback: {onDblClick: onDblClickPU}};

var dnodes = <?php echo $dtree; ?>;
var unodes = <?php echo $utree; ?>;
function dblClickExpand(treeId, treeNode) {
	return treeNode.level > 0;
}
function onDblClickOD(e, treeId, node) {
	if(node.level > 0){
		var exist = false;
		for(var j=0; j<odlist.length; j++){
			if(odlist[j] == node.sid){
				exist = true;
				break;
			}
		}
		if(!exist){
			odlist.push(node.sid);
			$('#odlist').append('<span class="uitem" did="' + node.sid + '">' + node.name + '</span> ');
		}
	}
}
function onDblClickPD(e, treeId, node) {
	if(node.level > 0){
		var exist = false;
		for(var j=0; j<pdlist.length; j++){
			if(pdlist[j] == node.sid){
				exist = true;
				break;
			}
		}
		if(!exist){
			pdlist.push(node.sid);
			$('#pdlist').append('<span class="uitem" did="' + node.sid + '">' + node.name + '</span> ');
		}
	}
}
function onDblClickOU(e, treeId, node) {
	if(node.level > 0){
		var exist = false;
		for(var j=0; j<oulist.length; j++){
			if(oulist[j] == node.sid){
				exist = true;
				break;
			}
		}
		if(!exist){
			oulist.push(node.sid);
			$('#oulist').append('<span class="uitem" did="' + node.sid + '">' + node.name + '</span> ');
		}
	}
}
function onDblClickPU(e, treeId, node) {
	if(node.level > 0){
		var exist = false;
		for(var j=0; j<pulist.length; j++){
			if(pulist[j] == node.sid){
				exist = true;
				break;
			}
		}
		if(!exist){
			pulist.push(node.sid);
			$('#pulist').append('<span class="uitem" did="' + node.sid + '">' + node.name + '</span> ');
		}
	}
}
function ShowODepartmentBox() {
	var obj = $('#odepartment');
	var offset = $('#odepartment').offset();
	$('#odepartmentbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnODepartmentDown);
}
function HideODepartmentBox() {
	$('#odepartmentbox').fadeOut('fast');
	$('body').unbind('mousedown', OnODepartmentDown);
}
function OnODepartmentDown(event) {
	if (!(event.target.id == 'odepartment' || event.target.id == 'odepartmentbox' || $(event.target).parents('#odepartmentbox').length > 0)) HideODepartmentBox();
}
function ShowPDepartmentBox() {
	var obj = $('#pdepartment');
	var offset = $('#pdepartment').offset();
	$('#pdepartmentbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnPDepartmentDown);
}
function HidePDepartmentBox() {
	$('#pdepartmentbox').fadeOut('fast');
	$('body').unbind('mousedown', OnPDepartmentDown);
}
function OnPDepartmentDown(event) {
	if (!(event.target.id == 'pdepartment' || event.target.id == 'pdepartmentbox' || $(event.target).parents('#pdepartmentbox').length > 0)) HidePDepartmentBox();
}
function ShowOUserBox() {
	var obj = $('#ouser');
	var offset = $('#ouser').offset();
	$('#ouserbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnOUserDown);
}
function HideOUserBox() {
	$('#ouserbox').fadeOut('fast');
	$('body').unbind('mousedown', OnOUserDown);
}
function OnOUserDown(event) {
	if (!(event.target.id == 'ouser' || event.target.id == 'ouserbox' || $(event.target).parents('#ouserbox').length > 0)) HideOUserBox();
}
function ShowPUserBox() {
	var obj = $('#puser');
	var offset = $('#puser').offset();
	$('#puserbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnPUserDown);
}
function HidePUserBox() {
	$('#puserbox').fadeOut('fast');
	$('body').unbind('mousedown', OnPUserDown);
}
function OnPUserDown(event) {
	if (!(event.target.id == 'puser' || event.target.id == 'puserbox' || $(event.target).parents('#puserbox').length > 0)) HidePUserBox();
}

$(function(){
	$('#department').click(function(){layer.pop('?m=System&a=PopDepartmentSelect','选择部门');});
	
	$.fn.zTree.init($("#odtree"), odsetting, dnodes);
	$.fn.zTree.init($('#pdtree'), pdsetting, dnodes);
	$.fn.zTree.init($('#outree'), ousetting, unodes);
	$.fn.zTree.init($('#putree'), pusetting, unodes);
	
    $('#ok').click(function(){
    	var act = $('#act').is(':checked') ? 1 : 0;
    	
		var oinherit = $('#oinherit').is(':checked') ? 1 : 0;
		var pinherit = $('#pinherit').is(':checked') ? 1 : 0;
		var act = $('#act').is(':checked') ? 1 : 0;
    	var pd = pdlist.join(',');
    	var pu = pulist.join(',');
    	
    	$.post('?m=System&a=OnApproveAdd', {gid:$('#gid').val(),departmentid:$('#departmentid').val(),act:act,approveobjectid:$('#approveobjectid').val(),cause:$('#cause').val(),pu:pu,flow:$('#flow').val(),},
            function(msg){
                if(msg == 1){
                    alert('审批增加成功');
                    location.href='<?php echo Url::REFERER(); ?>';
                }
                else{
					alert(msg);
                }
        	});
    });
    
    $('#btntest').click(function(){
    	alert("00000000000000");
    });
    
    $('#approvegroup').on('click', '.addgroup', function(){
        $.post('?m=System&a=OnApproveGroupAdd', function(){ ShowApproveGroup(); });
    });
    
    $('#approvegroup').on('click', '.deletegroup', function(){
        $.post('?m=System&a=OnApproveGroupDelete', {id:$(this).attr('did')}, function(){ ShowApproveGroup(); });
    });
    
    $('#approvegroup').on('click', '.addauditor', function(){
        box_auditor = $(this).jbox({
			title: '选择用户',
			target: "?m=System&a=AuditorList&gid="+$(this).attr('gid')
		});
    });
    
    $('#approvegroup').on('click', '.deleteauditor', function(){
        $.post('?m=System&a=OnApproveAuditorDelete', {gid:$(this).attr('gid'),id:$(this).attr('did')}, function(){ ShowApproveGroup(); });
    });
    
    $('#approveobjectid').change(function(){
    	location.href='<?php echo Url::URL_FULL(); ?>&approveobjectid=' + $(this).val();
    });
    
    $('#addcondition').click(function(){
        $.post('?m=System&a=OnApproveConditionAdd', {object:$('#approveobjectid').val(),field:$('#field').val(),operator:$('#operator').val(),value:$('#value').val()},
            function(msg){
            	if(msg==1){
                	ShowApproveCondition();
                	$('#value').val('');
                }
                else{
                	alert(msg);
                }
            });
    });

	$('#approvecondition').on('click', '.deletecondition', function(){
        $.post('?m=System&a=OnApproveConditionDelete', {id:$(this).attr('did')}, function(){ ShowApproveCondition(); });
    });
    
    $('#syntax').click(function(){
        $.post('?m=System&a=OnApproveCheckSyntax', {content:$('#flow').val()}, function(msg){ alert(msg); });
    });
    

    $('#odlist').on('dblclick', '.uitem', function(){
    	for(var i=0; i<odlist.length; i++){
    		if(odlist[i] == $(this).attr('did')){
    			odlist.splice(i, 1);
    		}
    	}
    	$(this).remove();
    });
    $('#pdlist').on('dblclick', '.uitem', function(){
    	for(var i=0; i<pdlist.length; i++){
    		if(pdlist[i] == $(this).attr('did')){
    			pdlist.splice(i, 1);
    		}
    	}
    	$(this).remove();
    });
    $('#oulist').on('dblclick', '.uitem', function(){
    	for(var i=0; i<oulist.length; i++){
    		if(oulist[i] == $(this).attr('did')){
    			oulist.splice(i, 1);
    		}
    	}
    	$(this).remove();
    });
    $('#pulist').on('dblclick', '.uitem', function(){
    	for(var i=0; i<pulist.length; i++){
    		if(pulist[i] == $(this).attr('did')){
    			pulist.splice(i, 1);
    		}
    	}
    	$(this).remove();
    });
});
</script>
<div class="cap"><div class="capl">增加审批</div></div>
<div class="pan">

<table class="ti">
	<tr>
		<td class="r">组织机构</td>
		<td class="v"><input type="text" id="gname" readonly value="<?php echo $gname; ?>" /><input type="hidden" id="gid" value="<?php echo $gid; ?>" /></td>
		<td class="r">部门</td>
		<td class="v"><input type="text" id="department" readonly /><input type="hidden" id="departmentid" value="<?php echo $id;?>" /> </td>
		<td class="r">状态</td>
		<td class="v"><input type="checkbox" id="act" checked /> 启用这个审批</td>
	</tr>
	<tr>
		<td class="r">审批对象</td>
		<td class="v"><?php echo $approveobjectid . $cause; ?></td>
		<td class="r">审批用户</td>
		<td class="" colspan="3"><input type="text" id="puser" readonly onclick="ShowPUserBox(); return false;" /><div id="puserbox" class="treebox" style="width:184px;"><ul id="putree" class="ztree"></ul></div></td>
	</tr>
	<tr>
		<td class="r">审批条件</td>
		<td class="v"><?php echo $field . $operator; ?><input type="text" id="value" class="input-small" />	<input type="button" id="addcondition" value="添加" /></td>
		<td class="r">审批用户列表</td>
		<td class="v" colspan="3"><div id="pulist" class="oplist"></div></td>
	</tr>
	<tr>
		<td class="r">审批条件列表</td>
		<td class="v"><div id="approvecondition"></div></td>
	</tr>
	<tr>
		<td class="r">审批流程</td>
		<td class="v" colspan="5"><textarea id="flow" class="span10" rows="20"></textarea><br /><button class="btn btn-success" id="syntax">检查语法</button> 审批组函数中必须加参数,例: $group0($approveid, $approveobjectid, $approvetargetid, $cause, $ostate, $fstate);</td>
	</tr>
</table>
<div class="bpan"><input type="button" id="ok" class="bok" value="确定" />　　<input type="button" class="bback" value="返回" /></div>
</div>
