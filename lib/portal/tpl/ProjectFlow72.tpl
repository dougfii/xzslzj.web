<script>
    $(function(){
        $('#add').click(function(){
            $.post('?m=Project&a=OnProjectFlow72', {id:$('#id').val(),attachments:$('#attachments').val(),no:$('#no').val(),comp:$('#comp').val(),signer:$('#signer').val(),date:$('#date').val()
            ,c1:$('#c1').val(),c2:$('#c2').val(),c3:$('#c3').val(),c4:$('#c4').val(),c5:$('#c5').val(),c6:$('#c6').val(),c7:$('#c7').val()
            ,v1c:$('#v1c').val(),v1n:$('#v1n').val(),v1d:$('#v1d').val(),v2c:$('#v2c').val(),v2n:$('#v2n').val(),v2d:$('#v2d').val(),v3c:$('#v3c').val(),v3n:$('#v3n').val(),v3d:$('#v3d').val(),v4c:$('#v4c').val(),v4n:$('#v4n').val(),v4d:$('#v4d').val(),v5c:$('#v5c').val(),v5n:$('#v5n').val(),v5d:$('#v5d').val()
            }, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('.atts').on( 'click', '.upd', function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnUpFlowDelete', {id:id}, function(ret){if(ret.code==1)$('#atta'+id).remove();else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 72;
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
    <div class="tool clear"><span class="cap">地基验槽签证 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if($edit) echo '<a class="tooladd" id="add" href="javascript:;">提交</a>'; ?></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">地基验槽签证</div>
            <div class="right">编号：<?php if($edit) echo '<input type="text" class="w" id="no" value="' . $no . '" />'; else echo $no; ?></div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="attachments" value="' . $attachments . '" />'; else echo $attachments; ?></td><td class="k">施工单位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="comp" value="' . $comp . '" />'; else echo $comp; ?></td></tr>
                <tr><td class="k">验槽部位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="signer" value="' . $signer . '" />'; else echo $signer; ?></td><td class="k">验槽时间</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></td></tr>
                <tr>
                    <td colspan="6">
                        <p>1. 基槽底地质报告土质情况</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c1">' . $c1 . '</textarea>'; else echo $c1; ?></p>
                        <br/>
                        <p>2. 基槽底实际土质情况</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c2">' . $c2 . '</textarea>'; else echo $c2; ?></p>
                        <br/>
                        <p>3. 基槽高程与尺寸</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c3">' . $c3 . '</textarea>'; else echo $c3; ?></p>
                        <br/>
                        <p>4. 降排水情况</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c4">' . $c4 . '</textarea>'; else echo $c4; ?></p>
                        <br/>
                        <p>5. 附图及说明</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c5">' . $c5 . '</textarea>'; else echo $c5; ?></p>
                    </td>
                </tr>
                <tr><td class="k">施工单位<br/>初验意见</td><td colspan=5"><?php if($edit) echo '<textarea rows="3" class="w" id="c6">' . $c6 . '</textarea>'; else echo $c6; ?></td></tr>
                <tr><td class="k">验槽结论</td><td colspan=5"><?php if($edit) echo '<textarea rows="3" class="w" id="c7">' . $c7 . '</textarea>'; else echo $c7; ?></td></tr>
                <tr><td class="k" rowspan="6">联合小组成员</td><td class="k" colspan="3">单位名称</td><td class="k">职务、职称</td><td class="k">签字</td></tr>
                <tr><td class="k">项目法人</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="v1c" value="' . $v1c . '" />'; else echo $v1c; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v1n" value="' . $v1n . '" />'; else echo $v1n; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v1d" value="' . $v1d . '" />'; else echo $v1d; ?></td></tr>
                <tr><td class="k">监理单位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="v2c" value="' . $v2c . '" />'; else echo $v2c; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v2n" value="' . $v2n . '" />'; else echo $v2n; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v2d" value="' . $v2d . '" />'; else echo $v2d; ?></td></tr>
                <tr><td class="k">勘测单位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="v3c" value="' . $v3c . '" />'; else echo $v3c; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v3n" value="' . $v3n . '" />'; else echo $v3n; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v3d" value="' . $v3d . '" />'; else echo $v3d; ?></td></tr>
                <tr><td class="k">设计单位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="v4c" value="' . $v4c . '" />'; else echo $v4c; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v4n" value="' . $v4n . '" />'; else echo $v4n; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v4d" value="' . $v4d . '" />'; else echo $v4d; ?></td></tr>
                <tr><td class="k">施工单位</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="v5c" value="' . $v5c . '" />'; else echo $v5c; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v5n" value="' . $v5n . '" />'; else echo $v5n; ?></td><td><?php if($edit) echo '<input type="text" class="w" id="v5d" value="' . $v5d . '" />'; else echo $v5d; ?></td></tr>
            </table>
            <?php echo $atts; ?>
        </div>
    </div>
    <input type="hidden" id="id"/>
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>
