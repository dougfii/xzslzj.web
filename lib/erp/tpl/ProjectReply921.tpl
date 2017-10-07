<script>
    $(function(){
        $('#allow').click(function(){
            $.post('?m=Project&a=OnProjectReply921', {fid:$('#fid').val(),proj:$('#proj').val(),stage:$('#stage').val(),signer:$('#signer').val(),signer_code:$('#signer_code').val(),owner:$('#owner').val(),owner_code:$('#owner_code').val(),comp:$('#comp').val(),date:$('#date').val()
            ,c1:$('#c1').val(),c2:$('#c2').val(),c3:$('#c3').val(),c4:$('#c4').val(),c5:$('#c5').val(),c6:$('#c6').val(),c7:$('#c7').val(),c8:$('#c8').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">政府验收 - 阶段验收（水下工程） - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="right"><input type="text" class="pae4-text1" id="proj" /> 工程 <input type="text" class="pae4-text1" id="stage" /> 阶段（竣工）验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 评 价 意 见</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<input type="text" class="pae4-text1" id="signer" />（签名）</div>
            <br/>
            <div class="center">　　　　　<input type="text" class="pae4-text1" id="signer_code" placeholder="质量监督员证书号" />　　　　</div>
            <br/><br/>
            <div class="center">审　　定：<input type="text" class="pae4-text1" id="owner" />（签名）</div>
            <br/>
            <div class="center">　　　　　<input type="text" class="pae4-text1" id="owner_code" placeholder="质量监督员证书号" />　　　　</div>
            <br/><br/><br/><br/>
            <div class="right center">工程质量监督机构名称（盖公章）<br/><input type="text" class="pae4-text1" id="comp" /></div>
            <br/><br/><br/><br/>
            <div class="right">日期：<input type="text" class="pae4-text1" id="date" onclick="laydate();" readonly /></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c1" placeholder="（工程批复过程、批复文号、概算投资，批准的主要工程内容，验收范围及其完成情况）"></textarea></div>
            <br/>
            <div class="">二、质量监督情况</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c2" placeholder="（质量监督的组织形式、工作方式、工作开展情况）"></textarea></div>
            <br/>
            <div class="">三、项目划分情况</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c3" placeholder="（总体项目划分和具体项目划分及验收内容项目划分）"></textarea></div>
            <br/>
            <div class="">四、质量数据分析</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c4" placeholder="（扼要说明施工单位自检，监理单位见证取样送检、平行检测和跟踪检测情况，详述项目法人委托检测单位检测情况，质量监督检测情况）"></textarea></div>
            <br/>
            <div class="">五、质量事故和缺陷处理情况</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c5"></textarea></div>
            <br/>
            <div class="">六、遗留问题的说明</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c6"></textarea></div>
            <br/>
            <div class="">七、质量评价意见</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c7"></textarea></div>
            <br/>
            <div class="">八、建议</div>
            <div class=""><textarea rows="5" class="pa4-textarea1" id="c8"></textarea></div>
            <br/>
        </div>
        <div class="pagedialog-buttons"><a href="javascript:;" class="btn" id="allow">同意</a></div>
        <br/>
    </div>
    <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
</div>
