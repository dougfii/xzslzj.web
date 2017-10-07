<script type="text/javascript">
    $(function () {
        $('.del').click(function () {
            var id = $(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function (i){layer.close(i);$.post('?m=Calendar&a=OnCalendarDelete', {id:id}, function(ret){if(ret.code==1)Redirect('<?php echo Url::REQUEST_URI(); ?>');else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('#findok').click(function () {
            location.href = '<?php echo Url::URL_FULL(); ?>&name=' + $('#name').val() + '&stateid=' + $('#stateid').val() + '&typeid=' + $('#typeid').val();
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">日程列表</span><span id="openfind" title="查找"></span><?php echo $paged; ?><a class="tooladd" href="?m=Calendar&a=CalendarAdd">增加</a></div>
    <div class="find clear">
        <span>日程名称</span><input type="text" id="name" value="<?php echo $name; ?>"/>
        <span>状态</span><?php echo $stateid; ?>
        <input type="button" class="" id="findok" value="查找"/>
    </div>
</div>
<div class="panel paneltool">
    <?php if(!empty($rs)): ?>
    <table class="tl">
        <tr>
            <th>编号</th>
            <th>日期</th>
            <th>状态</th>
            <th>客户</th>
            <th>联系人</th>
            <th>谈何事</th>
            <th>后续</th>
            <th>路线</th>
            <th>里程</th>
            <th>备注</th>
            <th>用户</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        <?php foreach($rs as $k=>$v): ?>
        <tr>
            <td class="fid"><?php echo $v['id']; ?></td>
            <td class="fdat"><?php echo $v['date']; ?></td>
            <td class="fsta"><?php echo $v['state']; ?></td>
            <td class="fdat"><?php echo $v['customer']; ?></td>
            <td class="fdat"><?php echo $v['contacts']; ?></td>
            <td class=""><?php echo $v['content']; ?></td>
            <td class=""><?php echo $v['plan']; ?></td>
            <td class=""><?php echo $v['walkway']; ?></td>
            <td class=""><?php echo $v['distance']; ?></td>
            <td class=""><?php echo $v['memo']; ?></td>
            <td class="fusr"><?php echo $v['user']; ?></td>
            <td class="ftim"><?php echo $v['time']; ?></td>
            <td class="fopr"><a href="?m=Calendar&a=CalendarEdit&id=<?php echo $v['id']; ?>">编辑</a> <a href="#" class="del" did="<?php echo $v['id']; ?>">删除</a></td>
        </tr>
        <?php
	endforeach;
?>
    </table>
    <?php
else:
	echo HTML::AlertInfo();
endif;
?>
</div>