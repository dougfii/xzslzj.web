<div class="toolbar">
    <div class="tool clear"><span class="cap">政府验收 - 阶段验收（水下工程） - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
<?php
if(!empty($rs)):
?>
        <div class="pagea4info">
            <div class="right"><span class="pa4-underline">　<?php echo $rs['proj']; ?>　</span> 工程 <span class="pa4-underline">　<?php echo $rs['stage']; ?>　</span> 阶段（竣工）验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 评 价 意 见</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<span class="pa4-underline">　　<?php echo $rs['signer']; ?>　　</span></div>
            <br/>
            <div class="center">　　　　　<span class="pa4-underline">　　<?php echo $rs['signer_code']; ?>　　</span></div>
            <br/><br/>
            <div class="center">审　　定：<span class="pa4-underline">　　<?php echo $rs['owner']; ?>　　</span></div>
            <br/>
            <div class="center">　　　　　<span class="pa4-underline">　　<?php echo $rs['owner_code']; ?>　　</span></div>
            <br/><br/><br/><br/>
            <div class="right center">工程质量监督机构名称（盖公章）<br/><?php echo $rs['comp']; ?></div>
            <br/><br/><br/><br/>
            <div class="right"><?php echo $rs['date']; ?></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><?php echo $rs['c1']; ?></div>
            <br/>
            <div class="">二、质量监督情况</div>
            <div class=""><?php echo $rs['c2']; ?></div>
            <br/>
            <div class="">三、项目划分情况</div>
            <div class=""><?php echo $rs['c3']; ?></div>
            <br/>
            <div class="">四、质量数据分析</div>
            <div class=""><?php echo $rs['c4']; ?></div>
            <br/>
            <div class="">五、质量事故和缺陷处理情况</div>
            <div class=""><?php echo $rs['c5']; ?></div>
            <br/>
            <div class="">六、遗留问题的说明</div>
            <div class=""><?php echo $rs['c6']; ?></div>
            <br/>
            <div class="">七、质量评价意见</div>
            <div class=""><?php echo $rs['c7']; ?></div>
            <br/>
            <div class="">八、建议</div>
            <div class=""><?php echo $rs['c8']; ?></div>
            <br/>
        </div>
<?php
else:
    echo HTML::AlertError();
endif;
?>
    </div>
</div>
