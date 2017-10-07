<script>
    $(function(){
        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow3Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if(!empty($rs)): ?><a class="tooladd" href="?m=Project&a=ProjectReply4&fid=<?php echo $fid; ?>">再次发送意见</a><?php endif; ?></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rs)):
    ?>
    <div class="pagea4">
        <div class="pagea4info">

            <div class="pa4-caption1">关于<?php echo $name; ?>工程质量监督检查意见落实整改情况的说明</div>
            <div class="clear"><span>文件编号:<?php echo $rs['no']; ?></span><span class="right">签发人:<?php echo $rs['signer']; ?></span></div>
            <div class="pa4-redline"></div>
            <div class="pa4-company"><?php echo $gc; ?>:</div>
            <div class=""><?php echo $rs['content']; ?></div>
            <?php echo $atts; ?>
            <div class="pa4-sign1 clear"><span class="pa4-signer1">单位:(项目法人)<?php echo $rs['comp']; ?><br/>日期:<?php echo $rs['date']; ?></span></div>
        </div>
    </div>
    <?php
    else:
        echo HTML::AlertInfo('文档尚未创建');
    endif;
    ?>
</div>
