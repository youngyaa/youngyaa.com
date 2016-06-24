<?php

/**
 * @package 	helpers
 * @version		1.4
 * @created		Dec 2011
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

if (!class_exists('BTMediaImageHelper')) {

    class BTMediaImageHelper extends JObject {

        private $image;
        private $sourcefile;
        private $image_type;
        private $image_width;
        private $image_height;

        private function getImageCreateFunction($type) {
            switch ($type) {
                case 'image/jpeg':
                case 'image/jpg':
                    $imageCreateFunc = 'imagecreatefromjpeg';
                    break;

                case 'image/png':
                    $imageCreateFunc = 'imagecreatefrompng';
                    break;

                case 'image/bmp':
                    $imageCreateFunc = 'imagecreatefrombmp';
                    break;

                case 'image/gif':
                    $imageCreateFunc = 'imagecreatefromgif';
                    break;

                case 'image/vnd.wap.wbmp':
                    $imageCreateFunc = 'imagecreatefromwbmp';
                    break;

                case 'image/xbm':
                    $imageCreateFunc = 'imagecreatefromxbm';
                    break;

                default:
                    $imageCreateFunc = 'imagecreatefromjpeg';
            }

            return $imageCreateFunc;
        }

        private function getImageSaveFunction($type) {
            switch ($type) {
                case 'jpeg':
                    $imageSaveFunc = 'imagejpeg';
                    break;

                case 'png':
                    $imageSaveFunc = 'imagepng';
                    break;

                case 'bmp':
                    $imageSaveFunc = 'imagebmp';
                    break;

                case 'gif':
                    $imageSaveFunc = 'imagegif';
                    break;

                case 'vnd.wap.wbmp':
                    $imageSaveFunc = 'imagewbmp';
                    break;

                case 'xbm':
                    $imageSaveFunc = 'imagexbm';
                    break;

                default:
                    $imageSaveFunc = 'imagejpeg';
            }

            return $imageSaveFunc;
        }

        public function loadImage($src) {
            list($imagewidth, $imageheight, $mimeType) = getimagesize($src);
            $this->image_width = $imagewidth;
            $this->image_height = $imageheight;
            $this->image_type = image_type_to_mime_type($mimeType);

            $this->sourcefile = $src;
        }

        private function save($filename, $quality = 100, $permissions = null) {
            $imageSaveFunc = self::getImageSaveFunction($this->image_type);
            if ($this->image_type == 'png') {
                $imageSaveFunc($this->image, $filename);
            } else if ($this->image_type == 'gif') {
                $imageSaveFunc($this->image, $filename);
            } else {
                $imageSaveFunc($this->image, $filename, $quality);
            }

            if ($permissions != null) {
                chmod($filename, $permissions);
            }
        }

        public function resize($new_name, $dWidth, $dHeight, $quality = 100, $crop = false, $crop_pos = 'crop_center') {
            if (!$crop) {
                $sx = 0;
                $sy = 0;
                $width = $this->image_width;
                $height = $this->image_height;
            } else {
                if ($this->image_height / $this->image_width > $dHeight / $dWidth) {
                    $width = $this->image_width;
                    $height = round(($dHeight * $this->image_width) / $dWidth);
                } else {
                    $height = $this->image_height;
                    $width = round(($this->image_height * $dWidth) / $dHeight);
                }
                switch ($crop_pos) {
                    case 'crop_top_left':
                        $sx = 0;
                        $sy = 0;
                        break;
                    case 'crop_top_middle':
                        $sx = round(($this->image_width - $width) / 2);
                        $sy = 0;
                        break;
                    case 'crop_top_right':
                        $sx = $this->image_width - $width;
                        $sy = 0;
                        break;
                    case 'crop_buttom_left':
                        $sx = 0;
                        $sy = $this->image_height - $height;
                        break;
                    case 'crop_buttom_middle':
                        $sx = round(($this->image_width - $width) / 2);
                        $sy = $this->image_height - $height;
                        break;
                    case 'crop_buttom_right':
                        $sx = $this->image_width - $width;
                        $sy = $this->image_height - $height;
                        break;
                    default:
                        if ($this->image_height / $this->image_width > $dHeight / $dWidth) {
                            $sx = 0;
                            $sy = round(($this->image_height - $height) / 2);
                        } else {
                            $sx = round(($this->image_width - $width) / 2);
                            $sy = 0;
                        }
                        break;
                }
            }

            $dImage = imagecreatetruecolor($dWidth, $dHeight);

            // Make transparent
            if ($this->image_type == 'png') {
                imagealphablending($dImage, false);
                imagesavealpha($dImage, true);
                $transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
                imagefilledrectangle($dImage, 0, 0, $dWidth, $dHeight, $transparent);
            }

            $imageCreateFunc = self::getImageCreateFunction($this->image_type);
            $this->image = $imageCreateFunc($this->sourcefile);

            imagecopyresampled($dImage, $this->image, 0, 0, $sx, $sy, $dWidth, $dHeight, $width, $height);
            $this->image = $dImage;
            self::save($new_name, $quality);
        }

        public function crop($new_name, $startWidth, $startHeight, $dWidth, $dHeight, $quality = 100) {

            if ($this->image_width > $dWidth) {
                $newImageWidth = $dWidth;
            } else {
                $newImageWidth = $this->image_width;
            }

            if ($this->image_height > $dHeight) {
                $newImageHeight = $dHeight;
            } else {
                $newImageHeight = $this->image_height;
            }

            $imageCreateFunc = self::getImageCreateFunction($this->image_type);

            $sImage = $imageCreateFunc($this->sourcefile);
            $dImage = imagecreatetruecolor($newImageWidth, $newImageHeight);

            // Make transparent
            if ($this->image_type == 'png') {
                imagealphablending($dImage, false);
                imagesavealpha($dImage, true);
                $transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
                imagefilledrectangle($dImage, 0, 0, $newImageWidth, $newImageHeight, $transparent);
            }

            imagecopy($dImage, $sImage, 0, 0, $startWidth, $startHeight, $newImageWidth, $newImageHeight);
            $this->image = $dImage;
            self::save($new_name, $quality);
        }

        public function resizeToHeight($new_name, $height, $quantity = 100) {
            $ratio = $height / $this->image_height;
            $width = $this->image_width * $ratio;
            $this->resize($new_name, $width, $height, $quantity);
        }

        public function resizeToWidth($new_name, $width, $quantity = 100) {
            $ratio = $width / $this->image_width;
            $height = $this->image_height * $ratio;
            $this->resize($new_name, $width, $height, $quantity);
        }

        public function scale($new_name, $scale, $quantity = 100) {
            $width = $this->image_width * $scale / 100;
            $height = $this->image_height * $scale / 100;
            $this->resize($new_name, $width, $height, $quantity);
        }

    }

}
?>
