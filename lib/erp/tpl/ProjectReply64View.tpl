<div class="toolbar">
    <div class="tool clear"><span class="cap">工程项目质量等级核定表 - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
<?php
if(!empty($rs)):
?>
        <div class="pagea4info">
            <div class="pa4-reply">关于对<?php echo $name; ?>工程项目质量等级核定表的批复</div>
            <div class=""><?php echo $rs['content']; ?></div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $gc; ?><br/><?php echo Format::DateString($rs['time']); ?></span></div>
            <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
        </div>
<?php
else:
    echo HTML::AlertError();
endif;
?>
    </div>
</div>
