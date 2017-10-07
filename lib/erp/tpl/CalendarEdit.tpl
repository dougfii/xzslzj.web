<script type="text/javascript">
    $(function () {
        $('#ok').click(function () {
            $.post('?m=Calendar&a=OnCalendarEdit', {id:$('#id').val(),date:$('#date').val(),stateid:$('#stateid').val(),customer:$('#customer').val(),contacts:$('#contacts').val(),content:$('#content').val(),plan:$('#plan').val(),walkway:$('#walkway').val(),distance:$('#distance').val(),memo:$('#memo').val()},
                    function (ret){if(ret.code==1)layer.msg('编辑成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<?php if(!empty($rs)): ?>
<div class="toolbar">
    <div class="tool clear"><span class="cap">编辑日程</span></div>
</div>
<div class="panel paneltool">
    <table class="ti">
        <tr>
            <td class="k">日　期</td>
            <td class="v"><input type="text" id="date" class="r" onfocus="laydate()" value="<?php echo $rs['date']; ?>" readonly/></td>
            <td class="k">状　态</td>
            <td class="v"><?php echo $stateid; ?></td>
            <td class="k">客　户</td>
            <td class="v"><input type="text" class="r" id="customer" value="<?php echo $rs['customer']; ?>"/></td>
            <td class="k">联系人</td>
            <td class="v"><input type="text" class="r" id="contacts" value="<?php echo $rs['contacts']; ?>"/></td>
        </tr>
        <tr>
            <td class="k">谈何事</td>
            <td class="v" colspan="3"><textarea id="content" class="r" rows="10" wrap="virtual"><?php echo $rs['content']; ?></textarea></td>
            <td class="k">后　续</td>
            <td class="v" colspan="3"><textarea id="plan" rows="10" wrap="virtual"<?php echo $rs['plan']; ?>></textarea></td>
        </tr>
        <tr>
            <td class="k">路　线</td>
            <td class="v"><input type="text" id="walkway" value="<?php echo $rs['walkway']; ?>"/></td>
            <td class="k">里　程</td>
            <td class="v"><input type="text" id="distance" value="<?php echo $rs['distance']; ?>"/></td>
            <td class="k">备　注</td>
            <td class="v" colspan="3"><textarea id="memo" rows="5" wrap="virtual"><?php echo $rs['memo']; ?></textarea></td>
        </tr>
        <tr>
            <td class="k">用　户</td>
            <td class="v"><?php echo $rs['user']; ?></td>
            <td class="k">时　间</td>
            <td class="v" colspan="5"><?php echo $rs['time']; ?></td>
        </tr>
    </table>
</div>
<input type="hidden" id="id" value="<?php echo $rs['id']; ?>"/>
<div class="bpan">
    <button id="ok" class="bok">确定</button>
    　　
    <button class="bback" onclick="go(-1)">返回</button>
</div>
<?php
else:
	echo HTML::AlertError();
endif;
?>