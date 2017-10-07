<script>
    $(function(){
        $('#allow').click(function(){
            var n3 = $('#n3').is(':checked') ? 1 : 0;
            var n4 = $('#n4').is(':checked') ? 1 : 0;
            var n5 = $('#n5').is(':checked') ? 1 : 0;
            var n6 = $('#n6').is(':checked') ? 1 : 0;
            var n7 = $('#n7').is(':checked') ? 1 : 0;
            var n8 = $('#n8').is(':checked') ? 1 : 0;
            var n9 = $('#n9').is(':checked') ? 1 : 0;
            $.post('?m=Project&a=OnProjectReply2', {n3:n3,n4:n4,n5:n5,n6:n6,n7:n7,n8:n8,n9:n9
            ,fid:$('#fid').val(),no:$('#no').val(),content:$('#content').val(),date:$('#date').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">单元工程质量项目划分 - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagedialog">
            <div class="pagedialog-caption">下一阶段</div>
            <div class="">
                <?php
            for($i=3; $i<=ProjectNodeCls::Count()-1; $i++)
            {
                echo '<input type="checkbox" id="n' . $i . '" checked> ' . ProjectNodeCls::Name($i) . '　　';
                }
                ?>
            </div>
        </div>
    </div>
    <br/>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center">文件编号：<input type="text" class="pae4-text1" id="no" /></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于对<?php echo $name; ?>项目划分确认的批复</div>
            <div class="pa4-company"><?php echo $company; ?>:</div>
            <div class=""><textarea rows="30" class="pa4-textarea1" id="content"></textarea></div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $gc; ?><br/>日期：<input type="text" class="pae4-text1" id="date" onclick="laydate();" readonly /></span></div>
            <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
        </div>
        <div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">同意</a></div>
        <br/>
    </div>
</div>
