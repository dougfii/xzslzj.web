<?php
class Template
{
	protected $vars = array ();
	protected $path;
	protected $file;
	
	public function __construct($file = null)
	{
		$this->setFile ( $file );
	}
	
	public function setFile($file = null)
	{
		$this->file = $this->path . $file;
	}
	
	public function setPath($path = null)
	{
		$this->path = $path;
	}
	
	public function set($key, $val)
	{
		$this->vars [$key] = (is_object ( $val ) && $val instanceof $this) ? $val->fetch () : $val;
	}
	
	public function fetch($file = null)
	{
		if ($file)
			$file = $this->path . $file;
		else
			$file = $this->file;
		if (! file_exists ( $this->file ))
			new Exception ( "Template file $this->file not exist" );
		
		extract ( $this->vars );
		ob_start ();
		include ($file);
		$contents = ob_get_contents ();
		ob_end_clean ();
		return $contents;
	}

}

/*
class CachedTemplate extends Template
{
	var $cache_id;
	var $expire;
	var $cached;
	
	function CachedTemplate($cache_id = null, $expire = 900)
	{
		$this->Template ();
		$this->cache_id = $cache_id ? 'cache/' . md5 ( $cache_id ) : $cache_id;
		$this->expire = $expire;
	}
	
	function is_cached()
	{
		if ($this->cached) return true;
		
		if (! $this->cache_id) return false;
		
		if (! file_exists ( $this->cache_id )) return false;
		
		if (! ($mtime = filemtime ( $this->cache_id ))) return false;
		
		if (($mtime + $this->expire) < time ())
		{
			@unlink ( $this->cache_id );
			return false;
		}
		else
		{
			$this->cached = true;
			return true;
		}
	}
	
	function fetch_cache($file)
	{
		if ($this->is_cached ())
		{
			$fp = @fopen ( $this->cache_id, 'r' );
			$contents = fread ( $fp, filesize ( $this->cache_id ) );
			fclose ( $fp );
			return $contents;
		}
		else
		{
			$contents = $this->fetch ( $file );
			
			if ($fp = @fopen ( $this->cache_id, 'w' ))
			{
				fwrite ( $fp, $contents );
				fclose ( $fp );
			}
			else
			{
				die ( 'Unable to write cache.' );
			}
			
			return $contents;
		}
	}
}
*/
?>