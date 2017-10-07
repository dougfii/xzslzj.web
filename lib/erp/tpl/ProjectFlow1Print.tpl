<div class="panel paneltool print1">
    <?php
    if(!empty($rs)):
    ?>
    <div class="pagea4" id="print_area">
        <div class="pagea4info">
            <link href="css/print.css" rel="stylesheet" type="text/css"/>
            <div class="pa4-caption1"><?php echo $name; ?>文件</div>
            <div class="clear"><div class="left"><?php echo $rs['no']; ?></div><div class="right">签发:<?php echo $rs['signer']; ?></div></div>
            <br/><div class="pa4-redline" height="1"></div>
            <div class="pa4-title1">关于申请<?php echo $name; ?>质量监督的请示</div>
            <div class="pa4-company"><?php echo COMPANY_NAME; ?>:</div>
            <div class=""><?php echo $rs['content']; ?></div>
            <?php echo $atts; ?>
            <div class="pa4-sign1 clear"><span class="pa4-signer1"><?php echo $company; ?><br/><?php echo $rs['date']; ?></span></div>
            <div class="pa4-attachment1">关键词:</div>
            <div class=""><?php echo $rs['keywords']; ?></div>
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
