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
            <div>
                <p>签收单位：<?php echo $rs['writer']; ?>　　日期：<?php echo $rs['writer_date']; ?></p>
                <p>签收人：<?php echo $rs['signer']; ?>　　日期：<?php echo $rs['signer_date']; ?></p>
            </div>
            <br/><br/>
            <?php echo $atts; ?>
            <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
        </div>
    </div>
    <?php
    else:
        echo HTML::AlertInfo('文档尚未创建');
    endif;
    ?>
</div>
