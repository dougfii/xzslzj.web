<?php
class MBlog
{
	const PATTERN_KEYWORD = '/\#([^\#|.]+)\#/';
	const PATTERN_USER = '/\@([a-zA-z0-9_]+)/';

	public static function ExtractKeyword($content)
	{
		preg_match_all ( self::GetKeywordPattern (), $content, $arr );
		return $arr [1];
	}

	public static function ExtractUser($content)
	{
		preg_match_all ( self::GetUserPattern (), $content, $arr );
		return $arr [1];
	}

	public static function ReplaceKeyword($content, $replace)
	{
        // $replace = '<a href="http://twitter.com/search?q=#${1}">#${1}#</a>';
        //return preg_replace ( self::GetKeywordPattern (), $replace, $content );
        return preg_replace_callback(self::GetKeywordPattern(), function () use ($replace) {
            return $replace;
        }, $content);
	}

	public static function ReplaceUser($content, $replace)
	{
        // $replace = '<a href="http://twitter.com/${1}">@${1}</a>';
        //return preg_replace ( self::GetUserPattern (), $replace, $content );
        return preg_replace_callback(self::GetUserPattern(), function () use ($replace) {
            return $replace;
        }, $content);
	}

	private static function GetKeywordPattern()
	{
		return self::PATTERN_KEYWORD;
	}

	private static function GetUserPattern()
	{
		$names = UserCls::Instance ()->NameItems ();
		if (empty ( $names ))
		{
			return self::PATTERN_USER;
		}
		else
		{
			$names = implode ( '|', $names );
			return '/\@(' . $names . ')/';
		}
	}
}
?>