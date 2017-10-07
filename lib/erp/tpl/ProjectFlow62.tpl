<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectFlow62Allow', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow62Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        var cols = <?php echo $cols; ?>;
        var arr = new Array();
        for(var i=0;i<cols;i++) arr.push(i);
        $('#facade').mergeCell({cols: arr});
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">单位工程外观质量结论 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
            <div class="center">单位工程外观质量结论</div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td class="v"><?php echo $rs['attachments']; ?></td><td class="k">施工单位</td><td class="v"><?php echo $rs['comp']; ?></td></tr>
                <tr><td class="k">单位工程编号</td><td class="v"><?php echo $rs['no']; ?></td><td class="k">评定日期</td><td class="v"><?php echo $rs['date_ping']; ?></td></tr>
            </table>
            <div id="facadebox"><?php echo $tables; ?></div>
            <table class="tx1">
                <tr><td rowspan="7">评定组成员</td><td colspan="2">单位名称</td><td>职称</td><td>签名</td></tr>
                <tr><td>项目法人</td><td class="v"><?php echo $rs['m11']; ?></td><td><?php echo $rs['m12']; ?></td><td><?php echo $rs['m13']; ?></td></tr>
                <tr><td>监理单位</td><td class="v"><?php echo $rs['m21']; ?></td><td><?php echo $rs['m22']; ?></td><td><?php echo $rs['m23']; ?></td></tr>
                <tr><td>设计单位</td><td class="v"><?php echo $rs['m31']; ?></td><td><?php echo $rs['m32']; ?></td><td><?php echo $rs['m33']; ?></td></tr>
                <tr><td>施工单位</td><td class="v"><?php echo $rs['m41']; ?></td><td><?php echo $rs['m42']; ?></td><td><?php echo $rs['m43']; ?></td></tr>
                <tr><td>检测单位</td><td class="v"><?php echo $rs['m51']; ?></td><td><?php echo $rs['m52']; ?></td><td><?php echo $rs['m53']; ?></td></tr>
                <tr><td>运行管理单位</td><td class="v"><?php echo $rs['m61']; ?></td><td><?php echo $rs['m62']; ?></td><td><?php echo $rs['m63']; ?></td></tr>
                <tr><td>质量监督机构</td><td colspan="4"><p>核定意见：<?php echo $rs['content']; ?></p><p class="right center">核定人（签字）：<?php echo $rs['signer']; ?><br/>（盖公章）<br/>日期：<?php echo $rs['date']; ?></p></tr>
            </table>
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
