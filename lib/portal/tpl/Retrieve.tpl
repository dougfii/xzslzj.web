<!doctype html>
<html>
<head>
    <title><?php echo APP_NAME; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/layer.css" rel="stylesheet"/>
    <link href="/css/member.css" rel="stylesheet"/>
    <script src="/js/jquery-1.11.3.min.js"></script>
    <script src="/js/layer.js"></script>
    <script src="/js/member.js"></script>
    <script>
        $(function () {
            $('#ok').click(function () {
                $.post('?a=OnRetrieve', {
                    pass: $('#pass').val(),
                    repass: $('#repass').val(),
                    pid: $('#pid').val(),
                    code: $('#code').val()
                }, function (ret) {
                    if (ret.code == 1)layer.msg("密码重置成功，请重新登录", 1, function(){Redirect('?m=project');}); else layer.msg(ret.msg, 2, -1);
                }, 'json');
            });
        });
    </script>
</head>
<body>
<div class="body">
    <div class="loginp">
        <div class="loginc">重置密码</div>
        新设密码：<input type="password" id="pass" placeholder="请输入新设置的密码" autofocus/><br/>
        重复密码：<input type="password" id="repass" placeholder="请再输入一遍密码"/><br/>
        <input type="hidden" id="pid" value="<?php echo $pid; ?>"/><input type="hidden" id="code" value="<?php echo $code; ?>"/>
        <input type="button" class="blogin" id="ok" value="提交"/><br/>
    </div>
</div>
</body>
</html>