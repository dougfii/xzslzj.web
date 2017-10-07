<?php

class Image
{
    // GD库1.6.2版以前支持GIF格式，但因GIF格式使用LZW演算法牵涉专利权，因此在GD1.6.2版之后不支持GIF的格式。如果你是WINDOWS的环境，你只要进入PHP.INI文件找到extension=php_gd2.dll，将#去除，重启APACHE即可，如果你是Linux环境，又想支持GIF，PNG，JPEG，你需要去下载libpng，zlib，以及freetype字体并安装。
    public static $SUPPORT_IMAGE_FORMAT = array('jpg', 'jpeg', 'png', 'gif');

    // 图片重设尺寸, 支持 jpg,gif,png
    function Resize($im, $w, $h, $filename, $ext)
    {
        if (!is_null($im) && !in_array($ext, self::$SUPPORT_IMAGE_FORMAT)) return false;

        $filename .= '.' . $ext;
        $newim = false;

        try {
            $sw = imagesx($im);
            $sh = imagesy($im);

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($w, $h);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
            } else {
                $newim = imagecreate($w, $h);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
            }

            switch ($ext) {
                case 'png' :
                    imagepng($newim, $filename, 0);
                    break;

                case 'gif' :
                    imagegif($newim, $filename);
                    break;

                default :
                    imagejpeg($newim, $filename, 100);
                    break;
            }

            imagedestroy($newim);

            return true;
        } catch (Exception $e) {
            if ($newim !== false) imagedestroy($newim);
        }

        return false;
    }

    // 图片缩放, 支持 jpg,gif,png
    // direct: true:以宽度为基准缩放, false:以高度为基准缩放
    function Zoom($im, $maxw, $maxh, $filename, $ext, $direct = true)
    {
        try {
            $sw = imagesx($im);
            $sh = imagesy($im);

            if (($maxw && $sw > $maxw) || ($maxh && $sh > $maxh)) {
                if ($maxw && $sw > $maxw) {
                    $ratiow = $maxw / $sw;
                    $resizew = true;
                }

                if ($maxh && $sh > $maxh) {
                    $ratioh = $maxh / $sh;
                    $resizeh = true;
                }

                if ($resizew && $resizeh) {
                    if ($direct) {
                        // 根据宽度缩放
                        if ($ratiow < $ratioh) $ratio = $ratiow;
                        else $ratio = $ratioh;
                    } else {
                        // 根据高度缩放
                        if ($ratiow > $ratioh) $ratio = $ratiow;
                        else $ratio = $ratioh;
                    }
                }

                if ($resizew && !$resizeh) $ratio = $ratiow;
                if ($resizeh && !$resizew) $ratio = $ratioh;

                $w = $sw * $ratio;
                $h = $sh * $ratio;
            } else {
                $w = $sw;
                $h = $sh;
            }

            return $this->resize($im, $w, $h, $filename, $ext);
        } catch (Exception $e) {
        }

        return false;
    }

    public function CreateFromBase64Str($base64str)
    {
        // 过滤图像BASE64编码前缀（不一定有）
        foreach (Image::$SUPPORT_IMAGE_FORMAT as $v) {
            $base64str = str_replace("data:image/{$v};base64,", '', $base64str);
        }
        // 解码
        return imagecreatefromstring(base64_decode($base64str));
    }
}