<div class="toolbar">
    <div class="tool clear"><span class="cap">单位工程外观质量结论</span><a class="tooladd back" href="javascript:;">返回</a><!-- <?php if($new) echo '<a class="tooladd" href="?m=Project&a=ProjectFlow62">新建</a>'; ?> --></div>
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
        echo '<td><a href="?m=Project&a=ProjectFlow62">' . $rl['no'] . '</a></td>';
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
            echo '<td><a href="?m=Project&a=ProjectFlow62&id=' . $v['id'] . '">' . $v['no'] . '</a></td>';
            echo '<td class="ftim">' . $v['time'] . '</td>';
            echo '<td class="fusr">' . $v['user'] . '</td>';
            echo '<td class="ftim">' . $v['replytime'] . '</td>';
            echo '<td class="fsta"><a href="?m=Project&a=ProjectReply62View&fid=' . $v['id'] . '">查看</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else{
        echo HTML::AlertInfo();
    }

    echo '<br/><br/>';

    if(!empty($import)){
    echo '<div>可导入列表</div><table class="tl">';
        echo '<tr>';
        echo '<th>编号</th>';
        echo '<th>申报时间</th>';
        echo '<th>批复者</th>';
        echo '<th>批复时间</th>';
        echo '<th>批复</th>';
        echo '<th>导入</th>';
        echo '</tr>';
        foreach($import as $k => $v){
        echo '<tr>';
            echo '<td><a href="?m=Project&a=ProjectFlow3&id=' . $v['id'] . '" target="_blank">' . $v['no'] . '</a></td>';
            echo '<td class="ftim">' . $v['time'] . '</td>';
            echo '<td class="fusr">' . $v['user'] . '</td>';
            echo '<td class="ftim">' . $v['replytime'] . '</td>';
            echo '<td class="fsta"><a href="?m=Project&a=ProjectReply3View&fid=' . $v['id'] . '">查看</a></td>';
            echo '<td class="fsta"><a href="?m=Project&a=ProjectFlow62&iid=' . $v['id'] . '">导入</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>
</div>
