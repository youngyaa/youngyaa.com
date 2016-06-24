<?php

/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		Oct 2011

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
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once 'btsource.php';
/**
 * class BtContentDataSource
 */
if(!class_exists('BtContentDataSource')){
class BtContentDataSource extends BTSource {

    function getList() {
        $params = &$this->_params;

        $formatter = $params->get('style_displaying', 'title');
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
		$use_linka = $params->get('use_linka',0);
		$use_introimg = $params->get('use_introimg',1);
		$use_caption = $params->get('use_caption',0);

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

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();

        $appParams = $app->getParams();

        $model->setState('params', $appParams);

        $model->setState('list.select', 'a.urls, a.images, a.fulltext, a.id, a.title, a.alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' . ' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' . ' a.hits, a.featured,' . ' LENGTH(a.fulltext) AS readmore');
        // Set the filters based on the module params

        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('limit_items', 12));
        $model->setState('filter.published', 1);

        // Access filter
		$userId = JFactory::getUser()->get('id');
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels($userId);
        $model->setState('filter.access', $access);

        $source = trim($params->get('source', 'category'));



        // User filter
        switch ($params->get('user_id')) {
            case 'by_me':
                $model->setState('filter.author_id', (int) $userId);
                break;
            case 'not_me':
                $model->setState('filter.author_id', $userId);
                $model->setState('filter.author_id.include', false);
                break;
            case 0:
                break;

            default:
                $model->setState('filter.author_id', (int) $params->get('user_id'));
                break;
        }

        // Filter by language
        $model->setState('filter.language',$app->getLanguageFilter());

        //  Featured switch
        switch ($params->get('show_featured')) {
            case 3:
                $model->setState('filter.featured', 'only');
                break;
            case 2:
                $model->setState('filter.featured', 'hide');
                break;
            default:
                $model->setState('filter.featured', 'show');
                break;
        }

        // Set ordering
		$ordering = explode('-', $ordering);
		if (trim($ordering[0]) == 'rand') {
			$model->setState('list.ordering', ' RAND() ');
		} else
		if (trim($ordering[0]) == 'featured')
		{
			$model->setState('list.ordering', ' fp.ordering');
			$model->setState('list.direction', 'asc');
		}
		else
		{
			$model->setState('list.ordering', 'a.' . $ordering[0]);
			$model->setState('list.direction', $ordering[1]);
		}

        //if category
        $items = array();
        if ($source == 'category') {
            $category_ids = self::getCategoryIds();
            if (!empty($category_ids) && $params->get('limit_items_for_each')) {
				$db->setQuery('SELECT id from #__categories where id in ('.implode($category_ids,',').') order by lft');
				$category_ids = $db->loadColumn();
                foreach ($category_ids as $category_id) {
                    $model->setState('filter.category_id', array($category_id));
                    $itemsPerCategory = $model->getItems();
                    $items = array_merge($items,$itemsPerCategory);
                }

            } else {
                // Category filter
                $model->setState('filter.category_id', $category_ids);
                $items = $model->getItems();;
            }
            //esle article_ids
        } else if($source == 'article_ids'){
            $ids = preg_split('/,/', $params->get('article_ids', ''));
            $tmp = array();
            foreach ($ids as $id) {
                $tmp[] = (int) trim($id);
            }
            $model->setState('filter.article_id', $tmp);
            $items = $model->getItems();
        }else{
			$tagsHelper = new JHelperTags();
			$tagIds = $this->_params->get('joomla_tags');
			if($tagsIds){
				$query = $tagsHelper->getTagItemsQuery(implode(',', $tagIds));
				$db->setQuery($query);
				$rs = $db->loadObjectList();
				if(!$rs){
					$items = $model->getItems();
				}else{
					$articleIds = array();
					foreach($rs as $article){
						if($article->type_alias != 'com_content.article') continue;
						$articleIds[] = $article->content_item_id;
					}
					$model->setState('filter.article_id', $articleIds);
					$items = $model->getItems();
				}
			}else{
				$items = $model->getItems();
			}	
		}
        foreach ($items as &$item) {

			// setting for route link
            $item->slug = $item->id . ':' . $item->alias;
            $item->catslug = $item->catid . ':' . $item->category_alias;

			// item link
			$item->link = '';
            if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				//Item link
				if($use_linka && $item->urls){
					$item->urls = json_decode($item->urls);
					$item->link = $item->urls->urla;
				}
				if(!$item->link) $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, JFactory::getLanguage()->getTag()));
			}
			else {

				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

			// format date
            $item->date = JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));

			// thumbnail & caption
			$item->thumbnail = '';
			$item->mainImage = '';
			if(($use_introimg|| $use_caption) && $item->images){
				$item->images = json_decode($item->images);
			}
            if ($showimage) {
				if($item->images && $use_introimg){
					$imgLink = $item->images->image_intro;
					if(!$imgLink){
						$imgLink = $imgLink;
					}
					if($imgLink){
						$item->mainImage = $imgLink;
						if ($isThumb){
							$item->thumbnail = self::renderThumb($imgLink, $thumbWidth, $thumbHeight, $isThumb, $quality);
						}
						else {
							$item->thumbnail = $imgLink;
						}
					}
				}
				if(!$item->thumbnail){
					$item = $this->generateImages($item, $isThumb,$quality);
				}
			}
			// change introtext with caption
			if($use_caption && $item->images){
				$caption = $item->images->image_intro_caption;
				if(!$caption){
						$caption = $item->images->image_fulltext_caption;
				}
				if($caption){
					$item->introtext = $caption;
				}
			}
			// set category link
            $item->categoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid, JFactory::getLanguage()->getTag()));

			// cut title
            if ($limit_title_by == 'word' && $titleMaxChars > 0) {

                $item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);
            } elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {
                $item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);
            }
			// escape html characters
			$item->title = htmlspecialchars($item->title);

			// import content prepare plugin
            if($params->get('content_plugin')){
                $item->introtext = JHtml::_('content.prepare', $item->introtext);
            }

			// cut description
            if ($limitDescriptionBy == 'word' && $descriptionMaxChars > 0) {
                $item->description = self::substrword($item->introtext, $descriptionMaxChars, $replacer, $isStrips, $stringtags);
            } elseif ($limitDescriptionBy == 'char' && $descriptionMaxChars > 0) {
                $item->description = self::substring($item->introtext, $descriptionMaxChars, $replacer, $isStrips, $stringtags);
            }

            // set authorlink empty
            $item->authorLink = "#";
        }
        return $items;
    }
	function getCategoryIds(){
		$db = JFactory::getDBO();
		$category = array();
		if($this->_params->get('auto_category') && JRequest::getVar('option')=='com_content'){
			$catid = JRequest::getInt('catid');
			if(!$catid && JRequest::getVar('view') == 'category') $catid = JRequest::getInt('id');
			if($catid) $category = array($catid);
			else{
				if( JRequest::getVar('view')=='article'){
					$itemid = JRequest::getInt('id');
					$db->setQuery('select catid from #__content where id='.$itemid);
					$catid = $db->loadResult();
					if($catid) $category = array($catid);
				}
			}
		}
		if(empty($category)){
			$category = $this->_params->get('category', array());
		}
		
		// since 2.4.2
		if($this->_params->get('sub_categories', 0) && count($category)){
			$parents = $category;
			foreach($parents as $c){
				$instance = JCategories::getInstance('Content');
				$cat = $instance->get($c);
				$children = $cat->getChildren();
				if(count($children)){
					foreach($children as $ch){
						if(!in_array($ch->id, $category)){
							$category[] = $ch->id;
						}
					}
				}
			}
		}
		
		$exCategories = str_replace(' ', '', $this->_params->get('exclude_categories', ''));
		if($exCategories){
			$exCategories = explode(',', $exCategories);
			if($exCategories && count($exCategories)){
				$category = array_diff($category, $exCategories);
			}
		}
		return $category;
	}

}
}
?>