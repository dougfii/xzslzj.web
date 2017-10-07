<?php
class ChineseSplite
{
	private static $specials = array ( '3m' );

    public static function Splite($str, $specials = array())
    {
        if ($str != '')
        {
            $str = Url::Unsanitize ( $str );
            $regex = '/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\'|\"|\`|\-|\=|\\\|\|/';
            //$str = preg_replace ( $regex, " ", $str );
            $str = preg_replace_callback($regex, function () {
                return ' ';
            }, $str);

            $words = self::SpliteChinese ( $str );
            $words = self::SpliteSpecials ( $words, $specials );

            return $words;
        }

        return array ();
    }

	private static function SpliteChinese($str)
	{
		ini_set ( 'display_errors', 'On' );
		ini_set ( 'memory_limit', '512M' );
		
		// 岐义处理
		$do_fork = true;
		// 新词识别
		$do_unit = true;
		// 多元切分
		$do_multi = true;
		// 是否预载全部词条
		$pri_dict = true;
		
		// 初始化类
		PhpAnalysis::$loadInit = false;
		$pa = new PhpAnalysis ( 'utf-8', 'utf-8', $pri_dict );
		
		// 重新编译词典
		// $sourcefile = dirname ( __FILE__ ) . '/dict/not-build/base_dic_full.txt';
		// $pa->MakeDict ( $sourcefile, dirname ( __FILE__ ) .'/dict/base_dic_full.dic' );
		
		// 载入词典
		$pa->LoadDict ();
		
		// 执行分词
		$pa->SetSource ( $str ); // 设置源字符串
		$pa->differMax = $do_multi; // 使用最大切分模式对二元词进行消岐
		$pa->unitWord = $do_unit; // 尝试合并单字
		$pa->StartAnalysis ( $do_fork ); // 开始执行分析
		
		$words = explode ( ' ', $pa->GetFinallyResult ( ' ', false ) );
		
		unset ( $pa );
		
		return $words;
	}

	private static function SpliteSpecials($words, $specials)
	{
		$specials = array_merge ( self::$specials, $specials );
		foreach ( $specials as $special )
		{
			foreach ( $words as $k => $v )
			{
				// 处理的字符+数字的，分开
				if (strtolower ( substr ( $v, stripos ( $v, $special ), strlen ( $special ) ) ) == $special && is_numeric ( substr ( $v, stripos ( $v, $special ) + strlen ( $special ), 1 ) ))
				{
					unset ( $words [$k] );
					// 追加处理字符前的字符
					array_push ( $words, substr ( $v, 0, stripos ( $v, $special ) ) );
					// 追加处理字符
					array_push ( $words, substr ( $v, stripos ( $v, $special ), strlen ( $special ) ) );
					// 追加处理字符后的字符
					array_push ( $words, substr ( $v, stripos ( $v, $special ) + strlen ( $special ) ) );
				}
				else if (substr ( $v, stripos ( $v, $special ) + strlen ( $special ), 1 ) == '')
				{
					unset ( $words [$k] );
					// 追加处理字符前的字符
					array_push ( $words, substr ( $v, 0, stripos ( $v, $special ) ) );
					// 追加处理字符
					array_push ( $words, substr ( $v, stripos ( $v, $special ), strlen ( $special ) ) );
				}
			}
		}
		//去除为空的数组元素
		foreach ($words as $m => $n)
		{
			if($n=='')  unset($words[$m]);				
		}
		return $words;
	}
	
	/*
	 * public static function Splite($str) { $rs = array (); if (! Util::IsBlank ( $str )) { $ignore = false; // 是否清除标点符号 $showa = false; // 是否标注词性 $duality = false; // 是否散字自动二元 $stats = true; // 是否看统计表 $multi = 0; // 是否复合分词? $multi |= intval($opt); $opt: 1:'最短词', 2:'二元', 4:'重要单字', 8:'全部单字' $xattr = '~v'; // 统计词性 ，多个用,分开 以~开头表示不包含 $limit = strlen ( $str ); // 只看统计前n个词，此处限制最大数为字符串长度 try { $cws = scws_new (); // $cws->set_charset ( 'utf8' ); // $cws->set_rule( '/usr/local/scws/etc/rules.utf8.ini'); // $cws->set_dict( '/usr/local/scws/etc/dict.utf8.xdb'); // $cws->set_duality ( $duality ); // $cws->set_ignore ( $ignore ); // $cws->set_multi ( $multi ); $cws->send_text ( $str ); // if ($stats == true) // { // // stats // printf ( "No. WordString Attr Weight(times)\n" ); // printf ( "-------------------------------------------------\n" ); // $list = $cws->get_tops ( $limit, $xattr ); // $cnt = 1; // settype ( $list, 'array' ); // foreach ( $list as $tmp ) // { // printf ( "%02d. %-24.24s %-4.2s %.2f(%d)\n", $cnt, $tmp ['word'], $tmp ['attr'], $tmp ['weight'], $tmp ['times'] ); // $cnt ++; // } // } // else // { // // segment // while ( $res = $cws->get_result () ) // { // foreach ( $res as $tmp ) // { // if ($tmp ['len'] == 1 && $tmp ['word'] == "\r") continue; // if ($tmp ['len'] == 1 && $tmp ['word'] == "\n") echo $tmp ['word']; // else if ($showa) printf ( "%s/%s ", $tmp ['word'], $tmp ['attr'] ); // else printf ( "%s ", $tmp ['word'] ); // } // flush (); // } // } $list = $cws->get_tops ( $limit, $xattr ); settype ( $list, 'array' ); foreach ( $list as $v ) { $rs [] = $v ['word']; } $cws->close (); } catch ( Exception $e ) { $cws->close (); } } return $rs; }
	 */
}
?>