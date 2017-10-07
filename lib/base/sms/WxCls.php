<?php

class WxCls
{
    const CORPID = "wx4dac9fd439db72b5";
    const SECRET = "uO26Tyh0job8o-idv4t0eX4-Sul0xxi77WGiICmpPmSgFdNbFFDNkkmj29qeIp9Z";
    const EXPIRE = 3600;
    const CATAS = 'cache';
    const CACHE = 'ticket';
    //坐标转换
    const AK = 'caW2bPWzYbKUIIBo9mr17q1g';


    public function __construct()
    {
    }

    protected function Get($key)
    {
        $cache = new Cache (self::EXPIRE);
        $list = $cache->get(self::CATAS, self::CACHE);

        if ($list !== false && is_array($list) && array_key_exists($key))
        {
            return $list[$key];
        }
        return null;
    }

    protected function Set($key, $val)
    {
        $cache = new Cache (self::EXPIRE);
        $list = $cache->get(self::CATAS, self::CACHE);
        if ($list === false)
            $list = array();
        $list[$key] = $val;
        $cache->Set($list, self::CATAS, self::CACHE);
    }

    public static function Clear()
    {
        $cache = new Cache (self::EXPIRE);
        $cache->Delete(self::CATAS, self::CACHE);
    }

    public function GetToken()
    {
        $cache = new Cache (self::EXPIRE);
        $token = $cache->get(self::CATAS, 'token');

        //        $params = array('corpid' => self::CORPID, 'corpsecret' => self::SECRET);
        //        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
        //        $ch = new Curl();
        //        return $ch->GetPostJson($url, $params)['access_token'];

        if (empty($token))
        {
            try
            {
                $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . self::CORPID . "&corpsecret=" . self::SECRET;
                $res = file_get_contents($url);
                $token = json_decode($res, true);
                $cache->Set($token, self::CATAS, 'token');
            }
            catch (Exception $e)
            {
                $token = '';
            }
        }
        return $token['access_token'];
    }

    public function GetCode($redirecturl)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4dac9fd439db72b5&redirect_uri=" . $redirecturl . "&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";

        Url::RedirectUrl($url);
    }

    public function GetUserId($token, $code)
    {
        //        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
        //        $params = array('access_token' => $token, 'code' => $code);
        //        $ch = new Curl();
        //        return $ch->GetPostJson($url, $params);

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' . $token . '&code=' . $code;
        $res = file_get_contents($url);
        return json_decode($res, true);
    }

    public function GetSigned()
    {
        $ticket = $this->GetJsApiTicket();
        $nonce = $this->CreateNonce();
        $timestamp = time();
        $url = Url::URL_FULL();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $raw = "jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$timestamp&url=$url";
        $signed = sha1($raw);

        return array('appid' => self::CORPID, 'nonce' => $nonce, 'timestamp' => $timestamp, 'signed' => $signed, 'url' => $url, 'raw' => $raw);
    }

    private function GetJsApiTicket()
    {
        $ticket = $this->Get('ticket');

        if (is_null($ticket) || !is_array($ticket) || $ticket['expire_time'] < time())
        {
            $token = $this->GetToken();
            // 如果是企业号用以下 URL 获取 ticket
            $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=" . $token;
            //$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $token;
            $res = file_get_contents($url);
            $ticket = json_decode($res, true);
            if (!empty($ticket))
                $ticket['expire_time'] = time() + self::EXPIRE;

            $this->Set('ticket', $ticket);
        }
        return $ticket['ticket'];
    }

    private function CreateNonce($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++)
        {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function WxLocationToBaidu($lat, $lng)
    {
        $url = "http://api.map.baidu.com/geoconv/v1/?coords=" . $lat . "," . $lng . "&from=3&to=5&ak=" . self::AK;
        $res = file_get_contents($url);
        return json_decode($res, true);
    }

}

class WxException extends Exception
{
}

?>