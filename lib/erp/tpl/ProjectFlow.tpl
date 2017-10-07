<div class="toolbar">
    <div class="tool clear"><span class="cap">工程</span></div>
</div>
<div class="panel paneltool">
<?php
if(!empty($rs))
{
    echo '<div class="plbox"><div class="row">';
    echo '<div class="item"><a href="?m=Project&a=ProjectFlow4List&pid=' . $pid . '">质量监督检查意见表</a><span class="state">&nbsp;</span></div>';
    foreach(ProjectNodeCls::Items() as $k=>$v)
    {
        if($k==0 || $rs["n{$k}"]>0)
        {
            if($k==5 || $k==6 || $k==7 || $k==9)
            {
                echo '<div class="item">' . $k . '. ' . $v . '<span class="state">&nbsp;</span></div>';

                if($k==5)
                {
                    foreach(ProjectNodeCls::MaterialItems() as $m=>$n)
                    {
                        echo '<div class="item">　● <a href="?m=Project&a=ProjectFlow' . $m . 'List&pid=' . $pid . '">' . $n . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$m}"]) . '</span></div>';
                    }
                }

                if($k==6)
                {
                    foreach(ProjectNodeCls::CheckItems() as $m=>$n)
                    {
                        echo '<div class="item">　● <a href="?m=Project&a=ProjectFlow' . $m . 'List&pid=' . $pid . '">' . $n . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$m}"]) . '</span></div>';
                    }
                }

                if($k==7)
                {
                    foreach(ProjectNodeCls::RecordItems() as $m=>$n)
                    {
                        echo '<div class="item">　● <a href="?m=Project&a=ProjectFlow' . $m . 'List&pid=' . $pid . '">' . $n . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$m}"]) . '</span></div>';
                    }
                }

                if($k==9)
                {
                    $m = 91;
                    echo '<div class="item">　● <a href="?m=Project&a=ProjectFlow' . $m . 'List&pid=' . $pid . '">' . ProjectNodeCls::AcceptName($m) . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$m}"]) . '</span></div>';
                    $m = 92;
                    echo '<div class="item">　● ' . ProjectNodeCls::AcceptName($m) . '</div>';
                    foreach(ProjectNodeCls::Accept2Items() as $m=>$n)
                    {
                        echo '<div class="item">　　◆ <a href="?m=Project&a=ProjectFlow' . $m . 'List&pid=' . $pid . '">' . $n . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$m}"]) . '</span></div>';
                    }
                }
            }
            else
            {
                echo '<div class="item">' . $k . '. <a href="?m=Project&a=ProjectFlow' . $k . 'List&pid=' . $pid . '">' . $v . '</a><span class="state">' . ProjectStateCls::Name($rs["s{$k}"]) . '</span></div>';
            }
        }
    }
    echo '</div></div>';
}
?>
</div>