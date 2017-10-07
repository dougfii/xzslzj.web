<?php

class MailHelpCls
{
    public static function sendForget($email, $title, $href)
    {
        $cont = '<p>请点击下面链接重新设置密码，使修改生效。为了保障您帐号的安全性，请及时完成操作！（将此链接复制到浏览器地址栏后访问也可完成重置密码） </p><br/>';
        $cont .= '<p>如果您没有请求重置密码，请忽略此邮件。</p><br/>';
        $cont .= '<p><a href="' . $href . '" target="_blank">' . $href . '</a></p><br/><br/>';
        $cont .= '<p>' . $href . '</p><br/><br/>';
        $cont .= "<p>感谢您使用徐州水利工程质量监督管理站网站！  系统邮件，请勿回复</p>";

        $zw = self::html($title, $cont);
        $mail = new MailSend ();

        return $mail->SendToSingle($email, $title, $zw) !== false;
    }

    private static function html($title, $content)
    {
        $s = '
<!doctype html><html><head><meta charset="utf-8">
<style type="text/css">
body{color:#000;background:#fff;font-size:12px;}
.m{margin:0 auto;width:700px;}
.n{background:#eb503b;height:40px;line-height:40px;color:#fff;font-size:16px;font-family:"Microsoft YaHei";padding-left:20px;}
.n a{color:#eee;text-decoration:none;}
.h{color:#fff;background:#1d2331;padding:20px 10px;font-size:14px;font-family:"Microsoft YaHei";padding-left:20px;}
.c{background:#f8f8f8;padding:10px;min-height:300px;line-height:150%;}
.c a{color:#6b92d3;text-decoration:none;}
.f{color:#999;border-top:#ddd dashed 1px;padding:20px 10px;background:#eaeaea;text-align:right;font-family:"Microsoft YaHei";}
</style>
</head>
	
<body>
<div class="m">
<div class="n">徐州水利工程质量监督管理站  <a href="http://www.xzslzj.cn">ZXSLZJ.CN</a></div>
<div class="h">' . $title . '</div>
<div class="c">' . $content . '</div>
<div class="f">Copyright © 徐州水利工程质量监督管理站 2016-2017 All Right Reserved.</div>
</div>
</body>
</html>
		';
        return $s;
    }
}

?>