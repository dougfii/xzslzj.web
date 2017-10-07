<div class="body body-index">
<?php
if(!empty($rs)):
?>
    <div class="article-title"><?php echo $rs['title']; ?></div>
    <div class="article-info"><?php echo '发布日期: ' . Format::DateString($rs['time']) . '　浏览: ' . $rs['views']; ?></div>
    <div class="article-content autoimg"><?php echo html_entity_decode($rs['html']); ?></div>
    <div class="article-back"><a href="javascript:window.close();">[ 关闭 ]</a></div>
<?php
else:
    echo HTML::AlertInfo();
endif;
?>
</div>