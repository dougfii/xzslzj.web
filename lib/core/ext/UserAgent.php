<?php

class UserAgent
{

	public static function getLanguage($lang = null)
	{
		static $fix = array ( '/(\\\\.)/' , '/(\s+)/' , '/(\([^)]*\))/' , '/(;q=[0-9.]+)/' , '/\.(.*)/' , '/^C$/' );
		// extraneous bits of RFC 3282 that we ignore
		// quoted-pairs
		// CFWS white space
		// CFWS comments
		// quality
		// found in the LANG environment variable
		// charset (e.g., en_CA.UTF-8)
		// POSIX 'C' locale
		
		if (is_null ( $lang ) || empty ( $lang )) $lang = Url::Sanitize ( @$_SERVER ['HTTP_ACCEPT_LANGUAGE'] );
		
		if (is_null ( $lang ))
		{
			$lang = '';
		}
		else
		{
			// language tags are case-insensitive per HTTP/1.1 s3.10 but the
			// region may be capitalized per ISO3166-1;
			// underscores are not permitted per RFC 4646 or 4647 (which
			// obsolete RFC 1766 and 3066),
			// but we guard against a bad user agent which naively uses its
			// locale
			$lang = strtolower ( str_replace ( '_', '-', $lang ) );

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
?>