<script>
    $(function(){
        $('#add').click(function(){
            $.post('?m=Project&a=OnProjectFlow924', {tno:$('#tno').val(),tcontent:$('#tcontent').val(),tdate:$('#tdate').val()
            ,id:$('#id').val(),proj:$('#proj').val(),stage:$('#stage').val(),signer:$('#signer').val(),owner:$('#owner').val(),comp:$('#comp').val(),date:$('#date').val()
            ,c1:$('#c1').val(),c2:$('#c2').val(),c3:$('#c3').val(),c4:$('#c4').val(),c5:$('#c5').val(),c6:$('#c6').val(),c7:$('#c7').val()}, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 924;
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
    <div class="tool clear"><span class="cap">政府验收 - 竣工预验收 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if($edit) echo '<a class="tooladd" id="add" href="javascript:;">提交</a>'; ?></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $company; ?>文件</div>
            <div class="clear center"><span><?php if($edit) echo '文件编号:<input type="text" class="pae4-text1" id="tno" value="' . $tno . '" />'; else echo $tno; ?></span></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于申请<?php echo $name; ?>质量等级核定的报告</div>
            <div class="pa4-company"><?php echo $gc; ?>:</div>
            <div class=""><?php if($edit) echo '<textarea rows="30" class="pa4-textarea1" id="tcontent">' . $tcontent . '</textarea>'; else echo $tcontent; ?></div>
            <div class="att">附件: 质量核定申报书</div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $company; ?><br/><?php if($edit) echo '日期:<input type="text" class="pae4-text1" id="tdate" value="' . $tdate . '" onclick="laydate();" readonly />'; else echo $tdate; ?></span></div>
        </div>
    </div>
    <br/>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="c"><?php if($edit) echo '<input type="text" class="pae4-text1" id="proj" value="' . $proj . '" />'; else echo $proj; ?> 工程 <?php if($edit) echo '<input type="text" class="pae4-text1" id="stage" value="' . $stage . '" />'; else echo $stage; ?> 阶段竣工预验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 核 定 申 报 书</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<?php if($edit) echo '<input type="text" class="pae4-text1" id="signer" value="' . $signer . '" />'; else echo $signer; ?>（签名）</div>
            <br/><br/>
            <div class="center">项目法人：<?php if($edit) echo '<input type="text" class="pae4-text1" id="owner" value="' . $owner . '" />'; else echo $owner; ?>（签名）</div>
            <br/><br/><br/><br/>
            <div class="right center">工程建设项目法人名称（盖公章）<br/><?php if($edit) echo '<input type="text" class="pae4-text1" id="comp" value="' . $comp . '" />'; else echo $comp; ?></div>
            <br/><br/><br/><br/>
            <div class="right">日期：<?php if($edit) echo '<input type="text" class="pae4-text1" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c1" placeholder="（工程批复过程、批复文号、概算投资，批准的主要工程内容，验收范围及其完成情况）">' . $c1 . '</textarea>'; else echo $c1; ?></div>
            <br/>
            <div class="">二、主要设计变更</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c2" placeholder="（主要设计变更及履行手续情况）">' . $c2 . '</textarea>'; else echo $c2; ?></div>
            <br/>
            <div class="">三、质量管理工作</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c3" placeholder="（项目法人、监理单位、施工单位质量体系建立和落实情况）">' . $c3 . '</textarea>'; else echo $c3; ?></div>
            <br/>
            <div class="">四、质量检测情况</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c4" placeholder="（施工单位自检，监理单位见证取样送检、平行检测和跟踪检测，项目法人委托检测情况和结果）">' . $c4 . '</textarea>'; else echo $c4; ?></div>
            <br/>
            <div class="">五、存在问题及处理情况</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c5" placeholder="（质量缺陷备案和质量事故处理情况）">' . $c5 . '</textarea>'; else echo $c5; ?></div>
            <br/>
            <div class="">六、遗留问题和尾工处理</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c6">' . $c6 . '</textarea>'; else echo $c6; ?></div>
            <br/>
            <div class="">七、工程质量的自评、复核、认定结果</div>
            <div class=""><?php if($edit) echo '<textarea rows="5" class="pa4-textarea1" id="c7">' . $c7 . '</textarea>'; else echo $c7; ?></div>
            <br/>
            <?php echo $atts; ?>
        </div>
    </div>
    <input type="hidden" id="id"/>
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>
