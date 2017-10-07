<?php
class IO
{

	public static function MakeDir($path, $mode = 0755, $denyAccess = true)
	{
		if (! is_dir ( $path ))
		{
			@mkdir ( $path, $mode, $recursive = true );
		}

		if ($denyAccess)
		{
			self::CreateHtaccessFile ( $path );
		}
	}

	public static function RemoveDir($dir, $delroot)
	{
		if (! $dh = @opendir ( $dir ))
		{
			return;
		}
		while ( false !== ($obj = readdir ( $dh )) )
		{
			if ($obj == '.' || $obj == '..')
			{
				continue;
			}

			if (! @unlink ( $dir . DIRECTORY_SEPARATOR . $obj ))
			{
				self::RemoveDir ( $dir . DIRECTORY_SEPARATOR . $obj, true );
			}
		}
		closedir ( $dh );
		if ($delroot)
		{
			@rmdir ( $dir );
		}
		return;
	}
	// 创建文件
	public static function CreateFile($path, $file, $content)
	{
		if (! is_writable ( $path )) return false;

		$file = $path . $file;

		$tmp = tempnam ( __FILE__, 'tmp_' );
		$fp = @fopen ( $tmp, 'wb' );

		if ($fp)
		{
			@fwrite ( $fp, $content, strlen ( $content ) );
			@fclose ( $fp );

			if (! @rename ( $tmp, $file ))
			{
				@unlink ( $file );
				if (! @rename ( $tmp, $file ))
				{
					@unlink ( $tmp );
				}
			}
			return true;
		}
		return false;
	}

	// 删除文件
	public static function DeleteFile($file)
	{
		if (file_exists ( $file )) return @unlink ( $file );
		return false;
	}

	// 得到文件基本名称
	public static function GetFileBaseName($filename)
	{
        //return preg_replace ( '/^.+[\\\\\\/]/', '', $filename );
        return preg_replace_callback('/^.+[\\\\\\/]/', function () {
            return '';
        }, $filename);
	}

	// 得到文件名称（不带扩展名）
	public static function GetFileName($filename)
	{
		$exts = explode ( '.', $filename );
		if (count ( $exts ) > 1) return implode ( '.', array_slice ( $exts, 0, count ( $exts ) - 1 ) );
		return $filename;
	}

	// 得到文件扩展名
	public static function GetFileExtension($filename)
	{
		$exts = explode ( '.', $filename );
		if (count ( $exts ) > 1)
		{
			$count = count ( $exts ) - 1;
			return $exts [$count];
		}
		return '';
	}

	public static function MakeFileName($name, $extension)
	{
		if (empty ( $extension )) return $name;
		return $name . '.' . $extension;
	}

	public static function CreateHtaccessFile($path, $content = "<Files \"*\">\nDeny from all\n</Files>\n")
	{
		@file_put_contents ( $path . DIRECTORY_SEPARATOR . '.htaccess', $content );
	}

	/*
	 * GetFilenameList 目录里的文件列表 Paraments: $dir: 起始目录 $recursive: 是否递归 $includeexts: 允许的扩展名列表(以竖线|分割) $exclusionexts: 排除的扩展名称列表(以竖线|分割)
	 */
	public static function GetFilenameList($dir, $recursive = true, $includeexts = null, $exclusionexts = null, $callback = null)
	{
		if (! is_null ( $includeexts ) && ! is_array ( $includeexts )) $includeexts = explode ( '|', $includeexts );
		if (! is_null ( $exclusionexts ) && ! is_array ( $exclusionexts )) $exclusionexts = explode ( '|', $exclusionexts );

		$files = array ();

		$dh = opendir ( $dir );

		while ( ($file = readdir ( $dh )) !== false )
		{
			$obj = $dir . DIRECTORY_SEPARATOR . $file;

			if ($file == '.' || $file == '..')
			{
				continue;
			}
			else if (is_dir ( $obj ) && $recursive)
			{
				// 如果是目录,进行递归
				$files = array_merge ( $files, self::GetFilenameList ( $obj, $recursive, $includeexts, $exclusionexts, $callback ) );
			}
			else
			{
				$f = $dir . DIRECTORY_SEPARATOR . $file;
				$x = strtolower ( self::GetFileExtension ( $f ) );

				if (! is_null ( $includeexts ))
				{
					$tmp = $f;
					foreach ( $includeexts as $ext )
					{
						if ($x == strtolower ( $ext ))
						{
							$tmp = null;
							break;
						}
					}
					if (! is_null ( $tmp )) $f = null;
				}

				if (! is_null ( $exclusionexts ) && ! is_null ( $f ))
				{
					foreach ( $exclusionexts as $ext )
					{
						if ($x == strtolower ( $ext ))
						{
							$f = null;
							break;
						}
					}
				}

				if (! is_null ( $f ))
				{
					// 如果是文件,直接输出
					$files [] = $f;

					if (! is_null ( $callback )) $callback ( $f );
				}
			}
		}

		return $files;
	}

	// 返回文件上次被修改的时间，出错时返回 FALSE。时间以 Unix 时间戳的方式返回。
	public static function GetMTime($filename)
	{
		return filemtime ( $filename );
	}

	// 返回文件上次 inode 被修改的时间，如果出错则返回 FALSE。时间以 Unix 时间戳的方式返回。
	public static function GetCTime($filename)
	{
		return filectime ( $filename );
	}
	// 返回文件上次被访问的时间，如果出错则返回 FALSE。时间以 Unix 时间戳的方式返回。
	public static function GetATime($filename)
	{
		return fileatime ( $filename );
	}
}
?>