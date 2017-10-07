<?php

class Util
{
    // *** 通用函数 ***//
    //
    // 组合数组为字符串
    public static function Impode($val, $delimiter = ',')
    {
        return implode($delimiter, $val);
    }

    // 分割字符串为数组
    public static function Explode($val, $delimiter = ',', $strict = true)
    {
        $rs = explode($delimiter, $val);

        if ($strict) {
            $rx = array();

            foreach ($rs as $v) {
                $v = trim($v);
                if (!is_null($v) && !self::IsBlank($v)) $rx [] = $v;
            }

            $rs = $rx;
        }

        return $rs;
    }

    // 分割字符串为二维数组 DON
    public static function ExplodeDouble($val, $delimiter1 = '^', $delimiter2 = '`')
    {
        $rs = array();

        $r1 = explode($delimiter1, $val);
        foreach ($r1 as $va) {
            $r2 = explode($delimiter2, $va);
            $ar = array();
            foreach ($r2 as $v) {
                $v = trim($v);
                $ar [] = $v;
            }
            $rs [] = $ar;
        }

        return $rs;
    }

    // 切分字符串为数组
    public static function Split($val, $delimiter = "/[\s\r\n|:,;]/")
    {
        return preg_split($delimiter, $val, -1, PREG_SPLIT_NO_EMPTY);
    }

    // 列表式字符串重组为适于SQL中IN为字符串类型的格式
    public static function SqlStr($val, $delimiter = ',', $strict = true)
    {
        $rs = self::Explode($val, $delimiter, $strict);
        if (!empty ($rs)) return "'" . implode("','", $rs) . "'";
        return '';
    }

    // 列表式字符串重组为适于SQL中IN为数值类型的格式
    public static function SqlNum($val, $delimiter = ',', $strict = true)
    {
        $rs = self::Explode($val, $delimiter, $strict);
        if (!empty ($rs)) return "'" . implode("','", $rs) . "'";
        return '';
    }

    // 从头截断字符串
    public static function CutStr($val, $len, $encode = 'utf8')
    {
        return self::SubStr($val, 0, $len, $encode);
    }

    // 截取字符串
    public static function SubStr($val, $start = 0, $len, $encode = 'utf8')
    {
        if ($encode == 'utf8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $val, $t_string);

            if (count($t_string [0]) - $start > $len) return join('', array_slice($t_string [0], $start, $len)) . "...";
            return join('', array_slice($t_string [0], $start, $len));
        } else {
            $start = $start * 2;
            $len = $len * 2;
            $strlen = strlen($val);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $len)) {
                    if (ord(substr($val, $i, 1)) > 129) {
                        $tmpstr .= substr($val, $i, 2);
                    } else {
                        $tmpstr .= substr($val, $i, 1);
                    }
                }
                if (ord(substr($val, $i, 1)) > 129) $i++;
            }
            if (strlen($tmpstr) < $strlen) $tmpstr .= "...";
            return $tmpstr;
        }
    }

    // 分割文本行为字符串数组
    public static function ExplodeLine($val, $strict = true)
    {
        $rs = self::Split($val, "/[\r\n]/");

        if ($strict) {
            $rx = array();

            foreach ($rs as $v) {
                $v = trim($v);
                if (!is_null($v) && !self::IsBlank($v)) $rx [] = $v;
            }

            $rs = $rx;
        }

        return $rs;
    }

    // 文本行转为列表字符串
    public static function Line2ListString($val, $delimiter = ',', $strict = true)
    {
        return self::Impode(self::ExplodeLine($val, $strict), $delimiter);
    }

    // 列表字符串转为文本行
    public static function ListString2Line($val, $delimiter = ',', $strict = true)
    {
        return self::Impode(self::Explode($val, $delimiter, $strict), "\n");
    }

    // 文本行转为KV数组
    public static function Line2KV($val, $delimiter = ':')
    {
        $rs = array();
        $rx = self::Split($val, "/[\r\n]/");
        foreach ($rx as $m) {
            $m = trim($m);
            if (!is_null($m) && !self::IsBlank($m)) {
                $n = preg_split("/[{$delimiter}]/", $m, 2);
                $k = $v = '';
                if (isset ($n [0])) {
                    $k = trim($n [0]);
                    if (!is_null($k) && !self::IsBlank($k)) {
                        if (isset ($n [1])) $v = trim($n [1]);
                        $rs [] = array('k' => $k, 'v' => $v);
                    }
                }
            }
        }
        return $rs;
    }

    // KV数组转为文本行
    public static function KV2Line($val, $delimiter = ':')
    {
        $s = '';
        foreach ($val as $m) {
            $k = str_replace($delimiter, '', $m ['k']);
            $v = $m ['v'];
            $s .= $k . $delimiter . $v . "\n";
        }
        return $s;
    }

    // 清理文本行KV
    public static function CleanLineKV($val, $delimiter = ':')
    {
        return self::KV2Line(self::Line2KV($val, $delimiter), $delimiter);
    }

    // 将一个字符串添加到一个列表字符串中，去除重复
    public static function AppendListString($val, $str, $delimiter = ',', $strict = true)
    {
        $rs = self::Explode($val, $delimiter, $strict);
        $exist = false;

        if (!empty ($rs) && !empty ($str)) {
            foreach ($rs as $v) {
                if ($v == $str) $exist = true;
            }
        }

        if (!$exist && !empty ($str)) $rs [] = $str;

        if (!empty ($rs)) return self::Impode($rs, $delimiter);
        return '';
    }

    /**
     * 此函数调整后再用
     *
     * @deprecated
     *
     *
     *
     *
     */
    public static function GetLastFromList($val, $exclude = null)
    {
        $p = strrpos($val, ',');

        if ($p !== false) {
            $l = explode(',', $val);

            for ($i = count($l); $i--;) {
                $e = trim(Url::Sanitize($l [$i]));

                if ($exclude === null || $e !== $exclude) {
                    return $e;
                }
            }
        }

        return trim(Url::Sanitize($val));
    }

    // 字节数以缩写格式显示
    public static function BytesSize($size)
    {
        $bytes = array('', 'K', 'M', 'G', 'T');
        foreach ($bytes as $val) {
            if ($size > 1024) {
                $size = $size / 1024;
            } else {
                break;
            }
        }
        return round($size, 1) . " " . $val;
    }

    // *** 校验函数 ***//
    //
    public static function IsInt($val)
    {
        return self::_REG($val, '/^[-+]?[0-9]+$/');
    }

    public static function IsNumeric($val)
    {
        return is_numeric($val);
    }

    public static function IsDecimal($val, $places = null)
    {
        if (is_null($places)) {
            $regex = '/^[-+]?[0-9]*\\.{1}[0-9]+(?:[eE][-+]?[0-9]+)?$/';
        } else {
            $regex = '/^[-+]?[0-9]*\\.{1}[0-9]{' . $places . '}$/';
        }

        return self::_REG($val, $regex);
    }

    public static function IsFloat($val, $prefix = 16, $places = 4)
    {
        $regex = '/^[0-9]{1,' . $prefix . '}(\\.{1}[0-9]{1,' . $places . '})?$/';
        return self::_REG($val, $regex);
    }

    public static function IsPrice($val, $prefix = 16, $places = 4)
    {
        $regex = '/^[-+]?[0-9]{1,' . $prefix . '}(\\.{1}[0-9]{1,' . $places . '})?$/';
        return self::_REG($val, $regex);
    }

    public static function IsRate($val, $prefix = 3, $places = 2)
    {
        $regex = '/^[-+]?[0-9]{1,' . $prefix . '}(\\.{1}[0-9]{1,' . $places . '})?$/';
        return self::_REG($val, $regex);
    }

    public static function IsMoney($val, $symbolPosition = 'left')
    {
        $money = '(?!0,?\d)(?:\d{1,3}(?:([, .])\d{3})?(?:\1\d{3})*|(?:\d+))((?!\1)[,.]\d{2})?';

        if ($symbolPosition == 'right') {
            $regex = '/^' . $money . '(?<!\x{00a2})\p{Sc}?$/u';
        } else {
            $regex = '/^(?!\x{00a2})\p{Sc}?' . $money . '$/u';
        }

        return self::_REG($val, $regex);
    }

    public static function IsAlphaNumeric($val)
    {
        if (is_array($val)) {
            // extract ( self::_defaults ( $val ) );
            return false;
        }

        if (empty ($val) && $val != '0') {
            return false;
        }
        return self::_REG($val, '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/mu');
    }

    public static function IsBlank($val)
    {
        return !self::_REG($val, '/[^\\s]/');
    }

    public static function IsNotEmpty($val)
    {
        if (empty ($val) && $val != '0') return false;
        return self::_REG($val, '/[^\s]+/m');
    }

    public static function IsNotEmptyArray($val)
    {
        if (is_array($val) && count($val) > 0) return !self::IsBlankArray($val);

        return false;
    }

    public static function IsBlankArray($val)
    {
        foreach ($val as $v) {
            if (is_array($v)) return self::IsBlankArray($v);
            else if (self::_REG($v, '/[^\\s]/')) return false;
        }

        return true;
    }

    public static function IsUUID($val)
    {
        return self::_REG($val, '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i');
    }

    public static function IsURL($val)
    {
        return self::_REG($val, '/^https?:\/\/[^\s]+$/i');
    }

    public static function IsEmail($val)
    {
        return self::_REG($val, '/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i');
    }

    public static function IsMobile($val)
    {
        return self::_REG($val, '/^(?:13|14|15|17|18)\d{9}$/');
    }

    public static function IsPhone($val)
    {
        return self::_REG($val, '/^((\d{2,4}-)?\d{2,4}-)?\d{7,8}(-\d{2,4})?$/');
    }

    // 分机号
    public static function IsExtPhone($val)
    {
        return self::_REG($val, '/^\d{3,4}$/');
    }

    public static function IsIdcard($val)
    {
        return self::_REG('/(^d{15}$)|(^d{18}$)|(^d{17}(d|X|x)$)/');
    }

    public static function IsPostal($val)
    {
        return self::_REG($val, '/^\d{6}$/');
    }

    public static function IsChinese($val, $utf8 = true)
    {
        return $utf8 ? self::IsChineseUTF8($val) : self::IsChineseGBK($val);
    }

    public static function IsChineseUTF8($val)
    {
        return self::_REG($val, '/^[\x{4e00}-\x{9fa5}]+$/u');
    }

    public static function IsChineseGBK($val)
    {
        return self::_REG($val, '/^([\x80-\xff][\x80-\xff])+$/');
    }

    public static function IsUsername($val)
    {
        return self::_REG($val, '/^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){3,19}$/');
    }

    public static function IsPassword($val)
    {
        return self::_REG($val, '/^[\w\$#@%*&\(\)\{\}\[\]\?\<\>\.\!]{6,20}$/');
    }

    public static function IsSmsAuthCode($val)
    {
        return self::_REG($val, '/^\d{6}$/');
    }

    public static function IsMD5($val)
    {
        return self::_REG($val, '/^[a-zA-Z0-9]{32}$/');
    }

    public static function IsHostPath($val)
    {
        return self::_REG($val, '!^[a-z|0-9~/\.:@]+$!i');
    }

    public static function IsIP($val)
    {
        // return self::_REG ( $val, '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/' );
        $n = ip2long($val);
        return ($n == -1 || $n == false) ? false : true;
    }

    public static function IsBetweenLen($val, $min = 1, $max = 20, $encode = 'utf8')
    {
        return (mb_strlen($val, $encode) >= $min && mb_strlen($val, $encode) <= $max) ? true : false;
    }

    public static function IsMaxLen($val, $len = 20, $encode = 'utf8')
    {
        return mb_strlen($val, $encode) > $len ? false : true;
    }

    public static function MaxVal($val, $limit = 0)
    {
        return $val > $limit ? false : true;
    }

    public static function MinVal($val, $limit = 0)
    {
        return $val < $limit ? false : true;
    }

    public static function Bool($val)
    {
        return $val ? true : false;
    }

    public static function Bool2Int($val)
    {
        return $val ? 1 : 0;
    }

    public static function Bool2String($val)
    {
        return $val ? 'true' : 'false';
    }

    protected static function _REG($val, $regex)
    {
        return preg_match($regex, $val) ? true : false;
    }
}

?>