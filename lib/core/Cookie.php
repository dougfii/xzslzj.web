<?php
class Cookie
{
	const MAX_COOKIE_SIZE = 1024;
	const VALUE_SEPARATOR = ':';
	protected $name = null;
	protected $expire = null;
	protected $path = '';
	protected $domain = '';
	protected $secure = false;
	protected $httponly = false;
	protected $value = array ();

	public function __construct($name, $expire = null, $path = null, $keyStore = false)
	{
		$this->name = $name;
		$this->path = $path;
		$this->expire = $expire;
		if (is_null ( $expire ) || ! is_numeric ( $expire ) || $expire < 0)
		{
			$this->expire = $this->GetDefaultExpire ();
		}
		
		$this->keyStore = $keyStore;
		if ($this->Found ())
		{
			$this->LoadContentFromCookie ();
		}
	}

	public function Found()
	{
		return isset ( $_COOKIE [$this->name] );
	}

	protected function GetDefaultExpire()
	{
		return time () + 86400 * 365 * 2;
	}

	protected function SetCookie($name, $value, $expires, $path = '', $domain = '', $secure = false, $httponly = false)
	{
		if (! empty ( $domain ))
		{
			if (! strncasecmp ( $domain, 'www.', 4 ))
			{
				$domain = substr ( $domain, 4 );
			}
			$domain = '.' . $domain;
			
			$port = strpos ( $domain, ':' );
			if ($port !== false)
			{
				$domain = substr ( $domain, 0, $port );
			}
		}
		
		$header = 'Set-Cookie: ' . rawurlencode ( $name ) . '=' . rawurlencode ( $value ) . (empty ( $expires ) ? '' : '; expires=' . gmdate ( 'D, d-M-Y H:i:s', $expires ) . ' GMT') . (empty ( $path ) ? '' : '; path=' . $path) . (empty ( $domain ) ? '' : '; domain=' . $domain) . (! $secure ? '' : '; secure') . (! $httponly ? '' : '; HttpOnly');
		
		header ( $header, false );
	}

	protected function SetP3PHeader()
	{
		header ( "P3P: CP='OTI DSP COR NID STP UNI OTPa OUR'" );
	}

	public function Delete()
	{
		$this->SetP3PHeader ();
		setcookie ( $this->name, false, time () - 86400 );
	}

	public function Save()
	{
		$cookieString = $this->MakeContentString ();
		if (strlen ( $cookieString ) > self::MAX_COOKIE_SIZE)
		{
			// If the cookie was going to be too large, instead, delete existing cookie and start afresh
			// This will result in slightly less accuracy in the case
			// where someone visits more than dozen websites tracked by the same Piwik
			// This will usually be the Piwik super user itself checking all his websites regularly
			$this->Delete ();
			return;
		}
		
		$this->SetP3PHeader ();
		$this->SetCookie ( $this->name, $cookieString, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly );
	}

	/**
	 * Extract signed content from string: content VALUE_SEPARATOR '_=' signature
	 *
	 * @param string $content        	
	 * @return string false or false if unsigned
	 */
	private function ExtractSignedContent($content)
	{
		$signature = substr ( $content, - 40 );
		if (substr ( $content, - 43, 3 ) == self::VALUE_SEPARATOR . '_=' && $signature == sha1 ( substr ( $content, 0, - 40 ) ))
		{
			return substr ( $content, 0, - 43 );
		}
		return false;
	}

	/**
	 * Load the cookie content into a php array.
	 * Parses the cookie string to extract the different variables.
	 * Unserialize the array when necessary.
	 * Decode the non numeric values that were base64 encoded.
	 */
	protected function LoadContentFromCookie()
	{
		$cookieStr = $this->ExtractSignedContent ( $_COOKIE [$this->name] );
		if ($cookieStr === false)
		{
			return;
		}
		
		$values = explode ( self::VALUE_SEPARATOR, $cookieStr );
		foreach ( $values as $nameValue )
		{
			$equalPos = strpos ( $nameValue, '=' );
			$varName = substr ( $nameValue, 0, $equalPos );
			$varValue = substr ( $nameValue, $equalPos + 1 );
			
			if (! is_numeric ( $varValue ))
			{
				$varValue = @json_decode ( base64_decode ( $varValue ), $assoc = true );
			}
			
			$this->value [$varName] = $varValue;
		}
	}

	protected function MakeContentString()
	{
		$cookieStr = '';
		foreach ( $this->value as $k => $v )
		{
			if (! is_numeric ( $v ))
			{
				$v = base64_encode ( json_encode ( $v ) );
			}
			
			$cookieStr .= "$k=$v" . self::VALUE_SEPARATOR;
		}
		
		if (! empty ( $cookieStr ))
		{
			$cookieStr .= '_=';
			
			$signature = sha1 ( $cookieStr );
			return $cookieStr . $signature;
		}
		
		return '';
	}

	public function SetDomain($domain)
	{
		$this->domain = $domain;
	}

	public function SetSecure($secure)
	{
		$this->secure = $secure;
	}

	public function SetHttpOnly($httponly)
	{
		$this->httponly = $httponly;
	}

	public function Set($name, $value)
	{
		$name = self::Escape ( $name );
		
		// Delete value if $value === null
		if (is_null ( $value ))
		{
			if ($this->keyStore === false)
			{
				unset ( $this->value [$name] );
				return;
			}
			unset ( $this->value [$this->keyStore] [$name] );
			return;
		}
		
		if ($this->keyStore === false)
		{
			$this->value [$name] = $value;
			return;
		}
		$this->value [$this->keyStore] [$name] = $value;
	}

	public function Get($name)
	{
		$name = self::Escape ( $name );
		if ($this->keyStore === false)
		{
			return isset ( $this->value [$name] ) ? self::Escape ( $this->value [$name] ) : false;
		}
		return isset ( $this->value [$this->keyStore] [$name] ) ? self::Escape ( $this->value [$this->keyStore] [$name] ) : false;
	}

	protected static function Escape($val)
	{
		return Url::Sanitizes ( $val );
	}

	public function __toString()
	{
		$str = 'COOKIE ' . $this->name . ', rows count: ' . count ( $this->value ) . ', cookie size = ' . strlen ( $this->MakeContentString () ) . ' bytes<br/>';
		$str .= var_export ( $this->value, $return = true );
		return $str;
	}
}
?>