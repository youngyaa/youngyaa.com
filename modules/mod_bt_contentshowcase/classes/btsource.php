<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		June 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * class BTSource
 */
require_once 'images.php';
if(!class_exists('BTSource')){
abstract class BTSource{

	public $_thumbnailPath = '';
	public $_thumbnaiURL = '';
	public $_defaultThumb = '';
	public $_imagesRendered = array('thumbnail' => array(), 'mainImage' => array());
	public $_params = array();

	public function __construct($params = null) {
		$this->_params = $params;
	}

	function setThumbPathInfo($path, $url,$defaultThumb) {
		$this->_thumbnailPath = $path;
		$this->_thumbnaiURL = $url;
		$this->_defaultThumb = $defaultThumb;
		return $this;
	}

	public function setImagesRendered($name = array()) {
		$this->_imagesRendered = $name;
		return $this;
	}
	
	public function renderThumb($path, $width = 280, $height = 150, $isThumb = true, $quality) {
		if ($isThumb){
			$path = str_replace(JURI::base(), '', $path);
			$imagSource = JPATH_SITE . '/' . $path;
			$imagSource = urldecode($imagSource);
			$tmp = explode('/', $imagSource);
			$imageName = md5($path.$width.$height).'-'. $tmp[count($tmp) - 1];
			$thumbPath = $this->_thumbnailPath . $imageName;
			if (file_exists($imagSource)) {	
				if (!file_exists($thumbPath)) {
					//create thumb
					BTImageHelper::createImage($imagSource, $thumbPath, $width, $height, true, $quality);
				}
				return $this->_thumbnaiURL . $imageName;
			}else{
				
				 if (!file_exists($thumbPath)){
					 // Try to load image from external source
					 // Image loaded?
					 if ($this->_CreateImageUsingCurl( $path, $thumbPath, 30 )) {
						 BTImageHelper::createImage($thumbPath, $thumbPath, $width, $height, true, $quality);
						 return $this->_thumbnaiURL . $imageName;
					 }
				 } else {
					 return $this->_thumbnaiURL . $imageName;
				 }
			}
		}
		//return path
		return $path;
	}
	private static function _CreateImageUsingCurl( $url,$thumbPath, $maxImageLoadTime = 30 )
	{
		$curl = false;
		if ( function_exists( 'curl_init' ) )
		{
			$curl = curl_init();
		}
		if ( $curl )
		{
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_HEADER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $maxImageLoadTime );
			//curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_MAXREDIRS, 11/*just a number that seems plenty enough*/ );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER,  FALSE );
			$contents = curl_exec( $curl );
			curl_close( $curl );
			JFile::write($thumbPath,$contents);
			return true;
		}
		return false;
	}

	/**
	 * parser a image in the content of article.
	 *
	 * @param.
	 * @return
	 */
	public function parseImages($row) {
		$introtext = true;
		if(trim($row->fulltext) && $this->_params->get('checkimg_fulltext',0)){
			$text = $row->introtext . $row->fulltext;
			$introtext = false;
		}else{
			$text = $row->introtext;
		}
		$row->thumbnail = $this->_defaultThumb;
		$row->mainImage = $this->_defaultThumb;

		$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/Us";
		if (!$this->_params->get('check_image_exist',0)) {
			preg_match($regex, $text, $matches);
			$images = (count($matches)) ? $matches : array();
			if (count($images)) {
				$row->mainImage = $images[1];
				$row->thumbnail = $images[1];
				if($introtext){$row->introtext = str_replace($images[0], "", $row->introtext);}
			}
		}
		else {
			preg_match_all($regex, $text, $matches);
			foreach ($matches[1] as $key => $match) {
				@$url = getimagesize($match);
				if (is_array($url)) {
					$row->mainImage = $match;
					$row->thumbnail = $match;
					if($introtext){$row->introtext = str_replace($matches[0][$key], "", $row->introtext);}
					break;
				}
			}
		}

		return $row;
	}

	//create thumb and save link to item
	public function generateImages($item, $isThumb = true, $quality = 100) {
		//
		$item = $this->parseImages($item);

		foreach ($this->_imagesRendered as $key => $value) {

			if ($item->{$key} && $image = $this->renderThumb($item->{$key}, $value[0], $value[1], $isThumb, $quality)) {
				$item->{$key} = $image;

			}
		}
		return $item;
	}

	/**
	 * Get a subtring with the max length setting.
	 *
	 * @param string $text;
	 * @param int $length limit characters showing;
	 * @param string $replacer;
	 * @return tring;
	 */
	public static function substring($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {
	
		if($isStrips){
			$text = preg_replace('/\<p.*\>/Us','',$text);
			$text = str_replace('</p>','<br/>',$text);
			$text = strip_tags($text, $stringtags);
		}
		
		if(function_exists('mb_strlen')){
			if (mb_strlen($text) < $length)	return $text;
			$text = mb_substr($text, 0, $length);
		}else{
			if (strlen($text) < $length)	return $text;
			$text = substr($text, 0, $length);
		}
		
		return $text . $replacer;
	}

	/**
	 * Get a subtring with the max word setting
	 *
	 * @param string $text;
	 * @param int $length limit characters showing;
	 * @param string $replacer;
	 * @return tring;
	 */

	public static function substrword($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {
		if($isStrips){
			$text = preg_replace('/\<p.*\>/Us','',$text);
			$text = str_replace('</p>','<br/>',$text);
			$text = strip_tags($text, $stringtags);
		}
		$tmp = explode(" ", $text);

		if (count($tmp) < $length)
			return $text;

		$text = implode(" ", array_slice($tmp, 0, $length)) . $replacer;

		return $text;
	}
	
	/**
	 * Get list article (abstract function)
	 */
	abstract public function getList();
}
}
?>