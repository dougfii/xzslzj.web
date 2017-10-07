<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectReply925', {fid:$('#fid').val(),proj:$('#proj').val(),signer:$('#signer').val(),signer_code:$('#signer_code').val(),owner:$('#owner').val(),owner_code:$('#owner_code').val(),approver:$('#approver').val(),approver_code:$('#approver_code').val(),comp:$('#comp').val(),date:$('#date').val()
            ,c1:$('#c1').val(),c2:$('#c2').val(),c3:$('#c3').val(),c4:$('#c4').val(),c5:$('#c5').val(),c6:$('#c6').val(),c7:$('#c7').val(),c8:$('#c8').val(),c9:$('#c9').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 9252;
            var no = $(this).attr('fid');
            var name = $(this).attr('fname');
            upload(pid, tid, no, name);
        });

        function upload(pid, tid, no, name){
            $.ajaxFileUpload({
                url:'?m=Upload&a=UpFlowFixed&no='+no,
                secureuri:false,
                fileElementId:'upfile'+no,
                dataType:'json',
                success: function(ret, status) {
                    if(ret.state=='SUCCESS') {
                        $.post('?m=Project&a=OnUpFlowFixed', {pid:pid,tid:tid,no:no,name:name,file:ret.name,url:ret.url,ext:ret.type,size:ret.size}, function (rt){
                            if(rt.code==1)layer.msg('上传完成', 1, function(){
                                $('#atta'+no).html('<a href="'+ret.url+'" target="_blank">'+name+'</a>');
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
    <div class="tool clear"><span class="cap">政府验收 - 竣工验收 - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="right"><input type="text" class="pae4-text1" id="proj" /> 工程竣工验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 监 督 报 告</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<input type="text" class="pae4-text1" id="signer" />（签名）</div>
            <br/>
            <div class="center">　　　　　<input type="text" class="pae4-text1" id="signer_code" placeholder="质量监督员证书号" />　　　　</div>
            <br/><br/>
            <div class="center">审　　定：<input type="text" class="pae4-text1" id="owner" />（签名）</div>
            <br/>
            <div class="center">　　　　　<input type="text" class="pae4-text1" id="owner_code" placeholder="质量监督员证书号" />　　　　</div>
            <br/><br/>
            <div class="center">批　　准：<input type="text" class="pae4-text1" id="approver" />（签名）</div>
            <br/>
            <div class="center">　　　　　<input type="text" class="pae4-text1" id="approver_code" placeholder="质量监督员证书号" />　　　　</div>
            <br/><br/><br/><br/>
            <div class="right center">工程质量监督机构名称（盖公章）<br/><input type="text" class="pae4-text1" id="comp" /></div>
            <br/><br/><br/><br/>
            <div class="right">日期：<input type="text" class="pae4-text1" id="date" onclick="laydate();" readonly /></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c1" placeholder="（工程批复过程、批复文号、概算投资，批准的主要工程内容，主要设计变更及履行手续情况，工程完成情况）"></textarea></div>
            <br/>
            <div class="">二、质量监督工作</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c2" placeholder="（质量监督的组织形式、工作方式、工作开展情况）"></textarea></div>
            <br/>
            <div class="">三、参建单位质量管理体系</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c3"></textarea></div>
            <br/>
            <div class="">四、工程项目划分确认</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c4"></textarea></div>
            <br/>
            <div class="">五、工程质量检测</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c5"></textarea></div>
            <br/>
            <div class="">六、工程质量事故和质量缺陷处理</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c6"></textarea></div>
            <br/>
            <div class="">七、工程质量核备与核定</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c7"></textarea></div>
            <br/>
            <div class="">八、工程质量结论意见</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c8"></textarea></div>
            <br/>
            <div class="">九、检验</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c9"></textarea></div>
            <br/>
        </div>
        <div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">同意</a></div>
        <br/>
    </div>
    <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>
