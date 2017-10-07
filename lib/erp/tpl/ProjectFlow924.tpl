<script>
    $(function(){
        $('#deny').click(function(){
            $.post('?m=Project&a=OnProjectFlow924Deny', {pid:$('#pid').val(),fid:$('#fid').val(),content:$('#content').val()}, function (ret){if(ret.code==1)layer.msg('批复完成', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">政府验收 - 竣工预验收 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rs)):
    ?>
    <?php if($approve): ?>
    <div class="pagea4">
        <div class="pagedialog">
            <div class="pagedialog-caption">批复意见</div>
            <div class=""><textarea id="content" class="pagedialog-textarea" rows="2"></textarea></div>
            <div class="pagedialog-buttons"><a href="?m=Project&a=ProjectReply924&fid=<?php echo $rs['id'] ?>" class="btn">同意</a><a href="javascript:;" class="btn" id="deny">驳回</a></div>
        </div>
    </div>
    <br/>
    <?php endif; ?>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $company; ?>文件</div>
            <div class="clear center"><span><?php echo $rs['tno']; ?></span></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于申请<?php echo $name; ?>质量等级核定的报告</div>
            <div class="pa4-company"><?php echo $gc; ?>:</div>
            <div class=""><?php echo $rs['tcontent']; ?></div>
            <div class="att">附件: 质量核定申报书</div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $company; ?><br/><?php echo $rs['tdate']; ?></span></div>
        </div>
    </div>
    <br/>
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center"><?php echo $rs['proj']; ?> 工程 <?php echo $rs['stage']; ?> 阶段竣工预验收</div>
            <br/><br/><br/><br/>
            <div class="pa4-title1">质 量 核 定 申 报 书</div>
            <br/><br/><br/><br/>
            <div class="center">编　　制：<?php echo $rs['signer']; ?>（签名）</div>
            <br/><br/>
            <div class="center">项目法人：<?php echo $rs['owner']; ?>（签名）</div>
            <br/><br/><br/><br/>
            <div class="right center">工程建设项目法人名称（盖公章）<br/><?php echo $rs['comp']; ?></div>
            <br/><br/><br/><br/>
            <div class="right">日期：<?php echo $rs['date']; ?></div>
            <br/><br/><br/><br/><br/><br/>
            <div class="">一、工程概况</div>
            <div class=""><?php echo $rs['c1']; ?></div>
            <br/>
            <div class="">二、主要设计变更</div>
            <div class=""><?php echo $rs['c2']; ?></div>
            <br/>
            <div class="">三、质量管理工作</div>
            <div class=""><?php echo $rs['c3']; ?></div>
            <br/>
            <div class="">四、质量检测情况</div>
            <div class=""><?php echo $rs['c4']; ?></div>
            <br/>
            <div class="">五、存在问题及处理情况</div>
            <div class=""><?php echo $rs['c5']; ?></div>
            <br/>
            <div class="">六、遗留问题和尾工处理</div>
            <div class=""><?php echo $rs['c6']; ?></div>
            <br/>
            <div class="">七、工程质量的自评、复核、认定结果</div>
            <div class=""><?php echo $rs['c7']; ?></div>
            <br/>
            <?php echo $atts; ?>
        </div>
        <input type="hidden" id="pid" value="<?php echo $rs['pid']; ?>" />
        <input type="hidden" id="fid" value="<?php echo $rs['id']; ?>" />
    </div>
    <?php
    else:
        echo HTML::AlertInfo('文档尚未创建');
    endif;
    ?>
</div>
