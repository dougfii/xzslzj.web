<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表 - [答复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
<?php
if(!empty($rs)):
?>
        <div class="pagea4info">
            <div class="pa4-caption1">关于<?php echo $name; ?>工程质量监督检查意见落实整改情况的说明</div>
            <div class="clear"><span><?php echo $rs['no']; ?></span><span class="right">签发人：<?php echo $rs['signer']; ?></span></div>
            <div class="pa4-redline"></div>
            <div class="pa4-company"><?php echo $gc; ?>:</div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1">单位：(项目法人) <?php echo $rs['comp']; ?><br/><?php echo $rs['date']; ?></span></div>
        </div>
<?php
else:
    echo HTML::AlertError();
endif;
?>
    </div>
</div>
