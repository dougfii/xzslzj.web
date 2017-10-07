<script>
    $(function(){
        $('#bayes').click(function(){
            $.post('?m=Project&a=OnProjectReply1', {fid:$('#fid').val(),no:$('#no').val(),content:$('#content').val(),date:$('#date').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">项目划分确认的批复 - <批示></span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool pagebg">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center"><?php echo $rs['no']; ?></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于对<?php echo $name; ?>项目划分确认的批复</div>
            <div class="pa4-company"><?php echo $company; ?>:</div>
            <div class=""><?php echo $rs['content']; ?></div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $gc; ?><br/><?php echo $rs['date']; ?></span></div>
        </div>
    </div>
</div>
