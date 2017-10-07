<script>
    $(function(){
        $('.read').click(function(){
            var id=$(this).attr('rid');
            $.post('?m=Project&a=OnRead', {id:id}, function(ret){if(ret.code==1)Refresh();else layer.msg(ret.msg, 2, -1);}, 'json');
        });
    });
</script>
<div class="panel">
    <div class="">未读消息</div>
    <?php
    if(!empty($rs))
    {
        echo '<table class="tl">';
        echo '<tr><th>序号</th><th>发送者</th><th>消息</th><th>时间</th><th>操作</th></tr>';
        foreach($rs as $v)
        {
            echo '<tr>';
            echo '<td class="fid">' . $v['id'] . '</td>';
            echo '<td class="fproj">' . $v['sender'] . '</td>';
            echo '<td class=""><a href="?m=Project&a=Progress&id=' . $v['objid'] . '">' . $v['msg'] . '</a></td>';
            echo '<td class="ftime">' . $v['time'] . '</td>';
            echo '<td class="fsta"><a href="javascript:;" class="read" rid="' . $v['id'] . '">已读</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo HTML::AlertInfo();
    }
    ?>
</div>
