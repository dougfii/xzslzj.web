<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectFlow4', {pid:$('#pid').val()
            ,name:$('#name').val(),no:$('#no').val(),code:$('#code').val(),date:$('#date').val(),content:$('#content').val(),personals:$('#personals').val(),progress:$('#progress').val(),writer:$('#writer').val(),writer_date:$('#writer_date').val(),signer:$('#signer').val(),signer_date:$('#signer_date').val()
            }, function (ret){if(ret.code==1)layer.msg('发送完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('.atts').on( 'click', '.upd', function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnUpFlowDelete', {id:id}, function(ret){if(ret.code==1)$('#atta'+id).remove();else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 3;
            upload(pid, tid);
        });

        function upload(pid, tid){
            $.ajaxFileUpload({
                url:'?m=Upload&a=UpFlowDynamic',
                secureuri:false,
                fileElementId:'upfile',
                dataType:'json',
                success: function(ret, status) {
                    if(ret.state=='SUCCESS') {
                        $.post('?m=Project&a=OnUpFlowDynamic', {pid:pid,tid:tid,no:0,name:ret.originalName,file:ret.name,url:ret.url,ext:ret.type,size:ret.size}, function (rt){
                            if(rt.code==1&&rt.msg>0)layer.msg('上传完成', 1, function(){
                                $('.atts ul').append('<li id="atta'+rt.msg+'"><a href="'+ret.url+'" target="_blank">'+ret.originalName+'</a>　　<a href="javascript:;" class="upd" did="'+rt.msg+'">删除</a></li>');
                                $('#attb').html('<span class="up">添加上传<input type="file" id="upfile" class="upfile" name="upfile" /></span>');
                            });else layer.msg(rt.msg, 1);
                        }, 'json');
                    }
                    else layer.msg(ret.state, 2, -1);
                },
                error:function(ret, status, e){
                    layer.msg('上传失败 ' + e, 2, -1);
                }
            });
        };
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">外观质量检查与评定项目确认 - [发送意见]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">质量监督检查意见表</div>
            <table class="tx1">
                <tr><td class="k">工程名称</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="name" value="' . $name . '" />'; else echo $name; ?></td><td class="k">检查表编号</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="no" value="' . $no . '" />'; else echo $no; ?></td></tr>
                <tr><td class="" colspan="2">质量监督人员和证号：<?php if($edit) echo '<input type="text" class="" id="code" value="' . $code . '" />'; else echo $code; ?></td><td class="k">检查时间</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></td></tr>
                <tr><td class="" colspan="4">参建单位人员：<?php if($edit) echo '<input type="text" class="" id="personals" value="' . $personals . '" />'; else echo $personals; ?></td></tr>
                <tr><td class="" colspan="4">工程形象进度：<?php if($edit) echo '<input type="text" class="" id="progress" value="' . $progress . '" />'; else echo $progress; ?></td></tr>
                <tr><td class="" colspan="4"><p>监督检查意见：</p><p><?php if($edit) echo '<textarea rows="10" class="w" id="content">' . $content . '</textarea>'; else echo $content; ?></p></td></tr>
                <tr><td class="" colspan="4"><p>被检查单位签收人：<?php if($edit) echo '<input type="text" class="" id="writer" value="' . $writer . '" />'; else echo $writer; ?>　　日期：<?php if($edit) echo '<input type="text" class="" id="writer_date" value="' . $writer_date . '" onclick="laydate();" readonly />'; else echo $writer_date; ?></p><p>填写人：<?php if($edit) echo '<input type="text" class="" id="signer" value="' . $signer . '" />'; else echo $signer; ?>　　日期：<?php if($edit) echo '<input type="text" class="" id="signer_date" value="' . $signer_date . '" onclick="laydate();" readonly />'; else echo $signer_date; ?></p></td>
            </table>
            <?php echo $atts; ?>
        </div>
        <?php if($edit) echo '<div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">发送</a></div>'; ?>
        <br/>
    </div>
    <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>