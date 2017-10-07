<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectReply3', {fid:$('#fid').val(),no:$('#no').val(),content:$('#content').val(),date:$('#date').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">外观质量检查与评定项目确认 - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center">文件编号：<input type="text" class="pae4-text1" id="no" /></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于对<?php echo $name; ?>外观质量检查与评定项目确认的批复</div>
            <div class="pa4-company"><?php echo $company; ?>:</div>
            <div class=""><textarea rows="30" class="pa4-textarea1" id="content"></textarea></div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $gc; ?><br/>日期：<input type="text" class="pae4-text1" id="date" onclick="laydate();" readonly /></span></div>
            <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
        </div>
        <div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">同意</a></div>
        <br/>
    </div>
    <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>