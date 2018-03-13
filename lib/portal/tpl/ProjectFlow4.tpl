<script>
    $(function(){
        $('#sign').click(function(){
            $.post('?m=Project&a=OnProjectFlow4Sign', {id:$('#id').val(),pid:$('#pid').val(),writer:$('#writer').val(),signer:$('#signer').val(),writer_date:$('#writer_date').val()}, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){Refresh();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rs)):
    ?>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center">检查表编号：<?php echo $rs['no']; ?></div>
            <div class="pa4-redline"></div>
            <div class="center">关于<?php echo $rs['name']; ?>质量监督检查有关情况的通知</div>
            <br/>
            <div><?php echo nl2br($rs['content']); ?></div>
            <br/><br/>
            <div class="right">
                <p><?php echo $gc; ?></p>
                <br/>
                <p>日期：<?php echo $rs['date']; ?></p>
            </div>
            <br/><br/><br/><br/><br/><br/><br/><br/>
            <?php
            if(!empty($rs['writer']) && !empty($rs['signer']) && !empty($rs['writer_date']))
            {
                echo "<div><p>签收单位：{$rs['writer']}</p><p>签收人：{$rs['signer']}</p><p>日期：{$rs['writer_date']}</p></div><br/><br/>";
                }
                else
                {
                    echo '<div><p>签收单位：<input type="text" class="" id="writer" value="' . $company . '" /></p><p>签收人：<input type="text" class="" id="signer" /></p><p>日期：<input type="text" class="" id="writer_date" value="' . date("Y-m-d") . '" onclick="laydate();" readonly /></p><p><a class="tooladd" id="sign" href="javascript:;">确认签收</a></p></div><br/><br/>';
                }
            ?>
            <?php echo $atts; ?>
            <input type="hidden" id="id" value="<?php echo $id; ?>" />
            <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
        </div>
    </div>
    <?php
    else:
        echo HTML::AlertInfo('文档尚未创建');
    endif;
    ?>
</div>
