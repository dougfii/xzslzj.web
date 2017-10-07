<div class="toolbar">
    <div class="tool clear"><span class="cap">单元工程质量项目划分</span><a class="tooladd back" href="javascript:;">返回</a></div>
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
        echo '<td><a href="?m=Project&a=ProjectFlow2&pid=' . $pid . '">' . $rl['no'] . '</a></td>';
        echo '<td class="ftim">' . $rl['time'] . '</td>';
        echo '<td class="fsta">' . $state . '</td>';
        echo '</tr>';
        echo '</table>';
        echo '<br/>';
    }

    if(!empty($rr)){
        echo '<table class="tl">';
        echo '<tr>';
        echo '<th>最后审退批复</th>';
        echo '<th>批复者</th>';
        echo '<th>批复时间</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>' . $rr['content'] . '</td>';
        echo '<td class="fusr">' . $rr['user'] . '</td>';
        echo '<td class="ftim">' . $rr['time'] . '</td>';
        echo '</tr>';
        echo '</table>';
        echo '<br/>';
    }
    ?>
    <?php
    if(!empty($rs)){
        echo '<div>审批通过</div><table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>申报时间</th>';
        echo '<th>批复者</th>';
        echo '<th>批复时间</th>';
        echo '<th>批复</th>';
        echo '</tr>';
        foreach($rs as $k => $v){
            echo '<tr>';
            echo '<td><a href="?m=Project&a=ProjectFlow2&pid=' . $pid . '&id=' . $v['id'] . '">' . $v['no'] . '</a></td>';
            echo '<td class="ftim">' . $v['time'] . '</td>';
            echo '<td class="fusr">' . $v['user'] . '</td>';
            echo '<td class="ftim">' . $v['replytime'] . '</td>';
            echo '<td class="fsta"><a href="?m=Project&a=ProjectReply2View&pid=' . $pid . '&fid=' . $v['id'] . '">查看</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else{
        echo HTML::AlertInfo();
    }
    ?>
</div>
