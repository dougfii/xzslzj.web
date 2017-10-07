<div class="toolbar">
    <div class="tool clear"><span class="cap">政府验收 - 阶段验收（水下工程） - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
<?php
if(!empty($rs)):
?>
        <div class="pagea4info">
            <div class="right"><span class="pa4-underline">　<?php echo $rs['proj']; ?>　</span> 工程竣工验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 监 督 报 告</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<span class="pa4-underline">　　<?php echo $rs['signer']; ?>　　</span></div>
            <br/>
            <div class="center">　　　　　<span class="pa4-underline">　　<?php echo $rs['signer_code']; ?>　　</span></div>
            <br/><br/>
            <div class="center">审　　定：<span class="pa4-underline">　　<?php echo $rs['owner']; ?>　　</span></div>
            <br/>
            <div class="center">　　　　　<span class="pa4-underline">　　<?php echo $rs['owner_code']; ?>　　</span></div>
            <br/>
            <div class="center">批　　准：<span class="pa4-underline">　　<?php echo $rs['approver']; ?>　　</span></div>
            <br/>
            <div class="center">　　　　　<span class="pa4-underline">　　<?php echo $rs['approver_code']; ?>　　</span></div>
            <br/><br/><br/><br/>
            <div class="right center">工程质量监督机构名称（盖公章）<br/><?php echo $rs['comp']; ?></div>
            <br/><br/><br/><br/>
            <div class="right"><?php echo $rs['date']; ?></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><?php echo $rs['c1']; ?></div>
            <br/>
            <div class="">二、质量监督工作</div>
            <div class=""><?php echo $rs['c2']; ?></div>
            <br/>
            <div class="">三、参建单位质量管理体系</div>
            <div class=""><?php echo $rs['c3']; ?></div>
            <br/>
            <div class="">四、工程项目划分确认</div>
            <div class=""><?php echo $rs['c4']; ?></div>
            <br/>
            <div class="">五、工程质量检测</div>
            <div class=""><?php echo $rs['c5']; ?></div>
            <br/>
            <div class="">六、工程质量事故和质量缺陷处理</div>
            <div class=""><?php echo $rs['c6']; ?></div>
            <br/>
            <div class="">七、工程质量核备与核定</div>
            <div class=""><?php echo $rs['c7']; ?></div>
            <br/>
            <div class="">八、工程质量结论意见</div>
            <div class=""><?php echo $rs['c8']; ?></div>
            <br/>
            <div class="">九、检验</div>
            <div class=""><?php echo $rs['c9']; ?></div>
            <br/>
        </div>
<?php
else:
    echo HTML::AlertError();
endif;
?>
    </div>
</div>
