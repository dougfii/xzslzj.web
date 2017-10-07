<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <?php
    /*
    if(!empty($rl)){
        echo '<div>正在进行</div>';
        echo '<table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>发表时间</th>';
        echo '<th>状态</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><a href="?m=Project&a=ProjectFlow4">' . $rl['no'] . '</a></td>';
        echo '<td class="ftim">' . $rl['time'] . '</td>';
        echo '<td class="fsta"><a href="?m=Project&a=ProjectReply4&fid=' . $rl['id'] . '">立即答复</a></td>';
        echo '</tr>';
        echo '</table>';
        echo '<br/>';
    }
    */
    ?>

    <?php
    if(!empty($rs)){
        //echo '<div>已答复</div>';
        echo '<table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>申报时间</th>';
        echo '<th>答复时间</th>';
        echo '<th>操作</th>';
        echo '</tr>';
        foreach($rs as $k => $v){
            echo '<tr>';
            echo '<td><a href="?m=Project&a=ProjectFlow4&id=' . $v['id'] . '">' . $v['no'] . '</a></td>';
            echo '<td class="ftim">' . $v['time'] . '</td>';
            echo '<td class="ftim">' . $v['replytime'] . '</td>';
            if($v['replyid']>0){
                echo '<td class="fsta"><a href="?m=Project&a=ProjectReply4View&fid=' . $v['id'] . '">查看</a></td>';
            }
            else{
                echo '<td class="fsta"><a href="?m=Project&a=ProjectReply4&fid=' . $v['id'] . '">立即答复</a></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    else{
        echo HTML::AlertInfo();
    }
    ?>
</div>
