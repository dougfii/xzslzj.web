<?php

class Data
{
    const ERROR = 0;
    const SUCCESS = 1;

    public static function Format($code, $msg, $data)
    {
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }

    public static function FormatSuccess($msg = '', $data = null)
    {
        return self::Format(self::SUCCESS, $msg, $data);
    }

    public static function FormatSuccessData($data = null)
    {
        return self::Format(self::SUCCESS, '', $data);
    }

    public static function FormatError($msg = '', $data = null)
    {
        return self::Format(self::ERROR, $msg, $data);
    }

    public static function FormatErrorData($data = null)
    {
        return self::Format(self::ERROR, '', $data);
    }
}

?>