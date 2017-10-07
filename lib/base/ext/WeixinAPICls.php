<?php

class WeixinAPICls
{

    public function valid()
    {
        $echoStr = $_GET ["echostr"];

        // valid signature , option
        if ($this->checkSignature())
        {
            echo $echoStr;
            exit ();
        }
    }

    public function responseMsg()
    {
        // get post data, May be due to the different environments
        $postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];

        // extract post data
        if (!empty ($postStr))
        {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $type = $postObj->MsgType;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if (!empty ($keyword))
            {
                $m = '';
                if ($type == 'text')
                    $m = '发送文字';

                $msgType = "text";
                $contentStr = "欢迎{$m}到广通";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
            else
            {
                echo "Input something...";
            }
        }
        else
        {
            echo "";
            exit ();
        }
    }

    private function checkSignature()
    {
        $signature = $_GET ["signature"];
        $timestamp = $_GET ["timestamp"];
        $nonce = $_GET ["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    const CORPID = "wx4dac9fd439db72b5";
    const SECRET = "uO26Tyh0job8o-idv4t0eX4-Sul0xxi77WGiICmpPmSgFdNbFFDNkkmj29qeIp9Z";

    public function GetTOKEN()
    {
        //$CorpID = CORPID;
        //$Secret = SECRET;
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . CORPID . "&corpsecret=" . SECRET;
        return $url;
    }
}

?>