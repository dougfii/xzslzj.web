<script>
    $(function(){
        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow1Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督申报 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><a class="tooladd" href="javascript:" onclick="Printer('?m=Project&a=ProjectFlow1Print&id=<?php echo $rs['id']; ?>');">打印</a></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rs)):
    ?>
    <?php if($approve): ?>
    <div class="pagea4">
        <div class="pagedialog">
            <div class="pagedialog-caption">批复意见</div>
            <div class=""><textarea id="content" class="pagedialog-textarea" rows="2"></textarea></div>
            <div class="pagedialog-buttons"><a href="?m=Project&a=ProjectReply1&fid=<?php echo $rs['id'] ?>" class="btn">同意</a><a href="javascript:;" class="btn" id="deny">驳回</a></div>
        </div>
    </div>
    <br/>
    <?php endif; ?>
    <div class="pagea4">
        <div class="pagea4info">

            <div class="pa4-caption1"><?php echo $company; ?>文件</div>
            <div class="clear"><span><?php echo $rs['no']; ?></span><span class="right">签发:<?php echo $rs['signer']; ?></span></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于申请<?php echo $name; ?>质量监督的请示</div>
            <div class="pa4-company"><?php echo COMPANY_NAME; ?>:</div>
            <div class="pa4-content"><?php echo $rs['content']; ?></div>
            <?php echo $atts; ?>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $company; ?><br/><?php echo $rs['date']; ?></span></div>
            <div class="pa4-attachment1">关键词:</div>
            <div class=""><?php echo $rs['keywords']; ?></div>
        </div>
        <input type="hidden" id="pid" value="<?php echo $rs['pid']; ?>" />
        <input type="hidden" id="fid" value="<?php echo $rs['id']; ?>" />
    </div>
    <?php
    else:
        echo HTML::AlertInfo('文档尚未创建');
    endif;
    ?>
</div>
