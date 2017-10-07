<?php

class UA
{
    public static function GetUA()
    {
        return $_SERVER ['HTTP_USER_AGENT'];
    }

    public static function GetUAID()
    {
        return md5(self::GetUA());
    }

    public static function IsMobile()
    {
        // 正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = '/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|';

        $regex_match .= 'htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|';

        $regex_match .= 'blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|';

        $regex_match .= 'symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|';

        $regex_match .= 'jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220';

        $regex_match .= ')/i';

        return isset ($_SERVER ['HTTP_X_WAP_PROFILE']) or isset ($_SERVER ['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER ['HTTP_USER_AGENT'])); // 如果UA中存在上面的关键词则返回真。
    }

    public static function GetLanguage($lang = null)
    {
        static $fix = array('/(\\\\.)/', '/(\s+)/', '/(\([^)]*\))/', '/(;q=[0-9.]+)/', '/\.(.*)/', '/^C$/');
        // extraneous bits of RFC 3282 that we ignore
        // quoted-pairs
        // CFWS white space
        // CFWS comments
        // quality
        // found in the LANG environment variable
        // charset (e.g., en_CA.UTF-8)
        // POSIX 'C' locale

        if (is_null($lang) || empty ($lang)) $lang = Url::Sanitize(@$_SERVER ['HTTP_ACCEPT_LANGUAGE']);

        if (is_null($lang)) {
            $lang = '';
        } else {
            // language tags are case-insensitive per HTTP/1.1 s3.10 but the
            // region may be capitalized per ISO3166-1;
            // underscores are not permitted per RFC 4646 or 4647 (which
            // obsolete RFC 1766 and 3066),
            // but we guard against a bad user agent which naively uses its
            // locale
            $lang = strtolower(str_replace('_', '-', $lang));

            // filters
            //$lang = preg_replace ( $fix, '', $lang );
            $lang = preg_replace_callback($fix, function () {
                return '';
            }, $lang);

            //$lang = preg_replace ( '/((^|,)chrome:.*)/', '', $lang, 1 ); // Firefox
            $lang = preg_replace_callback('/((^|,)chrome:.*)/', function () {
                return '';
            }, $lang, 1);
            // bug
            //$lang = preg_replace ( '/(,)(?:en-securid,)|(?:(^|,)en-securid(,|$))/', '$1', $lang, 1 ); // unregistered
            // language
            // tag
            $lang = preg_replace_callback('/(,)(?:en-securid,)|(?:(^|,)en-securid(,|$))/', function () {
                return '$1';
            }, $lang, 1);

            $lang = str_replace ( 'sr-sp', 'sr-rs', $lang ); // unofficial
            // (proposed) code in the
            // wild
        }

        return $lang;
    }
}