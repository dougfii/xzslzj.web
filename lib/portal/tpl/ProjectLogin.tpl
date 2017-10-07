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
                $.post('?m=Project&a=Login', {
                    name: $('#name').val(),
                    pass: $('#pass').val()
                }, function (ret) {
                    if (ret.code == 1)Redirect(ret.msg); else layer.msg(ret.msg, 2, -1);
                }, 'json');
            });

            $('#jok').click(function () {
                $.post('?m=Project&a=Join', {
                    gid: $('#gid').val(),
                    name: $('#jname').val(),
                    company: $('#jcompany').val(),
                    pass: $('#jpass').val(),
                    repass: $('#jrepass').val(),
                    contacts: $('#jcontacts').val(),
                    mobile: $('#jmobile').val(),
                    email: $('#jemail').val()
                }, function (ret) {
                    if (ret.code == 1)Redirect(ret.msg); else layer.msg(ret.msg, 2, -1);
                }, 'json');
            });

            $('#switch').click(function () {
                $('.loginp').hide();
                $('.loginj').fadeIn();
            });

            $('#jswitch').click(function () {
                $('.loginj').hide();
                $('.loginp').fadeIn();
            });
        });
    </script>
</head>
<body>
<div class="body">
    <div class="loginp">
        <div class="loginc"><?php echo APP_NAME; ?><br/>工程监督在线申报</div>
        工程名称：<input type="text" id="name" placeholder="请输入工程名称" autofocus/><br/>
        登录密码：<input type="password" id="pass" placeholder="请输入登录密码"/><br/>
        <input type="button" class="blogin" id="ok" value="登录"/><br/>
        <a href="javascript:void();" id="switch">新工程监督申请</a>　|　<a href="?a=forget">忘记密码</a>
    </div>
    <div class="loginj hidden">
        <div class="logint">新工程监督在线申请</div>
        质监机构：<?php echo $gid; ?><br/>
        工程名称：<input type="text" id="jname" placeholder="请输入工程名称" autofocus/><br/>
        申请单位：<input type="text" id="jcompany" placeholder="请输入申报单位" autofocus/><br/>
        登录密码：<input type="password" id="jpass" placeholder="请输入登录密码"/><br/>
        重复密码：<input type="password" id="jrepass" placeholder="请输入重复密码"/><br/>
        联 系 人：<input type="text" id="jcontacts" placeholder="请输入联系人姓名" autofocus/><br/>
        联系方式：<input type="text" id="jmobile" placeholder="请输入联系人的手机或电话号码" autofocus/><br/>
        电子邮箱：<input type="text" id="jemail" placeholder="请输入联系人的电子邮箱" autofocus/><br/>
        <input type="button" class="bjoin" id="jok" value="申请"/><br/>
        <a href="javascript:void();" id="jswitch">申报登录</a>
    </div>
</div>
</body>
</html>