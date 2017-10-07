<div class="body body-index">
    <div class="articles-left">
        <?php echo $side; ?>
    </div>
    <div class="articles-right">
        <div class="articles-title"><img src="/img/caption.png"/><?php echo $title; ?></div>
<?php
if(!empty($rs))
{
    echo '<div class="articles-content"><ul>';
    foreach($rs as $v)
    {
        echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank">' . $v['title'] . '';
        $diff = Date::DiffDay(time(), $v['time']);
        if($diff<=15) echo '　<img src="/img/new.gif"/>';
        echo '</a><i>' . Format::DateString($v['time']) . '</i></li>';
    }
    echo '</ul></div>';
    echo '<div class="articles-paged">' . $paged . '</div>';
}
else
{
    echo HTML::AlertInfo();
}
?>
    </div>
</div>