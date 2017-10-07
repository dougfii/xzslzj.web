<script type="text/javascript">
ShowApproveGroup();
ShowApproveCondition();

var box_auditor = null;

$(function(){
    $('#ok').click(function(){
    	var act = $('#act').is(':checked') ? 1 : 0;
        $.post('?m=System&a=OnApproveEdit', {id:$('#id').val(),approveobjectid:$('#approveobjectid').val(),cause:$('#cause').val(),flow:$('#flow').val(),act:act},
            function(msg){
                if(msg == 1){
                    alert('审批编辑成功');
                    location.href='<?php echo Url::REQUEST_URI(); ?>';
                }
                else{
					alert(msg);
                }
            });
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
});
</script>
<?php
if(!empty($rs)):
?>
<div class="clearfix caption"><h4 class="pull-left">编辑审批</h4></div>
<table class="table table-input">
	<tr>
		<td class="req span2">审批对象</td>
		<td class="val span4"><input type="text" id="approveobject" class="input-medium" value="<?php echo $approveobject; ?>" readonly /><input type="hidden" id="approveobjectid" value="<?php echo $rs['approveobjectid']; ?>" /><?php echo $cause; ?></td>
		<td class="req span2">状态</td>
		<td class="val span4"><input type="checkbox" id="act" <?php if($rs['act']) echo 'checked'; ?> /> 启用这个审批</td>
	</tr>
	<tr>
		<td class="req">审批条件</td>
		<td class="val"><?php echo $field . $operator; ?><input type="text" id="value" class="input-small" /><button class="btn" id="addcondition" type="button"><i class="icon-plus"></i></button></td>
		<td class="req"></td>
		<td class="val"></td>
	</tr>
	<tr>
		<td class="req">审批条件列表</td>
		<td class="val"><div id="approvecondition"></div></td>
		<td class="req">审批组列表</td>
		<td class="val"><div id="approvegroup"></div></td>
	</tr>
	<tr>
		<td class="req">审批流程</td>
		<td class="val" colspan="3"><textarea id="flow" class="span10" rows="30"><?php echo $rs['flow']; ?></textarea><br /><button class="btn btn-success" id="syntax">检查语法</button> 审批组函数中必须加参数,例: $group0($approveid, $approveobjectid, $approvetargetid, $cause, $ostate, $fstate);</td>
	</tr>
</table>
<input type="hidden" id="id" value="<?php echo $rs['id']; ?>" />
<div class="btn-panel"><button id="ok" class="btn btn-primary">确定</button>　　<button class="btn" onclick="history.back()">返回</button></div>
<?php
else:
	echo HTML::AlertError();
endif;
?>