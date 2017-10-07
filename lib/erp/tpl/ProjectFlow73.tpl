<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectFlow73Allow', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow73Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">质量缺陷备案 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
            <div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">同意</a><a href="javascript:;" class="btn" id="deny">驳回</a></div>
        </div>
    </div>
    <br/>
    <?php endif; ?>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">质量缺陷备案</div>
            <div class="right">编号：<?php echo $rs['no']; ?></div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td class="v"><?php echo $rs['attachments']; ?></td><td class="k">缺陷名称</td><td class="v"><?php echo $rs['comp']; ?></td></tr>
                <tr><td class="k">缺陷部位</td><td class="v"><?php echo $rs['signer']; ?></td><td class="k">备案日期</td><td class="v"><?php echo $rs['date']; ?></td></tr>
                <tr>
                    <td colspan="6">
                        <p>1. 质量缺陷产生的部位与特征（具体部位、缺陷描述并附示意图）</p>
                        <p><?php echo $rs['c1']; ?></p>
                        <br/>
                        <p>2. 质量缺陷产生的主要原因</p>
                        <p><?php echo $rs['c2']; ?></p>
                        <br/>
                        <p>3. 对工程的安全、功能和运用影响分析</p>
                        <p><?php echo $rs['c3']; ?></p>
                        <br/>
                        <p>4. 处理方案与或不处理原因</p>
                        <p><?php echo $rs['c4']; ?></p>
                        <br/>
                        <p>5. 保留意见（保留意见应说明主要理由，或采用其他方案及主要理由）</p>
                        <p><?php echo $rs['c5']; ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>施工单位名称：<?php echo $rs['v1c']; ?> （盖公章）</div>
                        <br/>
                        <div><span>质检部门负责人（签字）：<?php echo $rs['v1n']; ?></span><span class="right">技术负责人（签字）：<?php echo $rs['v1d']; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>设计单位名称：<?php echo $rs['v2c']; ?> （盖公章）</div>
                        <br/>
                        <div><span>设计代表（签字）：<?php echo $rs['v2n']; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>监理单位名称：<?php echo $rs['v3c']; ?> （盖公章）</div>
                        <br/>
                        <div><span>监理工程师（签字）：<?php echo $rs['v3n']; ?></span><span class="right">总监理工程师（签字）：<?php echo $rs['v3d']; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>项目法人名称：<?php echo $rs['v4c']; ?> （盖公章）</div>
                        <br/>
                        <div><span>现场代表（签字）：<?php echo $rs['v4n']; ?></span><span class="right">技术负责人（签字）：<?php echo $rs['v4d']; ?></span></div>
                    </td>
                </tr>
            </table>
            <?php echo $atts; ?>
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
