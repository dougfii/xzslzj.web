<script>
    $(function(){
        $('#add').click(function(){
            $.post('?m=Project&a=OnProjectFlow73', {id:$('#id').val(),attachments:$('#attachments').val(),no:$('#no').val(),comp:$('#comp').val(),signer:$('#signer').val(),date:$('#date').val()
            ,c1:$('#c1').val(),c2:$('#c2').val(),c3:$('#c3').val(),c4:$('#c4').val(),c5:$('#c5').val()
            ,v1c:$('#v1c').val(),v1n:$('#v1n').val(),v1d:$('#v1d').val(),v2c:$('#v2c').val(),v2n:$('#v2n').val(),v3c:$('#v3c').val(),v3n:$('#v3n').val(),v3d:$('#v3d').val(),v4c:$('#v4c').val(),v4n:$('#v4n').val(),v4d:$('#v4d').val(),v5c:$('#v5c').val(),v5n:$('#v5n').val(),v5d:$('#v5d').val()
            }, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('.atts').on( 'click', '.upd', function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnUpFlowDelete', {id:id}, function(ret){if(ret.code==1)$('#atta'+id).remove();else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 73;
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
    <div class="tool clear"><span class="cap">质量缺陷备案 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if($edit) echo '<a class="tooladd" id="add" href="javascript:;">提交</a>'; ?></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">质量缺陷备案</div>
            <div class="right">编号：<?php if($edit) echo '<input type="text" class="w" id="no" value="' . $no . '" />'; else echo $no; ?></div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td><?php if($edit) echo '<input type="text" class="w" id="attachments" value="' . $attachments . '" />'; else echo $attachments; ?></td><td class="k">缺陷名称</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="comp" value="' . $comp . '" />'; else echo $comp; ?></td></tr>
                <tr><td class="k">缺陷部位</td><td><?php if($edit) echo '<input type="text" class="w" id="signer" value="' . $signer . '" />'; else echo $signer; ?></td><td class="k">备案日期</td><td colspan="2"><?php if($edit) echo '<input type="text" class="w" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></td></tr>
                <tr>
                    <td colspan="6">
                        <p>1. 质量缺陷产生的部位与特征（具体部位、缺陷描述并附示意图）</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c1">' . $c1 . '</textarea>'; else echo $c1; ?></p>
                        <br/>
                        <p>2. 质量缺陷产生的主要原因</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c2">' . $c2 . '</textarea>'; else echo $c2; ?></p>
                        <br/>
                        <p>3. 对工程的安全、功能和运用影响分析</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c3">' . $c3 . '</textarea>'; else echo $c3; ?></p>
                        <br/>
                        <p>4. 处理方案与或不处理原因</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c4">' . $c4 . '</textarea>'; else echo $c4; ?></p>
                        <br/>
                        <p>5. 保留意见（保留意见应说明主要理由，或采用其他方案及主要理由）</p>
                        <p><?php if($edit) echo '<textarea rows="3" class="w" id="c5">' . $c5 . '</textarea>'; else echo $c5; ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>施工单位名称：<?php if($edit) echo '<input type="text" class="" id="v1c" value="' . $v1c . '" />'; else echo $v1c; ?> （盖公章）</div>
                        <br/>
                        <div><span>质检部门负责人（签字）：<?php if($edit) echo '<input type="text" class="" id="v1n" value="' . $v1n . '" />'; else echo $v1n; ?></span><span class="right">技术负责人（签字）：<?php if($edit) echo '<input type="text" class="" id="v1d" value="' . $v1d . '" />'; else echo $v1d; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>设计单位名称：<?php if($edit) echo '<input type="text" class="" id="v2c" value="' . $v2c . '" />'; else echo $v2c; ?> （盖公章）</div>
                        <br/>
                        <div><span>设计代表（签字）：<?php if($edit) echo '<input type="text" class="" id="v2n" value="' . $v2n . '" />'; else echo $v2n; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>监理单位名称：<?php if($edit) echo '<input type="text" class="" id="v3c" value="' . $v3c . '" />'; else echo $v3c; ?> （盖公章）</div>
                        <br/>
                        <div><span>监理工程师（签字）：<?php if($edit) echo '<input type="text" class="" id="v3n" value="' . $v3n . '" />'; else echo $v3n; ?></span><span class="right">总监理工程师（签字）：<?php if($edit) echo '<input type="text" class="" id="v3d" value="' . $v3d . '" />'; else echo $v3d; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div>项目法人名称：<?php if($edit) echo '<input type="text" class="" id="v4c" value="' . $v4c . '" />'; else echo $v4c; ?> （盖公章）</div>
                        <br/>
                        <div><span>现场代表（签字）：<?php if($edit) echo '<input type="text" class="" id="v4n" value="' . $v4n . '" />'; else echo $v4n; ?></span><span class="right">技术负责人（签字）：<?php if($edit) echo '<input type="text" class="" id="v4d" value="' . $v4d . '" />'; else echo $v4d; ?></span></div>
                    </td>
                </tr>
            </table>
            <?php echo $atts; ?>
        </div>
    </div>
    <input type="hidden" id="id"/>
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>
