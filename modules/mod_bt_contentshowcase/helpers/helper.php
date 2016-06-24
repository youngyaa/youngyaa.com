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
jimport('joomla.filesystem.folder');

class modBtContentShowcaseHelper {

	/**
	 * Get list articles
	 * Ver 1 : only form content
	 */
	public static function getList( &$params, $module ){
		// create thumbnail folder
	 	$thumbPath = JPATH_BASE . '/cache/' .$module->module.'/';
		$thumbUrl  = str_replace(JPATH_BASE.'/',JURI::base(),$thumbPath);
		$defaultThumb = JURI::base().'modules/'.$module->module.'/images/no-image.jpg';	
		
		if( !is_dir($thumbPath) ) {
			JFolder::create( $thumbPath, 0755 );
		};
		
		//Get source form params
		$source 	= $params->get('source','category');
		if($source == 'category' || $source == 'article_ids' || $source == 'joomla_tags')
		{
			$source = 'content';
		}
		else if($source == 'k2_category' || $source == 'k2_article_ids' || $source == 'k2_tags')
		{
			$source = 'k2';
		}
		else if($source == 'btportfolio_category' || $source == 'btportfolio_article_ids')
		{
			$source = 'btportfolio';
		}
		else if($source == 'easyblog_category' || $source == 'easyblog_article_ids')
		{
			$source = 'easyblog';
		}
		else{
			$source = 'content';
		}


		//var_dump($source);

		$path = JPATH_SITE.'/modules/mod_bt_contentshowcase/classes/'.$source.".php";
		require_once $path;
		$objectName = "Bt".ucfirst($source)."DataSource";
	 	$object = new $objectName($params );
		//3 step
		//1.set images path
		//2.Render thumb
		//3.Get List
	 	$items = $object->setThumbPathInfo($thumbPath,$thumbUrl,$defaultThumb)
			->setImagesRendered( array( 'thumbnail' =>
                                array( (int)$params->get( 'thumbnail_width', 60 ), (int)$params->get( 'thumbnail_height', 60 ))
                        ) )
			->getList();
  		return $items;

           
	}

	public static function fetchHead($params){
		$document	= JFactory::getDocument();
		$header = $document->getHeadData();
		$mainframe = JFactory::getApplication();
		$template = $mainframe->getTemplate();
        $layout = $params->get('layout');
        $templatePath = JPATH_BASE.'/templates/'.$template.'/html/mod_bt_contentshowcase/themes/'.$layout;
        $templateURL = JURI::root().'templates/'.$template.'/html/mod_bt_contentshowcase/themes/'.$layout;
        $moduleURL = JURI::root().'modules/mod_bt_contentshowcase';
        $moduleLayoutURL = $moduleURL. '/tmpl/themes/' . $layout;
         
		$loadJquery = true;
		$loadJcarousel = true;
		switch($params->get('loadJquery',"auto")){
			case "0":
				$loadJquery = false;
				break;
			case "1":
				$loadJquery = true;
				break;
			case "auto":
				foreach($header['scripts'] as $scriptName => $scriptData)
				{
					if(substr_count($scriptName,'/jquery'))
					{
						$loadJquery = false;
					}
				}
			break;
		}
		foreach($header['scripts'] as $scriptName => $scriptData)
		{
			if($layout == 'blocknews' || $layout == 'simple_list' || $layout == 'featuredcarousel'){
				$loadJcarousel = false;
				break;
			}
			if (substr_count($scriptName, 'jcarousel')) {
				$loadJcarousel = false;
			}
		}
		
		//Add js
		if($loadJquery)
		{
			$document->addScript($moduleURL. '/assets/js/jquery.min.js');
		}
        //add jcarousel lib
		if (substr_count($layout, 'metro')){
			$document->addScript($moduleURL. '/assets/js/metroslide.js');
		}else{
			if ($loadJcarousel){
				$document->addScript($moduleURL. '/assets/js/jcarousel.js');
				$document->addStyleSheet( $moduleURL.'/assets/css/jcarousel.css');
			}
		}

        //overide css for layout
		if(file_exists($templatePath.'/css/btcontentshowcase.css'))
		{
			$document->addStyleSheet(  $templateURL.'/css/btcontentshowcase.css');
		}
		else{
			$document->addStyleSheet($moduleLayoutURL. '/css/btcontentshowcase.css');
		}
         
		if(file_exists($templatePath.'/js/default.js'))
		{
			$document->addScript(  $templateURL.'/js/default.js');
		}
		else{
			$document->addScript($moduleLayoutURL. '/js/default.js');
		}	
		if($layout == 'frontpageshow'||$layout == 'highlight'){
			$document->addScript($moduleURL. '/assets/js/jquery.easing.1.3.js');
		}
		if($layout == 'featuredcarousel'){
			$document->addScript($moduleLayoutURL. '/js/helper-plugins/jquery.touchSwipe.min.js');
			$document->addScript($moduleLayoutURL. '/js/caroufredsel.min.js');
			$document->addScript($moduleLayoutURL. '/js/imagesloaded.pkgd.min.js');
		}
		
	}
}
?>