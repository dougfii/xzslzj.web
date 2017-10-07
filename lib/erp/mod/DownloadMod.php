<?php
class DownloadMod implements IMod
{

	public function index()
	{
	}

	public function UserFileDownload()
	{
		$id = UUID::Check ( Url::Req ( 'id', '', 'str' ) );
		if (empty ( $id ))
		{
			echo ALERT_ERROR;
			return;
		}
		
		// $file = $_SERVER ['DOCUMENT_ROOT'] . '/' . $path.$filename;
		// $file = iconv ( 'utf-8', 'gbk', $file );
		
		$path = UserFileCls::Instance ()->GetPath ( $id );
		$name = UserFileCls::Instance ()->GetName ( $id );
		$filename = UserFileCls::Instance ()->GetFileName ( $id );
		$savefile = $_SERVER ['DOCUMENT_ROOT'] . '/' . $path . $filename;
		
		if (! file_exists ( $savefile ))
		{ // 检查文件是否存在
			echo "文件找不到或已被删除";
			exit ();
		}
		else
		{
			$file = fopen ( $savefile, "r" );
			// 输入文件标签
			Header ( "Content-type: application/octet-stream" );
			Header ( "Accept-Ranges: bytes" );
			Header ( "Accept-Length: " . filesize ( $savefile ) );
			
			$ua = $_SERVER ["HTTP_USER_AGENT"];
			
			// $name = "中文 文件名.txt";
			$encoded_filename = str_replace ( "+", "%20", urlencode ( $name ) );
			
			header ( 'Content-Type: application/octet-stream' );
			
			if (preg_match ( "/MSIE/", $ua ))
			{
				header ( 'Content-Disposition: attachment; filename="' . $encoded_filename . '"' );
			}
			else if (preg_match ( "/Firefox/", $ua ))
			{
				header ( 'Content-Disposition: attachment; filename*="utf8\'\'' . $name . '"' );
			}
			else
			{
				header ( 'Content-Disposition: attachment; filename="' . $name . '"' );
			}
			
			ob_clean ();
			flush ();
			// 输出文件内容
			echo fread ( $file, filesize ( $savefile ) );
			fclose ( $file );
			exit ();
		}
	}
}
?>