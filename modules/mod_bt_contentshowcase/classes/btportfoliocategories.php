<?php

/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.3.1
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
require_once(JPATH_ADMINISTRATOR.'/components/com_bt_portfolio/tables/category.php');
require_once(JPATH_ROOT.'/components/com_bt_portfolio/router.php');
require_once 'btsource.php';
/**
 * class BtContentDataSource
 */
if(!class_exists('BtPortofolioCategoriesDataSource')){
class BtBtportfoliocategoriesDataSource extends BTSource {

    function getList() {
        if (!is_file(JPATH_SITE . "/components/com_bt_portfolio/bt_portfolio.php")) {
			return array();
		}

		$params = &$this->_params;

		/* title */
		$show_title = $params->get('show_title', 1);

		$titleMaxChars = $params->get('title_max_chars', '100');
		$limit_title_by = $params->get('limit_title_by', 'char');
		$replacer = $params->get('replacer', '...');
		$isStrips = $params->get("auto_strip_tags", 1);
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

		/* intro */
		$show_intro = $params->get('show_intro', 1);

		$maxDesciption = $params->get('description_max_chars', 100);

		$limitDescriptionBy = $params->get('limit_description_by', 'char');


		$ordering = $params->get('ordering', 'created-desc');
		if ($ordering == 'publish_up-asc')
			$ordering = 'created-desc';

		$limit = $params->get('limit_items', 12);

		//ordering_asc -> ordering asc
		//$ordering      = str_replace( '_', '  ', $ordering );

		// Set ordering
		$ordering = explode('-', $ordering);
		if (trim($ordering[0]) == 'rand') {
			$ordering = ' RAND() ';
		}
		else {
			$ordering = $ordering[0] . ' ' . $ordering[1];
		}


		//check user access to articles
		$user = JFactory::getUser();

		$isThumb = $params->get('image_thumb', 1);
		$thumbWidth = (int) $params->get('thumbnail_width', 280);
		$thumbHeight = (int) $params->get('thumbnail_height', 150);

		$isStripedTags = $params->get('auto_strip_tags', 0);

		$db = JFactory::getDBO();
		$query = "SELECT DISTINCT c.* FROM #__bt_portfolio_categories as c";
		$query .= " WHERE c.published = 1" . " AND c.id IN (" . implode(',', $this->_params->get('btportfolio_category', array())) . ") AND c.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") AND c.language in (" . $db->Quote(JFactory::getLanguage()->getTag()) . "," . $db->Quote('*') . ")";

		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach ($data as $key => &$item) {
			$Itemid = BTFindItemID($item->id);
			$Itemid = $Itemid? '&Itemid='.$Itemid:'';
			if (in_array($item->access, $user->getAuthorisedViewLevels())) {
				$item->link = JRoute::_("index.php?option=com_bt_portfolio&view=category&id=" . $item->id .':'.$item->alias.$Itemid);
			}
			else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

			$item->date = false;

			//title cut
			if ($limit_title_by == 'word' && $titleMaxChars > 0) {
				$item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);
			}
			elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {
				$item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);
			}
			$item->title = htmlspecialchars($item->title);
			

			if ($limitDescriptionBy == 'word') {
				$item->description = self::substrword($item->description, $maxDesciption, $replacer, $isStrips, $stringtags);
			}
			else {
				$item->description = self::substring($item->description, $maxDesciption, $replacer, $isStrips, $stringtags);
			}

			$item->categoryLink = false;

			//Get name author
			//If set get, else get username by userid
			$item->author = false;
			$item->thumbnail = "";
			$item->mainImage = "";
			$item->authorLink = "#";
			$url_image = '';
			if ($params->get('show_image')) {
				$image = $item->main_image;
				if (!$image) {
					if ($this->_defaultThumb) $url_image = $this->_defaultThumb;
				}
				else {
					$app = JFactory::getApplication('site');
					$btPortfolioParams = $app->getParams('com_bt_portfolio');
					$url_image = JURI::root() . $image;
				}
				$item->mainImage = $url_image;
				if ($isThumb)
					$item->thumbnail = self::renderThumb($url_image, $thumbWidth, $thumbHeight,$isThumb);
				else {
					$item->thumbnail = $url_image;
				}
			}
			$item->extra_fields = null;
		}
		
		return $data;
    }
}
}
?>