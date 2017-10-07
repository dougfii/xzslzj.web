<?php

class Cache
{
    protected $expire = 0;
    protected $path;

    function __construct($expire = 0)
    {
        $this->expire = $expire;
        $this->path = BASE_SYS_PATH . 'cache/';
    }

    function GetFilename($id = 'system', $file = 'cache')
    {
        return $this->path . $id . '/' . $file . '.php';
    }

    function Get($id = 'system', $file = 'cache')
    {
        $cache_complete = false;
        $content = '';

        $filename = $this->GetFilename($id, $file);
        if (file_exists($filename))
        {
            $t = IO::GetMTime($filename);
            if ($t === false || ($t > 0 && (time() - $t) > $this->expire))
            {
                $this->Delete($id, $file);
                return false;
            }


            $ok = @include($this->path . $id . '/' . $file . '.php');

            if ($ok && $cache_complete == true)
            {
                return $content;
            }
        }

        return false;
    }

    function Set($content, $id = 'system', $file = 'cache')
    {
        IO::MakeDir($this->path . $id . '/');

        if (!is_writable($this->path . $id . '/'))
        {
            return false;
        }

        $id = $this->path . $id . '/' . $file . '.php';

        $s = "<" . "?php\n\n";
        $s .= "$" . "content   = " . var_export($content, true) . ";\n\n";
        $s .= "$" . "cache_complete   = true;\n\n";
        $s .= "?" . ">";

        // $tmp = tempnam ( $this->path . $id . '/', 'tmp_' );
        $tmp = tempnam(__FILE__, 'tmp_');
        $fp = @fopen($tmp, 'wb');

        if ($fp)
        {
            @fwrite($fp, $s, strlen($s));
            @fclose($fp);

            if (!@rename($tmp, $id))
            {
                @unlink($id);
                if (!@rename($tmp, $id))
                {
                    @unlink($tmp);
                }
            }
            return true;
        }
        return false;
    }

    function Delete($id = 'system', $file = 'cache')
    {
        $filename = $this->path . $id . '/' . $file . '.php';
        if (file_exists($filename))
        {
            @unlink($filename);
            return true;
        }
        return false;
    }

    function Remove($id = 'system')
    {
        IO::RemoveDir($this->path . $id . '/', $deleteRootToo = false);
    }

    function RemoveAll()
    {
        IO::RemoveDir($this->path, $delroot = false);
    }
}

?>