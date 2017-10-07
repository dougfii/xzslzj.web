<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rs)):
    ?>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">质量监督检查意见表</div>
            <table class="tx1">
                <tr><td class="k">工程名称</td><td class="v"><?php echo $rs['name']; ?></td><td class="k">检查表编号</td><td class="v"><?php echo $rs['no']; ?></td></tr>
                <tr><td class="" colspan="2">质量监督人员和证号：<?php echo $rs['code']; ?></td><td class="k">检查时间</td><td class="v"><?php echo $rs['date']; ?></td></tr>
                <tr><td class="" colspan="4">参建单位人员：<?php echo $rs['personals']; ?></td></tr>
                <tr><td class="" colspan="4">工程形象进度：<?php echo $rs['progress']; ?></td></tr>
                <tr><td class="" colspan="4"><p>监督检查意见：</p><p><?php echo $rs['content']; ?></td></tr>
                <tr><td class="" colspan="4"><p>被检查单位签收人：<?php echo $rs['writer']; ?>　　日期：<?php echo $rs['writer_date']; ?></p><p>填写人：<?php echo $rs['signer']; ?>　　日期：<?php echo $rs['signer_date']; ?></p></td>
            </table>
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
