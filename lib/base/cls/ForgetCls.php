<?php

class ForgetCls
{
    const TABLE = 't_forget';
    private static $instance = null;

    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new self ();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public static function Count($where = '')
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array());
        if ($rs && count($rs) > 0 && $rs ['count'] > 0)
            return $rs ['count'];
        return 0;
    }

    public static function Add($pid, $code)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(pid, code)
				VALUES(?,?)
				RETURNING id
				", array($pid, $code));
        return $rs ['id'];
    }

    public static function Exist($pid, $code)
    {
        return (self::Count("WHERE pid={$pid} AND LOWER(code)=LOWER('{$code}') AND expire>=CURRENT_TIMESTAMP") > 0);
    }
}

?>