<?php
/**
 * @package     com_bt_media - BT Media
 * @version	1.3
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;

/**
 * * BtWaterMask
 */
if (!class_exists('BtMediaWaterMask')) {

    class BtMediaWaterMask {

        public static function getWaterMarkOptions() {
            $options = array();
            $options['padding'] = 4; // offset for positioning and appearance
            $options['font'] = dirname(__FILE__) . '/fonts/tahoma.ttf';
            $options['text'] = chr(169) . " Bowthemes"; // Message text for watermark
            $options['image'] = dirname(__FILE__) . '/watermark.png'; // Watermark image file name (include a path/URL, if necessary)
            $options['type'] = 'img'; // Default watermarking method/type
            $options['fcolor'] = 'ffffff'; // Default font color White
            $options['fsize'] = 11; // Default font size (in pixels for GD v1.x, in point size for GD v2.x+.  Use gdinfo.php to check your server.)
            $options['bg'] = 1;  // Fill background for message text watermarks flag (true/false)
            $options['bgcolor'] = '000000';
            $options['factor'] = 1; //Default watermark image overlay reduction factor (multiplier) - set to 1 to retain original size
            $options['position'] = 'br'; // Default watermark vertical position
            $options['opacity'] = 70; // Default watermark image overlay opacity
            $options['rotate'] = 0; // Default watermark image overlay rotation (in degrees)
            $options['size'] = 'large';
            $options['thumbnail_factor'] = 0.5;
            return $options;
        }

        /*         * * Base on PHP Image Watermark Script v2.3
         * *** By Richard L. Trethewey http://www.rainbodesign.com/pub/watermark/
         * ** */

        public static function createWaterMark($source, $options) {
            // Get info about image to be watermarked
            $old_image = $source;
            $size = getimagesize($old_image);
            $imagetype = $size[2];
            $usingAlpha = false;
            // Create GD Image Resource from original image
            switch ($imagetype) {
                case(1):
                    $image = imagecreatefromgif($old_image);
                    break;

                case(2):
                    $image = imagecreatefromjpeg($old_image);
                    break;

                case(3):
                    $image = imagecreatefrompng($old_image);
                    break;

                case(6):
                    $image = imagecreatefrombmp($old_image);
                    break;
            } // end switch($imagetype)

            if ($options['type'] == 'img') {
                $sizeWM = getimagesize($options['image']);
                switch ($sizeWM[2]) {
                    case(1):
                        $watermark = imagecreatefromgif($options['image']);
                        break;

                    case(2):
                        $watermark = imagecreatefromjpeg($options['image']);
                        break;

                    case(3):
                        $watermark = imagecreatefrompng($options['image']);
                        break;

                    case(6):
                        $watermark = imagecreatefrombmp($options['image']);
                        break;
                }
                //$watermark = imagecreatefromjpeg($options['image']);	// Create a GD image resource from overlay image
                $wm_width = imagesx($watermark);  // get the original width
                $wm_height = imagesy($watermark);  // get the original height

                if($options['size'] == 'large') {
                    $watermark_rWidth = $wm_width * $options['factor']; // calculate new, reduced width
                    $watermark_rHeight = $wm_height * $options['factor']; // calculate new, reduced height
                }else{
                    $watermark_rWidth = $wm_width * $options['thumbnail_factor']; // calculate new, reduced width
                    $watermark_rHeight = $wm_height * $options['thumbnail_factor']; // calculate new, reduced height
                }
                $watermark_r = @imagecreatetruecolor($watermark_rWidth, $watermark_rHeight);

                // Make transparent
                if ($sizeWM[2] == 3) {
                    $usingAlpha = true;
                    imagealphablending($watermark_r, false);
                    imagesavealpha($watermark_r, true);
                    $transparent = imagecolorallocatealpha($watermark_r, 255, 255, 255, 127);
                    imagefilledrectangle($watermark_r, 0, 0, $wm_width, $wm_height, $transparent);
                }
                // Make transparent
                if ($sizeWM[2]==3) {
                    $usingAlpha = true;
                    imagealphablending($watermark_r, false);
                    imagesavealpha($watermark_r,true);
                    $transparent = imagecolorallocatealpha($watermark_r, 255, 255, 255, 127);
                    imagefilledrectangle($watermark_r, 0, 0, $wm_width, $wm_height, $transparent);
                }
                imagecopyresampled($watermark_r, $watermark, 0, 0, 0, 0, $watermark_rWidth, $watermark_rHeight, $wm_width, $wm_height);
                imagedestroy($watermark);  // original watermark image no longer needed
            }
            if ($options['type'] == 'msg') {
                // Calculate size of overlay image for text
                // array imagettfbbox ( float $size , float $angle , string $fontfile , string $text )
                if($options['size'] == 'thumb'){
                    $options['fsize'] *= $options['thumbnail_factor'];

                }
                $wm_bBox = imagettfbbox($options['fsize'], 0, $options['font'], $options['text']);
                $minX = min(array($wm_bBox[0], $wm_bBox[2], $wm_bBox[4], $wm_bBox[6]));
                $maxX = max(array($wm_bBox[0], $wm_bBox[2], $wm_bBox[4], $wm_bBox[6]));
                $minY = min(array($wm_bBox[1], $wm_bBox[3], $wm_bBox[5], $wm_bBox[7]));
                $maxY = max(array($wm_bBox[1], $wm_bBox[3], $wm_bBox[5], $wm_bBox[7]));
                $thebox = array(
                    "left" => abs($minX) - 1,
                    "top" => abs($minY) - 1,
                    "width" => $maxX - $minX,
                    "height" => $maxY - $minY
                );
                //$watermark_rWidth = ($upperRightX - $upperLeftX)+$options['padding'];
                //$watermark_rHeight = ($lowerLeftY - $upperLeftY)+$options['padding'];
                if($options['size'] == 'large') {
                    $watermark_rWidth = ($thebox["width"]) + $options['padding'] * 2;
                    $watermark_rHeight = ($thebox["height"] ) + $options['padding'] * 2;
                }else{

                    $watermark_rWidth = ($thebox["width"] )  + $options['padding'] * 2;
                    $watermark_rHeight = ($thebox["height"] ) + $options['padding'] * 2;
                }
                // Create the overlay image
                $watermark_r = imagecreatetruecolor($watermark_rWidth, $watermark_rHeight);

                $userColor = self::hex2int(self::validHexColor($options['fcolor'], 'ffffff'));
                $txtColor = imageColorAllocate($watermark_r, $userColor['r'], $userColor['g'], $userColor['b']);

                if ($options['bg']) {
                    // Set an appropriate background color
                    $userColor = self::hex2int(self::validHexColor($options['bgcolor'], '000000'));
                    $watermark_rBGColor = imageColorAllocate($watermark_r, $userColor['r'], $userColor['g'], $userColor['b']);
                    imagefilledrectangle($watermark_r, 0, 0, $watermark_rWidth, $watermark_rHeight, $watermark_rBGColor);
                } else {
                    // Make the background transparent
                    $usingAlpha = true;
                    imagealphablending($watermark_r, false);
                    imagesavealpha($watermark_r, true);
                    $transparent = imagecolorallocatealpha($watermark_r, 255, 255, 255, 127);
                    imagefilledrectangle($watermark_r, 0, 0, $watermark_rWidth, $watermark_rHeight, $transparent);
                }

                // array imageTTFText  ( resource image, int size, int angle, int x, int y, int color, string fontfile, string text)
                imageFTText($watermark_r, $options['fsize'], 0, $thebox["left"] + ($watermark_rWidth - $thebox["width"]) / 2, $thebox["top"] + ($watermark_rHeight - $thebox["height"]) / 2, $txtColor, $options['font'], $options['text']);
            }
            // endif $options['type'] == 'msg'
            // Handle rotation
            if ($options['rotate'] != 0) {
                if (phpversion() >= 5.1) {
                    $png = imagecreatetruecolor($watermark_rWidth, $watermark_rHeight);
                    $bg = imagecolorallocatealpha($png , 0, 0, 0, 127);
                    $watermark_r = imagerotate($watermark_r, 40, $bg, 0);
                } else {
                    $watermark_r = imagerotate($watermark_r, $options['rotate'], 0);
                } // endif phpversion()

                $watermark_rWidth = imagesx($watermark_r);
                $watermark_rHeight = imagesy($watermark_r);
            } // endif $options['rotate'] !=0
            // Calculate overlay image position
            switch ($options['position']) {
                case 'tl':
                    $dest_x = $options['padding'];
                    $dest_y = $options['padding'];
                    break;
                case 'tr':
                    $dest_x = $size[0] - $watermark_rWidth - $options['padding'];
                    $dest_y = $options['padding'];
                    break;
                case 'bl':
                    $dest_x = $options['padding'];
                    $dest_y = $size[1] - $watermark_rHeight - $options['padding'];
                    break;
                case 'br':
                    $dest_x = $size[0] - $watermark_rWidth - $options['padding'];
                    $dest_y = $size[1] - $watermark_rHeight - $options['padding'];
                    break;
                default:
                    $dest_x = round(($size[0] - $watermark_rWidth) / 2);
                    $dest_y = round(($size[1] - $watermark_rHeight) / 2);
                    break;
            }
            // Overlay the logo watermark image on the original image
            // int imagecopymerge ( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h, int pct );
            if ($usingAlpha) {
                self::imagecopymerge_alpha($image, $watermark_r, $dest_x, $dest_y, 0, 0, $watermark_rWidth, $watermark_rHeight, $options['opacity']);
            } else {
                imagecopymerge($image, $watermark_r, $dest_x, $dest_y, 0, 0, $watermark_rWidth, $watermark_rHeight, $options['opacity']);
            }
            imagedestroy($watermark_r);
            ob_end_clean();
//            ob_clean();
            switch ($imagetype) {
                case(1):
                    header('Content-type: image/gif');
                    imagegif($image);
                    break;

                case(2):
                    header('Content-type: image/jpeg');
                    imagejpeg($image);
                    break;

                case(3):
                    header('Content-type: image/png');
                    imagepng($image);
                    break;

                case(6):
                    header('Content-type: image/bmp');
                    imagewbmp($image);
                    break;
            } // end switch($imagetype)

            imagedestroy($image);
            exit;
        }

        function hex2int($hex) {
            return array('r' => hexdec(substr($hex, 0, 2)), // 1st pair of digits
                'g' => hexdec(substr($hex, 2, 2)), // 2nd pair
                'b' => hexdec(substr($hex, 4, 2))  // 3rd pair
            );
        }

        function validHexColor($input = '000000', $default = '000000') {
            return @(eregi('^[0-9a-f]{6}$', $input)) ? $input : $default;
        }

        function rotateImage($img, $rotation) {
            $width = imagesx($img);
            $height = imagesy($img);
            switch ($rotation) {
                case 90: $newimg = @imagecreatetruecolor($height, $width);
                    break;
                case 180: $newimg = @imagecreatetruecolor($width, $height);
                    break;
                case 270: $newimg = @imagecreatetruecolor($height, $width);
                    break;
                case 0: return $img;
                    break;
                case 360: return $img;
                    break;
            }
            if ($newimg) {
                for ($i = 0; $i < $width; $i++) {
                    for ($j = 0; $j < $height; $j++) {
                        $reference = imagecolorat($img, $i, $j);
                        switch ($rotation) {
                            case 90: if (!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference)) {
                                return false;
                            }break;
                            case 180: if (!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference)) {
                                return false;
                            }break;
                            case 270: if (!@imagesetpixel($newimg, $j, $width - $i, $reference)) {
                                return false;
                            }break;
                        }
                    }
                } return $newimg;
            }
            return false;
        }

        function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
            if (!isset($pct)) {
                return false;
            }
            $pct /= 100;
            // Get image width and height
            $w = imagesx($src_im);
            $h = imagesy($src_im);
            // Turn alpha blending off
            imagealphablending($src_im, false);
            // Find the most opaque pixel in the image (the one with the smallest alpha value)
            $minalpha = 127;
            for ($x = 0; $x < $w; $x++)
                for ($y = 0; $y < $h; $y++) {
                    $alpha = ( imagecolorat($src_im, $x, $y) >> 24 ) & 0xFF;
                    if ($alpha < $minalpha) {
                        $minalpha = $alpha;
                    }
                }
            //loop through image pixels and modify alpha for each
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    //get current alpha value (represents the TANSPARENCY!)
                    $colorxy = imagecolorat($src_im, $x, $y);
                    $alpha = ( $colorxy >> 24 ) & 0xFF;
                    //calculate new alpha
                    if ($minalpha !== 127) {
                        $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
                    } else {
                        $alpha += 127 * $pct;
                    }
                    //get the color index with new alpha
                    $alphacolorxy = imagecolorallocatealpha($src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha);
                    //set pixel with the new color + opacity
                    if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                        return false;
                    }
                }
            }
            // The image copy
            imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        }

    }

}