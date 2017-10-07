<div class="toolbar">
    <div class="tool clear"><span class="cap">质量监督检查意见表</span><a class="tooladd back" href="javascript:;">返回</a><a class="tooladd" href="?m=Project&a=ProjectFlow9999&pid=<?php echo $pid; ?>">发送新意见</a></div>
</div>
<div class="panel paneltool">
    <?php
    if(!empty($rl)){
        echo '<div>正在进行</div>';
        echo '<table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>发表时间</th>';
        echo '<th>状态</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><a href="?m=Project&a=ProjectFlow9999&fid=' . $rl['id'] . '">' . $rl['no'] . '</a></td>';
        echo '<td class="ftim">' . $rl['time'] . '</td>';
        echo '<td class="fsta">未答复</td>';
        echo '</tr>';
        echo '</table>';
        echo '<br/>';
    }
    ?>

    <?php
    if(!empty($rs)){
        echo '<div>已答复</div><table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>申报时间</th>';
        echo '<th>答复时间</th>';
        echo '<th>答复</th>';
        echo '</tr>';
        foreach($rs as $k => $v){
            echo '<tr>';
            echo '<td><a href="?m=Project&a=ProjectFlow9999&pid=' . $pid . '&fid=' . $v['id'] . '">' . $v['no'] . '</a></td>';
            echo '<td class="ftim">' . $v['time'] . '</td>';
            echo '<td class="ftim">' . $v['replytime'] . '</td>';
            echo '<td class="fsta"><a href="?m=Project&a=ProjectReply9999View&pid=' . $pid . '&fid=' . $v['id'] . '">查看</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else{
        echo HTML::AlertInfo();
    }
    ?>
</div>
