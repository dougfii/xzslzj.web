<?php

class Json
{
    const ERROR = 0;
    const SUCCESS = 1;

    public static function ReturnError($msg = '', $data = null)
    {
        echo self::Output(self::ERROR, $msg, $data);
        exit ();
    }

    public static function ReturnSuccess($msg = '', $data = null)
    {
        echo self::Output(self::SUCCESS, $msg, $data);
        exit ();
    }

    public static function ReturnCode($code, $msg = '', $data = null)
    {
        echo self::Output($code, $msg, $data);
        exit ();
    }

    public static function Error($msg = '', $data = null)
    {
        return self::Output(self::ERROR, $msg, $data);
    }

    public static function Success($msg = '', $data = null)
    {
        return self::Output(self::SUCCESS, $msg, $data);
    }

    public static function Output($code, $msg = '', $data = null)
    {
        return self::Out(self::Format($code, $msg, $data));
    }

    private static function Format($code, $msg, $data)
    {
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }

    public static function Out($array)
    {
        return Coding::JSON2UTF8(json_encode($array, true));
    }

    //解决JSON串必须为UTF8
    public static function Encode($array)
    {
        return json_encode($array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    public static function Decode($json, $isArray = true)
    {
        return json_decode($json, $isArray);
    }
}

?>