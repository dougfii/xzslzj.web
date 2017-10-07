<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectFlow64Allow', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow64Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">工程项目质量等级核定表 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
            <div class="center">工程项目质量等级核定表</div>
            <table class="tx1">
                <tr><td class="k">工程项目名称</td><td class="v"><?php echo $rs['name']; ?></td><td class="k">项目法人</td><td class="v"><?php echo $rs['signer']; ?></td></tr>
                <tr><td class="k">工程等级</td><td class="v"><?php echo $rs['no']; ?></td><td class="k">核定时间</td><td class="v"><?php echo $rs['date']; ?></td></tr>
            </table>
            <table class="tx1">
                <thead>
                    <tr><td rowspan="2">序号</td><td rowspan="2">单位工程名称与编号</td><td colspan="2">核定等级</td></tr>
                    <tr><td>优良</td><td>合格</td></tr>
                </thead>
                <tbody id="items">
                    <?php
                 if(!empty($items)){
                    foreach($items as $k=>$v){
                            echo '<tr class="delitem"><td class="c item0">' . ($k+1) . '</td><td>';
                            echo $edit ? '<input type="text" class="w item1" vlaue="' . $v[0] . '"/>' : $v[0];
                            echo '</td><td class="c">';
                            echo $edit ? '<input type="text" size="3" class="c item2" vlaue="' . $v[1] . '"/>' : $v[1];
                            echo '</td><td class="c">';
                            echo $edit ? '<input type="text" size="3" class="c item3" vlaue="' . $v[2] . '"/>' : $v[2];
                            echo '</td></tr>';
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="4">分部工程共 <?php echo $total0; ?> 个，其中优良 <?php echo $total1; ?> 个，优良率 <?php echo $total2; ?> %，主要单位工程优良率 <?php echo $total3; ?> %，工程项目质量等级 <?php echo $total4; ?> 。</td></tr>
                </tfoot>
            </table>
            <table class="tx1">
                <tr><td colspan="2"><p>监理单位意见：</p><p><?php echo $rs['v1c']; ?></p><p>总监理工程师（签字）：<?php echo $rs['v1n']; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php echo $rs['v1d']; ?></p></td><td colspan="2"><p>项目法人意见：</p><p><?php echo $rs['v2c']; ?></p><p>技术负责人（签字）：<?php echo $rs['v2n']; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php echo $rs['v2d']; ?></p></td></tr>
                <tr><td colspan="4"><p>质量监督机构核备意见：</p><p><?php echo $rs['v3c']; ?></p><p>核备人（签字）：<?php echo $rs['v3n']; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php echo $rs['v3d']; ?></p></td></tr>
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
