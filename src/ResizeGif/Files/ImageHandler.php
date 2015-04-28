<?php
/**
 * Created by PhpStorm.
 * User: grandt
 * Date: 24-04-2015
 * Time: 15:19
 */

namespace grandt\ResizeGif\Files;

use Imagick;

/**
 * Use either the GD or ImageMagic libraries to resize a single frame gif file.
 *
 * License: GNU LGPL 2.1.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015 A. Grandt
 * @license   GNU LGPL 2.1
 * @version   1.0.0
 */
class ImageHandler {

    /**
     * @param string $srcFile
     * @param string $dstFile
     * @param float $ratio
     * @return string
     */
    public static function resizeGif($srcFile, $dstFile, $ratio) {
        $isGdInstalled = (extension_loaded('gd') || extension_loaded('gd2')) && function_exists('gd_info');
        if ($isGdInstalled) {
            return self::resizeGifGD($srcFile, $dstFile, $ratio);
        }
        if (extension_loaded('imagick')) {
            return self::resizeGifIM($srcFile, $dstFile, $ratio);
        }
        return FALSE;
    }

    /**
     * @param string $srcFile
     * @param string $dstFile
     * @param float $ratio
     * @return string
     */
    public static function resizeGifGD($srcFile, $dstFile, $ratio) {

        list($originalWidth, $originalHeight) = getimagesize($srcFile);

        $newWidth = (int)round($originalWidth * $ratio);
        $newHeight = (int)round($originalHeight * $ratio);

        $image_o = imagecreatefromgif($srcFile);
        $image_p = imagecreatetruecolor($newWidth, $newHeight);
        imagetruecolortopalette($image_p, false, 256);

        $tcIdx = imagecolortransparent($image_o);

        $color = FALSE;

        if ($tcIdx != -1) {
            $color = @imagecolorsforindex($image_o, $tcIdx);
        }
        if ($color !== FALSE) {
            $transparent = imagecolorallocatealpha($image_p, $color['red'], $color['green'], $color['blue'], 127);
            imagefill($image_p, 0, 0, $transparent);
            imagecolortransparent($image_p, $transparent);
        }

        imagealphablending($image_p, true);
        imagesavealpha($image_p, true);

        imagealphablending($image_o, true);

        imagecopyresampled($image_p, $image_o, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        $cleanDestFile = false;
        if ($dstFile == null) {
            $cleanDestFile = true;
            $dstFile = tempnam("Beware", "rsg");
        }
        imagegif($image_p, $dstFile);

        imagedestroy($image_o);
        imagedestroy($image_p);

        $imgData = file_get_contents($dstFile);

        if ($cleanDestFile) {
            unset($dstFile);
        }

        return $imgData;
    }

    /**
     * @param string $srcFile
     * @param string $dstFile
     * @param float $ratio
     * @return string
     */
    public static function resizeGifIM($srcFile, $dstFile, $ratio) {

        $image = new Imagick($srcFile);

        $d = $image->getImageGeometry();
        $originalWidth = $d['width'];
        $originalHeight = $d['height'];

        $newWidth = (int)round($originalWidth * $ratio);
        $newHeight = (int)round($originalHeight * $ratio);

        $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $image->writeImage($dstFile);
        $image->destroy();

        return file_get_contents($dstFile);
    }
}
