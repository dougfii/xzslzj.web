<script type="text/javascript">
$(function(){
    $('#okp').click(function(){
        layer.confirm('您确认需要重置密码吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnProjectPassword', {id:$('#id').val(),pass:$('#pass').val()}, function(ret){if(ret.code==1)layer.msg('重置密码成功', 2, function(){location.reload();});else layer.msg(ret.msg, 2, -1);}, 'json');});
    });

    $('#ok').click(function(){
        $.post('?m=Project&a=OnProjectInfo', {id:$('#id').val(),name:$('#name').val(),company:$('#company').val()}, function (ret){if(ret.code==1)layer.msg('修改成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
    });
});
</script>
<?php
if(!empty($rs)):
?>
<div class="toolbar">
    <div class="tool clear"><span class="cap">项目编辑</span><a class="tooladd back" href="javascript:;">返回</a></div>
</div>
<div class="panel paneltool">
    <table class="tx1">
        <tr>
            <td class="k">序号</td>
            <td class="v"><?php echo $rs['id']; ?></td>
            <td class="k">组织</td>
            <td class="v"><?php echo $rs['group']; ?></td>
            <td class="k">工程名称</td>
            <td class="v"><input type="text" id="name" value="<?php echo $rs['name']; ?>"/></td>
            <td class="k">申报单位</td>
            <td class="v"><input type="text" id="company" value="<?php echo $rs['company']; ?>"/></td>
        </tr>
        <tr>
            <td class="k">联系人</td>
            <td class="v"><?php echo $rs['contacts']; ?></td>
            <td class="k">联系方式</td>
            <td class="v"><?php echo $rs['mobile']; ?></td>
            <td class="k">邮箱</td>
            <td class="v"><?php echo $rs['email']; ?></td>
            <td class="k">注册时间</td>
            <td class="v"><?php echo $rs['time']; ?></td>
        </tr>
        <tr>
            <td class="k">最后登录</td>
            <td class="v"><?php echo $rs['last']; ?></td>
            <td class="k">重置密码</td>
            <td colspan="5"><input type="text" id="pass"/><input type="button" id="okp" value="重置"/></td>
        </tr>
    </table>
    <div class="bpan"><input type="button" id="ok" class="bok" value="确定"/></div>
    <input type="hidden" id="id" value="<?php echo $rs['id']; ?>" />
</div>
<?php
else:
	echo HTML::AlertError();
endif;
?>