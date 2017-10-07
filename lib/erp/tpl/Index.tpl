<!doctype html>
<html>
<head>
<title><?php echo APP_TITLE; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/layer.css" rel="stylesheet" />
<link href="css/erp.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="js/layer.js"></script>
<script type="text/javascript" src="js/erp.js"></script>
<script type="text/javascript">
$(function(){
	$('#ok').click(function(){
        $.post('?a=Login', {username:$('#username').val(),password:$('#password').val()}, function(ret){if(ret.code==1)Redirect(ret.msg);else layer.msg(ret.msg, 2, -1);}, 'json');
    });
});
</script>
</head>
<body>
<div class="body">
	<div class="loginp">
		<div class="loginc">水利工程质量监督站管理登陆</div>
		账号：<input type="text" id="username" placeholder="请输入用户名称" autofocus /><br />
		密码：<input type="password" id="password" placeholder="请输入用户密码" /><br />
		<input type="button" class="blogin" id="ok" value="登录" />
	</div>
</div>
</body>
</html>