<?php

class P
{
    /*
     * permission bit table BIN HEX DEC OPR 0001 0x1 1 View(Read/List) 0010 0x2 2 Add 0100 0x4 4 Edit 1000 0x8 8 Delete permission bit table EXTENSION BIN HEX DEC OPR 0001 0x1 1 Team Manager 0010 0x2 2 0100 0x4 4 1000 0x8 8
     */
    const BIT1 = 1;
    const BIT2 = 2;
    const BIT3 = 4;
    const BIT4 = 8;
    const BIT5 = 16;
    const BIT6 = 32;
    const BIT7 = 64;
    const BIT8 = 128;
    const BIT9 = 256;
    const BIT10 = 512;
    const BIT11 = 1024;
    const BIT12 = 2048;
    const BIT13 = 4096;
    const BIT14 = 8192;
    const BIT15 = 16384;
    const BIT16 = 32768;
    //
    const VIEW = 1;
    const ADD = 2;
    const EDIT = 4;
    const DELETE = 8;
    const EXPORT = 16;
    const TEAM = 32;

    //
    //
    private $uid = null;
    private $p = array();
    private static $instance = null;

    public static function Instance($uid)
    {
        if (self::$instance == null)
        {
            self::$instance = new self ($uid);
        }
        return self::$instance;
    }

    private function __construct($uid)
    {
        $this->uid = $uid;

        for ($i = 0; $i < R::TOTAL; $i++)
        {
            $this->p [$i] = 0;
        }

        $this->LoadUserPermission();
    }

    protected function LoadUserPermission()
    {
        $rs = UserCls::Instance()->Permissions($this->uid);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $this->p [$i] = $rs [$i];
            }
        }
    }

    public function IsCanBIT1($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT1($this->p [$res]);
        return false;
    }

    public function IsCanBIT2($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT2($this->p [$res]);
        return false;
    }

    public function IsCanBIT3($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT3($this->p [$res]);
        return false;
    }

    public function IsCanBIT4($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT4($this->p [$res]);
        return false;
    }

    public function IsCanBIT5($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT5($this->p [$res]);
        return false;
    }

    public function IsCanBIT6($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT6($this->p [$res]);
        return false;
    }

    public function IsCanBIT7($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT7($this->p [$res]);
        return false;
    }

    public function IsCanBIT8($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT8($this->p [$res]);
        return false;
    }

    public function IsCanBIT9($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT9($this->p [$res]);
        return false;
    }

    public function IsCanBIT10($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT10($this->p [$res]);
        return false;
    }

    public function IsCanBIT11($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT11($this->p [$res]);
        return false;
    }

    public function IsCanBIT12($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT12($this->p [$res]);
        return false;
    }

    public function IsCanBIT13($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT13($this->p [$res]);
        return false;
    }

    public function IsCanBIT14($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT14($this->p [$res]);
        return false;
    }

    public function IsCanBIT15($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT15($this->p [$res]);
        return false;
    }

    public function IsCanBIT16($res)
    {
        if (array_key_exists($res, $this->p))
            return self::IsBIT16($this->p [$res]);
        return false;
    }

    public function IsCanView($res)
    {
        return $this->IsCanBIT1($res);
    }

    public function IsCanAdd($res)
    {
        return $this->IsCanBIT2($res);
    }

    public function IsCanEdit($res)
    {
        return $this->IsCanBIT3($res);
    }

    public function IsCanDelete($res)
    {
        return $this->IsCanBIT4($res);
    }

    public function IsCanExport($res)
    {
        return $this->IsCanBIT5($res);
    }

    public function IsCanTeam($res)
    {
        return $this->IsCanBIT6($res);
    }

    public static function IsBIT1($var)
    {
        return (hexdec($var) & self::BIT1) ? true : false;
    }

    public static function IsBIT2($var)
    {
        return (hexdec($var) & self::BIT2) ? true : false;
    }

    public static function IsBIT3($var)
    {
        return (hexdec($var) & self::BIT3) ? true : false;
    }

    public static function IsBIT4($var)
    {
        return (hexdec($var) & self::BIT4) ? true : false;
    }

    public static function IsBIT5($var)
    {
        return (hexdec($var) & self::BIT5) ? true : false;
    }

    public static function IsBIT6($var)
    {
        return (hexdec($var) & self::BIT6) ? true : false;
    }

    public static function IsBIT7($var)
    {
        return (hexdec($var) & self::BIT7) ? true : false;
    }

    public static function IsBIT8($var)
    {
        return (hexdec($var) & self::BIT8) ? true : false;
    }

    public static function IsBIT9($var)
    {
        return (hexdec($var) & self::BIT9) ? true : false;
    }

    public static function IsBIT10($var)
    {
        return (hexdec($var) & self::BIT10) ? true : false;
    }

    public static function IsBIT11($var)
    {
        return (hexdec($var) & self::BIT11) ? true : false;
    }

    public static function IsBIT12($var)
    {
        return (hexdec($var) & self::BIT12) ? true : false;
    }

    public static function IsBIT13($var)
    {
        return (hexdec($var) & self::BIT13) ? true : false;
    }

    public static function IsBIT14($var)
    {
        return (hexdec($var) & self::BIT14) ? true : false;
    }

    public static function IsBIT15($var)
    {
        return (hexdec($var) & self::BIT15) ? true : false;
    }

    public static function IsBIT16($var)
    {
        return (hexdec($var) & self::BIT16) ? true : false;
    }

    public static function isView($var)
    {
        return self::IsBIT1($var);
    }

    public static function isAdd($var)
    {
        return self::IsBIT2($var);
    }

    public static function isEdit($var)
    {
        return self::IsBIT3($var);
    }

    public static function isDelete($var)
    {
        return self::IsBIT4($var);
    }

    public static function isExport($var)
    {
        return self::IsBIT5($var);
    }

    public static function isTeam($var)
    {
        return self::IsBIT6($var);
    }

    public static function isPrice($var)
    {
        return self::isBIT7($var);
    }

    public static function isCost($var)
    {
        return self::isBIT8($var);
    }

    public static function isCustomer($var)
    {
        return self::isBIT9($var);
    }

    public static function make($bit1, $bit2, $bit3, $bit4, $bit5, $bit6, $bit7, $bi8, $bit9, $bit10, $bit11, $bit12, $bit13, $bit14, $bit15, $bit16)
    {
        $mask = 0;
        if ($bit1)
            $mask += 1; // view
        if ($bit2)
            $mask += 2; // add
        if ($bit3)
            $mask += 4; // edit
        if ($bit4)
            $mask += 8; // delete
        if ($bit5)
            $mask += 16; // export
        if ($bit6)
            $mask += 32; // team
        if ($bit7)
            $mask += 64; // price, jackup
        if ($bi8)
            $mask += 128; // cost
        if ($bit9)
            $mask += 256; // customer
        if ($bit10)
            $mask += 512;
        if ($bit11)
            $mask += 1024;
        if ($bit12)
            $mask += 2048;
        if ($bit13)
            $mask += 4096;
        if ($bit14)
            $mask += 8192;
        if ($bit15)
            $mask += 16384;
        if ($bit16)
            $mask += 32768;
        return dechex($mask);
    }

    public static function Clean($str)
    {
        $str = explode(',', $str);

        $p = array();
        for ($i = 0; $i < R::TOTAL; $i++)
        {
            $p [$i] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        }

        foreach ($str as $v)
        {
            if (!empty ($v))
            {
                $v = explode('_', $v);
                if (count($v) == 2)
                {
                    $m = intval($v [0]);
                    $n = intval($v [1]) - 1;
                    $p [$m] [$n] = 1;
                }
            }
        }

        $j = array();
        for ($i = 0; $i < R::TOTAL; $i++)
        {
            $j [$i] = P::make($p [$i] [0], $p [$i] [1], $p [$i] [2], $p [$i] [3], $p [$i] [4], $p [$i] [5], $p [$i] [6], $p [$i] [7], $p [$i] [8], $p [$i] [9], $p [$i] [10], $p [$i] [11], $p [$i] [12], $p [$i] [13], $p [$i] [14], $p [$i] [15]);
        }

        return implode(',', $j);
    }

    public static function Init($admin = false)
    {
        $a = array();

        for ($i = 0; $i < R::TOTAL; $i++)
        {
            $a [$i] = $admin ? 'ffff' : '0000';
        }

        return implode(',', $a);
    }

    public static function GetPermission($val)
    {
        $p = array();
        for ($i = 0; $i < R::TOTAL; $i++)
        {
            $p [$i . '_1'] = '';
            $p [$i . '_2'] = '';
            $p [$i . '_3'] = '';
            $p [$i . '_4'] = '';
            $p [$i . '_5'] = '';
            $p [$i . '_6'] = '';
            $p [$i . '_7'] = '';
            $p [$i . '_8'] = '';
            $p [$i . '_9'] = '';
            $p [$i . '_10'] = '';
            $p [$i . '_11'] = '';
            $p [$i . '_12'] = '';
            $p [$i . '_13'] = '';
            $p [$i . '_14'] = '';
            $p [$i . '_15'] = '';
            $p [$i . '_16'] = '';
        }

        $m = explode(',', $val);
        foreach ($m as $k => $v)
        {
            if ($k < R::TOTAL)
            {
                if (self::isView($v))
                    $p [$k . '_1'] = 'checked';
                if (self::isAdd($v))
                    $p [$k . '_2'] = 'checked';
                if (self::isEdit($v))
                    $p [$k . '_3'] = 'checked';
                if (self::isDelete($v))
                    $p [$k . '_4'] = 'checked';
                if (self::isExport($v))
                    $p [$k . '_5'] = 'checked';
                if (self::isTeam($v))
                    $p [$k . '_6'] = 'checked';
                if (self::IsBIT7($v))
                    $p [$k . '_7'] = 'checked';
                if (self::IsBIT8($v))
                    $p [$k . '_8'] = 'checked';
                if (self::IsBIT9($v))
                    $p [$k . '_9'] = 'checked';
                if (self::IsBIT10($v))
                    $p [$k . '_10'] = 'checked';
                if (self::IsBIT11($v))
                    $p [$k . '_11'] = 'checked';
                if (self::IsBIT12($v))
                    $p [$k . '_12'] = 'checked';
                if (self::IsBIT13($v))
                    $p [$k . '_13'] = 'checked';
                if (self::IsBIT14($v))
                    $p [$k . '_14'] = 'checked';
                if (self::IsBIT15($v))
                    $p [$k . '_15'] = 'checked';
                if (self::IsBIT16($v))
                    $p [$k . '_16'] = 'checked';
            }
        }

        return $p;
    }
}

?>