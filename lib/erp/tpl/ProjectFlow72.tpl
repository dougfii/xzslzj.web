<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectFlow72Allow', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow72Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">地基验槽签证 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
            <div class="center">地基验槽签证</div>
            <div class="right">编号：<?php echo $rs['no']; ?></div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td class="v" colspan="2"><?php echo $rs['attachments']; ?></td><td class="k">施工单位</td><td class="v" colspan="2"><?php echo $rs['comp']; ?></td></tr>
                <tr><td class="k">验槽部位</td><td class="v" colspan="2"><?php echo $rs['signer']; ?></td><td class="k">验槽时间</td><td class="v" colspan="2"><?php echo $rs['date']; ?></td></tr>
                <tr>
                    <td colspan="6">
                        <p>1. 基槽底地质报告土质情况</p>
                        <p><?php echo $rs['c1']; ?></p>
                        <br/>
                        <p>2. 基槽底实际土质情况</p>
                        <p><?php echo $rs['c2']; ?></p>
                        <br/>
                        <p>3. 基槽高程与尺寸</p>
                        <p><?php echo $rs['c3']; ?></p>
                        <br/>
                        <p>4. 降排水情况</p>
                        <p><?php echo $rs['c4']; ?></p>
                        <br/>
                        <p>5. 附图及说明</p>
                        <p><?php echo $rs['c5']; ?></p>
                    </td>
                </tr>
                <tr><td class="k">施工单位<br/>初验意见</td><td colspan=5"><?php echo $rs['c6']; ?></td></tr>
                <tr><td class="k">验槽结论</td><td colspan=5"><?php echo $rs['c7']; ?></td></tr>
                <tr><td class="k" rowspan="6">联合小组成员</td><td class="k" colspan="3">单位名称</td><td class="k">职务、职称</td><td class="k">签字</td></tr>
                <tr><td class="k">项目法人</td><td colspan="2"><?php echo $rs['v1c']; ?></td><td><?php echo $rs['v1n']; ?></td><td><?php echo $rs['v1d']; ?></td></tr>
                <tr><td class="k">监理单位</td><td colspan="2"><?php echo $rs['v2c']; ?></td><td><?php echo $rs['v2n']; ?></td><td><?php echo $rs['v2d']; ?></td></tr>
                <tr><td class="k">勘测单位</td><td colspan="2"><?php echo $rs['v3c']; ?></td><td><?php echo $rs['v3n']; ?></td><td><?php echo $rs['v3d']; ?></td></tr>
                <tr><td class="k">设计单位</td><td colspan="2"><?php echo $rs['v4c']; ?></td><td><?php echo $rs['v4n']; ?></td><td><?php echo $rs['v4d']; ?></td></tr>
                <tr><td class="k">施工单位</td><td colspan="2"><?php echo $rs['v5c']; ?></td><td><?php echo $rs['v5n']; ?></td><td><?php echo $rs['v5d']; ?></td></tr>
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
