<?php

/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.4.5
 * @created		July 2013
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
//Get to process link
jimport('joomla.application.component.model');

require_once 'btsource.php';

/**
 * class BtContentDataSource
 */
if(!class_exists('BtEasyblogDataSource')){
class BtEasyblogDataSource extends BTSource {
	private $easyBlogVersion = '5.0';
	public function __construct($params = null){
		$db = JFactory::getDbo();
		$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE name = 'com_easyblog'");
		$manifest_cache= $db->loadResult();
		if($manifest_cache){
			$cache = new JRegistry($manifest_cache);
			$this->easyBlogVersion = $cache->get('version');
		}
		
		if(version_compare($this->easyBlogVersion, '5.0', 'ge')){
			require_once( JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php' );	
		}else{
			require_once(JPATH_ROOT . '/components/com_easyblog/constants.php');
			require_once( EBLOG_HELPERS . '/helper.php' );
			require_once( EBLOG_HELPERS . '/router.php' );
			require_once( EBLOG_CLASSES . '/image.php' );
		}
		require_once( JPATH_ROOT . '/components/com_easyblog/router.php');
		parent::__construct($params);
	}
    function getList() {
        $params = &$this->_params;

        $titleMaxChars = $params->get('title_max_chars', '100');
        $limit_title_by = $params->get('limit_title_by', 'char');
        $descriptionMaxChars = $params->get('description_max_chars', 100);
        $limitDescriptionBy = $params->get('limit_description_by', 'char');
        $isThumb = $params->get('image_thumb', 1);
		$thumbWidth = (int) $params->get('thumbnail_width', 280);
		$thumbHeight = (int) $params->get('thumbnail_height', 150);
		$quality = (int) $params->get('image_quality', 80);
		$showimage = $params->get('show_image', 1);
        $replacer = $params->get('replacer', '...');
        $isStrips = $params->get("auto_strip_tags", 1);
		$limit = $params->get('limit_items', 12);
        $stringtags = '';
		if ($isStrips) {
			$allow_tags = $params->get("allow_tags", '');
			$stringtags = '';
			if(!is_array($allow_tags)){
				$allow_tags = explode(',',$allow_tags);
			}
			foreach ($allow_tags as $tag) {
				$stringtags .= '<' . $tag . '>';
			}
		}

        if (!$params->get('default_thumb', 1)) {
            $this->_defaultThumb = '';
        }

        $ordering = $params->get('ordering', 'created-asc');

        $db = JFactory::getDbo();
		$query = '	SELECT p.*, c.title as category_title, f.id as featured, f.created as featured_created , u.name as author
					FROM #__easyblog_post as p
					LEFT JOIN #__easyblog_category as c ON p.category_id = c.id
					LEFT JOIN #__easyblog_featured as f ON p.id = f.content_id AND f.type = \'post\'
					LEFT JOIN #__users as u ON p.created_by = u.id';
		
		//build where 
		$where = ' WHERE p.published = 1 ';
		$userId = JFactory::getUser()->get('id');
		//$where .=' AND (p.private = 0 OR (p.created_by = ' . $userId . ' AND p.private = 1)) ';
		
        // User filter
        switch ($params->get('user_id')) {
            case 'by_me':
                $where .= ' AND p.created_by = ' . $userId;
                break;
            case 'not_me':
                $where .= ' AND p.created_by <> ' . $userId;
                break;
            default:
                break;
        }
		// filter by featured params
        if ($params->get('show_featured', '1') == 2) {
            $where .= " AND f.id IS NULL ";
        } elseif ($params->get('show_featured', '1') == 3) {
            $where .= " AND f.id IS NOT NULL ";
        }
		
        // Set ordering
		$order = '';
		$ordering = explode('-', $ordering);
		if (trim($ordering[0]) == 'rand') {
			$order = ' ORDER BY RAND() ';
			
		} else if (trim($ordering[0]) == 'featured'){
			$order = ' ORDER BY f.created DESC ' ;
		}
		else
		{
			$order = ' ORDER BY p.' . $ordering[0] . ' ' . $ordering[1];
		}

        //if category
		$source = trim($params->get('source', 'category'));
        $items = array();
		
        if ($source == 'easyblog_category') {
            $category_ids = self::getCategoryIds();
            if (!empty($category_ids) && $params->get('limit_items_for_each')) {
				
                foreach ($category_ids as $category_id) {
					$db->setQuery($query. $where . ' AND p.category_id = ' . $category_id . $order . ($limit ? ' LIMIT ' . $limit : ''), false);
                    $itemsPerCategory = $db->loadObjectList();
					
                    $items = array_merge($items,$itemsPerCategory);
                }

            } else if(!empty($category_ids)){
                // Category filter
				$where .= ' AND p.category_id IN (' . implode(',', $category_ids) . ') ';
                $db->setQuery($query. $where . $order . ($limit ? ' LIMIT ' . $limit : ''));
				$items = $db->loadObjectList();
            }else{
				$db->setQuery($query. $where . $order . ($limit ? ' LIMIT ' . $limit : ''));
				$items = $db->loadObjectList();
			}
            //esle article_ids
        } else {
            $where .= ' AND p.id IN (' . $params->get('easyblog_article_ids') . ' ) ';
			$db->setQuery($query. $where . $order . ($limit ? ' LIMIT ' . $limit : ''));
			$items = $db->loadObjectList();
            
        }
        foreach ($items as &$item) {
			$item->fulltext = $item->content;
			$item->introtext = $item->intro;
			$item->catid = $item->category_id;
			$item->link = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->id . $this->getMenuItemId($item));

			// format date
            $item->date = JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));

			// thumbnail & caption
			$item->thumbnail = '';
			$item->mainImage = '';

            if ($showimage) {
				$imageUrl = $this->getImage($item);
				if($imageUrl){
					$item->thumbnail = $item->mainImage = $imageUrl;
					if ($isThumb){
						$item->thumbnail = self::renderThumb($item->thumbnail, $thumbWidth, $thumbHeight, $isThumb, $quality);
					}
				}else{
					
					$item = $this->generateImages($item, $isThumb, $quality);
				}
			}
			//var_dump($item->fulltext);
			// set category link
            $item->categoryLink = EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$item->category_id . $this->getMenuItemId($item, 'category'));

			// cut title
            if ($limit_title_by == 'word' && $titleMaxChars > 0) {
                $item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);
            } elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {
                $item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);
            }
			// escape html characters
			$item->title = htmlspecialchars($item->title);

			// cut description
            if ($limitDescriptionBy == 'word' && $descriptionMaxChars > 0) {
                $item->description = self::substrword($item->intro, $descriptionMaxChars, $replacer, $isStrips, $stringtags);
            } elseif ($limitDescriptionBy == 'char' && $descriptionMaxChars > 0) {
                $item->description = self::substring($item->intro, $descriptionMaxChars, $replacer, $isStrips, $stringtags);
            }
			
			// import content prepare plugin
            if($params->get('content_plugin')){
                $item->description = JHtml::_('content.prepare', $item->description);
            }
			
            // set authorlink empty
            $item->authorLink = EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $item->created_by . $this->getMenuItemId($item, 'blogger'));
        }
        return $items;
    }
	function getCategoryIds(){
		$db = JFactory::getDBO();
		$category = array();
		if($this->_params->get('auto_category') && JRequest::getVar('option')=='com_easyblog'){
			if(JRequest::getVar('view')=='categories'){
				$catid = JRequest::getInt('id');
			}
			else{
				if( JRequest::getVar('view')=='entry'){
					$itemid = JRequest::getInt('id');
					$db->setQuery('select category_id from #__easyblog_post where id='.$itemid);
					$catid = $db->loadResult();
					
				}
			}
			if($catid) $category = array($catid);
		}
		if(empty($category)){
			$category = $this->_params->get('easyblog_category', array());
		}
		
		//since 2.4.2
		if($this->_params->get('sub_categories', 0) && count($category)){
			$db = JFactory::getDBO();
			$parents = $category;
			foreach($parents as $c){
				$db->setQuery('SELECT id FROM #__easyblog_category WHERE parent_id = ' . $c);
				$children = $db->loadColumn();
				if($children && count($children)){
					$category = array_merge($category, $children);
				}
			}
			$category = array_unique($category);
		}
		$excluded  = str_replace(' ', '', $this->_params->get('exclude_categories', ''));
		if($excluded){
			$excluded = explode(',', $excluded);
		
			if($excluded && count($excluded)){
				$category = array_diff($category, $excluded);
			}
		}
		return $category;
	}
	
	public function getImage($blog)
	{
		static $image	= array();
		if( !isset( $image[ $blog->id ] ) )
		{			
			if( !$blog->image )
			{
				$image[ $blog->id ]		= false;
				return false;
			}

			if(version_compare($this->easyBlogVersion,'5.0', 'ge')){
				$mm = EB::mediamanager();
				$uri = JUri::getInstance();
				return $uri->getScheme() . ':' . $mm->getUrl($blog->image);
			}else{
				$imageObject	= json_decode( $blog->image );
				if( !$imageObject )
				{
					$image 		= false;
					return false;
				}
				// Get the configuration object.
				$cfg = EasyBlogHelper::getConfig();

				// Let's see where should we find for this.
				$storagePath 	= '';
				$storageURI		= '';

				if( isset( $imageObject->place ) && $imageObject->place == 'shared' )
				{
					$storagePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_shared_path' ) , '/\\');
					$storageURI		= rtrim( JURI::root() , '/' ) . '/' . trim( str_ireplace( '\\' , '/' , $cfg->get( 'main_shared_path' ) ) , '/\\');
				}
				else
				{
					$place 			= $imageObject->place;
					$place 			= explode( ':' , $place );
					$place[1]		= (int) $place[1];

					$path 			= $imageObject->path;

					// Set the storage path
					$storagePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_image_path' ) , '/\\') . DIRECTORY_SEPARATOR . $place[1];
					// @task: Set the storage URI
					$storageURI		= rtrim( JURI::root() , '/' ) . '/' . trim( $cfg->get( 'main_image_path' ) , '/\\') . '/' . $place[1];
				}

				// Ensure that the item really exist before even going to do anything on the original image.
				// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
				$itemPath 			= $storagePath . DIRECTORY_SEPARATOR . trim( $imageObject->path , '/\\' );

				if( !JFile::exists( $itemPath ) )
				{
					// @TODO: Perhaps we should update $this->image with an empty value since image no longer exists.
					$image[ $blog->id ]		= false;
					return false;
				}


				$image[ $blog->id ]			= new EasyBlogImage( $imageObject->path , $storagePath , $storageURI );
				return $image[ $blog->id ]->getSource('original');
			}
		
		}
	}
	function getMenuItemId($post, $type = 'default')
	{
		$itemId                 = '';
		$routeTypeCategory		= false;
		$routeTypeBlogger		= false;
		$routeTypeTag			= false;

		if( $type != 'default' )
		{
			switch ($type)
			{
				case 'menuitem':
					$itemId					= $params->get( 'menuitemid' ) ? '&Itemid=' . $params->get( 'menuitemid' ) : '';
					break;
				case 'category':
					$routeTypeCategory  = true;
					break;
				case 'blogger':
					$routeTypeBlogger  = true;
					break;
				case 'tag':
					$routeTypeTag  = true;
					break;
				default:
					break;
			}
		}

		if( $routeTypeCategory )
		{
			$xid    = EasyBlogRouter::getItemIdByCategories( $post->category_id );
		}
		else if($routeTypeBlogger)
		{
			$xid    = EasyBlogRouter::getItemIdByBlogger( $post->created_by );
		}
		else if($routeTypeTag)
		{
			$tags	= self::_getPostTagIds( $post->id );
			if( $tags !== false )
			{
				foreach( $tags as $tag )
				{
					$xid    = EasyBlogRouter::getItemIdByTag( $tag );
					if( $xid !== false )
						break;
				}
			}
		}

		if( !empty( $xid ) )
		{
			// lets do it, do it, do it, lets override the item id!
			$itemId = '&Itemid=' . $xid;
		}

		return $itemId;
	}
}
}
?>