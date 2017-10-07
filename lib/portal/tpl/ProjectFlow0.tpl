<script>
    $(function(){
        $('#flow1step1').click(function(){
            $.post('?m=Member&a=OnFLow1Step1', {id:$('#id').val(),no:$('#no').val(),signer:$('#signer').val(),content:$('#content').val(),date:$('#date').val(),keywords:$('#keywords').val()}, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap"><?php echo $rs['n0n']; ?> - [<?php echo $rs['s0n']; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $rs['act']?'项目审批通过':'项目立项正在审批中，请稍后....'; ?></div>
        </div>
    </div>
</div>
