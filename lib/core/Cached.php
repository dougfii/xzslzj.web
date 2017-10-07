<?php

class Cached
{
    protected static $conn = null;

    public function __construct()
    {
        self::Connect();
    }

    public function __destruct()
    {
        self::Disconnect();
    }

    protected static function Connect()
    {
        if (is_null(self::$conn)) {
            $compression = isset ($conf ['compression']) && !is_null($conf ['compression']) ? $conf ['compression'] : false;
            $binary = isset ($conf ['binary']) && !is_null($conf ['binary']) ? $conf ['binary'] : true;

            $conn = null;
            $conf = Config::Instance()->memecached;
            if (!is_null($conf)) {
                $conn = new Memcached();
                $conn->setOption(Memcached::OPT_COMPRESSION, $compression);
                $conn->setOption(Memcached::OPT_BINARY_PROTOCOL, $binary);
                $conn->addServer($conf['host'], $conf['port']);
                $conn->setSaslAuthData($conf['username'], $conf['password']);
            }

            self::$conn = $conn;
        }

        if (!is_null(self::$conn)) echo 'ok'; else echo 'err';
        return self::$conn;
    }

    protected static function Disconnect()
    {
        if (isset (self::$conn) && !is_null(self::$conn)) {
            self::$conn->quit();
            self::$conn = null;
        }
    }

    public static function Get($key)
    {
        return self::Connect()->get($key);
    }

    public static function Set($key, $val, $expire = 0)
    {
        self::Connect()->set($key, $val, $expire);
    }

    public static function Add($key, $val, $expire = 0)
    {
        self::Set($key, $val, $expire);
    }

    public static function Replace($key, $val, $expire = 0)
    {
        self::Connect()->replace($key, $val, $expire);
    }

    public static function Delete($key)
    {
        self::Connect()->delete($key);
    }
}

?>