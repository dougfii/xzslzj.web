<script>
    $(function(){
        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow91Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">法人验收 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
            <div class="pagedialog-buttons"><a href="?m=Project&a=ProjectReply91&fid=<?php echo $rs['id'] ?>" class="btn">同意</a><a href="javascript:;" class="btn" id="deny">驳回</a></div>
        </div>
    </div>
    <br/>
    <?php endif; ?>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">法人验收</div>
            <table class="tx1">
                <thead>
                <tr><td class="k">序号</td><td class="k">验收内容</td><td class="k">时间</td><td class="k">备注</td></tr>
                </thead>
                <tbody id="items">
                <?php
             if(!empty($items)){
                foreach($items as $k=>$v){
                        echo '<tr class="delitem"><td class="c item0">' . ($k+1) . '</td><td>';
                        echo $edit ? '<input type="text" class="w item1" value="' . $v[0] . '"/>' : $v[0];
                        echo '</td><td class="c">';
                        echo $edit ? '<input type="text" size="3" class="w item2" value="' . $v[1] . '"/>' : $v[1];
                        echo '</td><td class="c">';
                        echo $edit ? '<input type="text" size="3" class="w item3" value="' . $v[2] . '"/>' : $v[2];
                        echo '</td></tr>';
                    }
                }
                ?>
                </tbody>
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
