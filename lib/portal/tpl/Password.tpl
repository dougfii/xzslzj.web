<script type="text/javascript">
    $(function(){
        $('#ok').click(function(){
            $.post('?m=Project&a=OnPassword', {opass:$('#opass').val(),npass:$('#npass').val(),rpass:$('#rpass').val()}, function (ret){if(ret.code==1)layer.msg('密码修改成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">修改密码</span></div>
</div>
<div class="panel paneltool">
    <br/>
    <div>原始密码 <input type="password" id="opass"/></div>
    <br/>
    <div>新设密码 <input type="password" id="npass"/></div>
    <br/>
    <div>重复密码 <input type="password" id="rpass"/></div>
    <br/>
    <div class=""><input type="button" id="ok" class="bok" value="确定"/>　<input type="button" class="bback" value="返回"/></div>
</div>