<?php
class Archive
{
	protected $path;

	function __construct()
	{
		$this->path = BASE_SYS_PATH . 'archive/';
	}

	function Get($id = 'system', $file = 'archive')
	{
		$cache_complete = false;
		$content = '';
		
		$ok = @include ($this->path . $id . '/' . $file . '.php');
		
		if ($ok && $cache_complete == true)
		{
			return $content;
		}
		
		return false;
	}

	function Set($content, $id = 'system', $file = 'archive')
	{
		IO::MakeDir ( $this->path . $id . '/' );
		
		if (! is_writable ( $this->path . $id . '/' ))
		{
			return false;
		}
		
		$id = $this->path . $id . '/' . $file . '.php';
		
		$s = "<" . "?php\n\n";
		$s .= "$" . "content   = " . var_export ( $content, true ) . ";\n\n";
		$s .= "$" . "cache_complete   = true;\n\n";
		$s .= "?" . ">";
		
		// $tmp = tempnam ( $this->path . $id . '/', 'tmp_' );
		$tmp = tempnam ( __FILE__, 'tmp_' );
		$fp = @fopen ( $tmp, 'wb' );
		
		if ($fp)
		{
			@fwrite ( $fp, $s, strlen ( $s ) );
			@fclose ( $fp );
			
			if (! @rename ( $tmp, $id ))
			{
				@unlink ( $id );
				if (! @rename ( $tmp, $id ))
				{
					@unlink ( $tmp );
				}
			}
			return true;
		}
		return false;
	}

	function Delete($id = 'system', $file = 'archive')
	{
		$filename = $this->path . $id . '/' . $file . '.php';
		if (file_exists ( $filename ))
		{
			@unlink ( $filename );
			return true;
		}
		return false;
	}
}
?>