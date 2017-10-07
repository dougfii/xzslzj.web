<script type="text/javascript">
    $(function () {
        $('#ok').click(function () {
            $.post('?m=Calendar&a=OnCalendarAdd', {date:$('#date').val(),stateid:$('#stateid').val(),customer:$('#customer').val(),contacts:$('#contacts').val(),content:$('#content').val(),plan:$('#plan').val(),walkway:$('#walkway').val(),distance:$('#distance').val(),memo:$('#memo').val()},
                    function (ret){if(ret.code==1)layer.msg('增加成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">增加日程</span></div>
</div>
<div class="panel paneltool">
    <table class="ti">
        <tr>
            <td class="k">日　期</td>
            <td class="v"><input type="text" id="date" class="r" onfocus="laydate()" value="<?php echo $date; ?>" readonly/></td>
            <td class="k">状　态</td>
            <td class="v"><?php echo $stateid; ?></td>
            <td class="k">客　户</td>
            <td class="v"><input type="text" class="r" id="customer"/></td>
            <td class="k">联系人</td>
            <td class="v"><input type="text" class="r" id="contacts"/></td>
        </tr>
        <tr>
            <td class="k">谈何事</td>
            <td class="v" colspan="3"><textarea id="content" class="r" rows="10" wrap="virtual"></textarea></td>
            <td class="k">后　续</td>
            <td class="v" colspan="3"><textarea id="plan" rows="10" wrap="virtual"></textarea></td>
        </tr>
        <tr>
            <td class="k">路　线</td>
            <td class="v"><input type="text" id="walkway"/></td>
            <td class="k">里　程</td>
            <td class="v"><input type="text" id="distance"/></td>
            <td class="k">备　注</td>
            <td class="v" colspan="3"><textarea id="memo" rows="5" wrap="virtual"></textarea></td>
        </tr>
    </table>
    <div class="bpan"><input type="button" id="ok" class="bok" value="确定"/><input type="button" class="bback" value="返回"/></div>
</div>