<div class="body body-index">
    <div class="articles-left">
        <?php echo $side; ?>
    </div>
    <div class="articles-right">
<?php
if(!empty($rs)):
?>
    <div class="contacts-title"><img src="/img/contacts.png"/><?php echo $rs['title']; ?></div>
    <div class="contacts-content autoimg"><?php echo html_entity_decode($rs['html']); ?></div>
<?php
else:
    echo HTML::AlertInfo();
endif;
?>
    </div>
</div>