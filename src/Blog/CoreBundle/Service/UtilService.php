<?php

namespace Blog\CoreBundle\Service;

class UtilService
{
    function __construct()
    {

    }

    public function normalizePath($path)
    {
        $parts = array();// Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
        $segments = explode('/', $path);// Collect path segments
        $test = '';// Initialize testing variable
        foreach($segments as $segment)
        {
            if($segment != '.')
            {
                $test = array_pop($parts);
                if(is_null($test))
                    $parts[] = $segment;
                else if($segment == '..')
                {
                    if($test == '..')
                        $parts[] = $test;

                    if($test == '..' || $test == '')
                        $parts[] = $segment;
                }
                else
                {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }
        return implode('/', $parts);
    }

    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function createScaledImage($fileName, $options)
    {
        if (!isset($options['upload_dir'])) {
            return false;
        }

        $filePath = $options['upload_dir'] . $fileName;
        $newFilePath = $options['upload_dir'] . $fileName;

        list($imgWidth, $imgHeight) = @getimagesize($filePath);
        if (!$imgWidth || !$imgHeight) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $imgWidth,
            $options['max_height'] / $imgHeight
        );
        if ($scale >= 1) {
            if ($filePath !== $newFilePath) {
                return copy($filePath, $newFilePath);
            }
            return true;
        }
        $newWidth = $imgWidth * $scale;
        $newHeight = $imgHeight * $scale;
        $newImg = @imagecreatetruecolor($newWidth, $newHeight);
        $imageQuality = null;
        $writeImage = null;
        switch (strtolower(substr(strrchr($fileName, '.'), 1)))
        {
            case 'jpg':
            case 'jpeg':
                $srcImg = @imagecreatefromjpeg($filePath);
                $writeImage = 'imagejpeg';
                $imageQuality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($newImg, @imagecolorallocate($newImg, 0, 0, 0));
                $srcImg = @imagecreatefromgif($filePath);
                $writeImage = 'imagegif';
                break;
            case 'png':
                @imagecolortransparent($newImg, @imagecolorallocate($newImg, 0, 0, 0));
                @imagealphablending($newImg, false);
                @imagesavealpha($newImg, true);
                $srcImg = @imagecreatefrompng($filePath);
                $writeImage = 'imagepng';
                $imageQuality = isset($options['png_quality']) ? $options['png_quality'] : 9;
                break;
            default:
                $srcImg = null;
        }
        $success = $srcImg && @imagecopyresampled(
                $newImg,
                $srcImg,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $imgWidth,
                $imgHeight
            ) && $writeImage($newImg, $newFilePath, $imageQuality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($srcImg);
        @imagedestroy($newImg);

        return $success;
    }
}