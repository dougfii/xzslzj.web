<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督申报 - [批复]</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
<?php
if(!empty($rs)):
?>
        <div class="pagea4info">
            <div class="pa4-caption1"><?php echo $gc; ?></div>
            <div class="center"><?php echo $rs['no']; ?></div>
            <div class="pa4-redline"></div>
            <div class="pa4-title1">关于对<?php echo $name; ?>质量监督的批复</div>
            <div class="pa4-company"><?php echo $company; ?>:</div>
            <div class="pa4-content"><?php echo $rs['content']; ?></div>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $gc; ?><br/><?php echo $rs['date']; ?></span></div>
            <input type="hidden" id="fid" value="<?php echo $fid; ?>" />
        </div>
<?php
else:
    echo HTML::AlertError();
endif;
?>
    </div>
</div>
