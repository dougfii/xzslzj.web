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
                $.post('?a=OnForget', {
                    name: $('#name').val(),
                    email: $('#email').val()
                }, function (ret) {
                    if (ret.code == 1)layer.msg("找回密码邮件已经发送到您的邮箱，请查看", 1, function(){Refresh();}); else layer.msg(ret.msg, 2, -1);
                }, 'json');
            });
        });
    </script>
</head>
<body>
<div class="body">
    <div class="loginp">
        <div class="loginc">忘记密码 - 邮箱找回</div>
        工程名称：<input type="text" id="name" placeholder="请输入工程名称" autofocus/><br/>
        电子邮箱：<input type="text" id="email" placeholder="请输入注册时的电子邮箱"/><br/>
        <input type="button" class="blogin" id="ok" value="提交"/><br/>
        <a href="javascript:GoBack();">返回</a>
    </div>
</div>
</body>
</html>