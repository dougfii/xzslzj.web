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
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center">检查表编号：<?php if($edit) echo '<input type="text" class="" id="no" value="' . $no . '" />'; else echo $no; ?></div>
            <div class="pa4-redline"></div>
            <div class="center">关于<?php if($edit) echo '<input type="text" class="" id="name" value="' . $name . '" />'; else echo $name; ?>质量监督检查有关情况的通知</div>
            <br/>
            <div>
                <?php
              if($edit){
                echo '<textarea rows="50" class="" style="width:100%;padding:5px;" id="content">';
                   if(empty($content)){
                        echo $company . "：\n";
                        echo "　　_______年___月___日，我站质量监督员（____、____、____）对" . $name . "施工过程进程第____次巡查监督，主要对_______施工现场、监理及施工单位资料进行检查。参建单位主要人员为_______________________________________________。现将检查情况分述如下：\n\n";
                    }
                    else{
                        echo $content;
                    }
                   echo '</textarea>';
                }
                else{
                    echo nl2br($content);
                }
                ?>
            </div>
            <br/><br/>
            <div class="right">
                <p><?php echo $gc; ?></p>
                <br/>
                <p>日期：<?php if($edit) echo '<input type="text" class="" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></p>
            </div>
            <br/><br/><br/><br/><br/><br/><br/><br/>
            <div>
                <p>签收单位：<?php if($edit) echo '<input type="text" class="" id="writer" value="' . $writer . '" />'; else echo $writer; ?>　　日期：<?php if($edit) echo '<input type="text" class="" id="writer_date" value="' . $writer_date . '" onclick="laydate();" readonly />'; else echo $writer_date; ?></p>
                <p>签收人：<?php if($edit) echo '<input type="text" class="" id="signer" value="' . $signer . '" />'; else echo $signer; ?>　　日期：<?php if($edit) echo '<input type="text" class="" id="signer_date" value="' . $signer_date . '" onclick="laydate();" readonly />'; else echo $signer_date; ?></p>
            </div>
            <br/><br/>
            <?php echo $atts; ?>
            <div>
                <input type="hidden" id="code"/>
                <input type="hidden" id="personals"/>
                <input type="hidden" id="progress"/>
            </div>
        </div>
        <?php if($edit) echo '<div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">发送</a></div>'; ?>
        <br/>
    </div>
    <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>