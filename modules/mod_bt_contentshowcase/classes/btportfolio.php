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
require_once 'btsource.php';
require_once(JPATH_ADMINISTRATOR.'/components/com_bt_portfolio/tables/portfolio.php');
require_once(JPATH_ROOT.'/components/com_bt_portfolio/router.php');
/**
 * BtK2DataSource Class
 */
if(!class_exists('BtBtportfolioDataSource')){
class BtBtportfolioDataSource extends BTSource {

	public function getList() {
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
		$quality = (int) $params->get('image_quality', 80);
		$isStripedTags = $params->get('auto_strip_tags', 0);

		$extraURL = $params->get('open_target') != 'modalbox' ? '' : '&tmpl=component';

		$db = JFactory::getDBO();
		$date = JFactory::getDate();
		$now = $date->toSql();

		$dateFormat = $params->get('date_format', 'DATE_FORMAT_LC3');

		$show_author = $params->get('show_author', 0);

		$query = "SELECT DISTINCT a.*, c.title as category_title, c.alias as category_alias,
						c.id as catid" . " FROM #__bt_portfolios as a" . " LEFT JOIN #__bt_portfolio_categories c ON a.catids like CONCAT('%,',c.id,',%') ";

		$query .= " WHERE a.published = 1" . " AND a.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")" . " AND c.published = 1" . " AND c.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") AND a.language in (" . $db->Quote(JFactory::getLanguage()->getTag()) . "," . $db->Quote('*') . ")";
		// User filter
		$userId = $user->get('id');
		switch ($params->get('user_id')) {
			case 'by_me':
				$query .= 'AND a.created_by = ' . $userId;
				break;
			case 'not_me':
				$query .= 'AND a.created_by != ' . $userId;
				break;
			case 0:
				break;
			default:
				$query .= 'AND a.created_by = ' . $userId;
				break;
		}

		if ($params->get('show_featured', '1') == 2) {
			$query .= " AND a.featured != 1";
		}
		elseif ($params->get('show_featured', '1') == 3) {
			$query .= " AND a.featured = 1";
		}

		$data= array();

		$source = trim($this->_params->get('source', 'btportfolio_category'));
		$catids = $source == 'btportfolio_category' ? self::getCategoryIds():'';
        if($source == 'btportfolio_category' && !empty($catids) && $this->_params->get('limit_items_for_each')) {
			$db->setQuery('SELECT id from #__bt_portfolio_categories where id in ('.implode($catids,',').') order by ordering');
			$catids = $db->loadColumn();
            foreach ($catids as $catid){
                $condition = $condition = ' AND c.id = ' . $catid . ' ';
                $db->setQuery($query . $condition . ' ORDER BY ' . $ordering . ($limit ? ' LIMIT ' . $limit : ''));
                $data = array_merge($data, $db->loadObjectlist());
            }
        } else {
            $condition = $this->buildConditionQuery($source,$catids);
            $db->setQuery($query . $condition . ' ORDER BY ' . $ordering . ($limit ? ' LIMIT ' . $limit : ''));
            $data = array_merge($data, $db->loadObjectlist());
        }
	
		foreach ($data as $key => &$item) {
			$Itemid = BTFindItemID($item->catid,$item->id);
			$Itemid = $Itemid? '&Itemid='.$Itemid:'';
			if (in_array($item->access, $user->getAuthorisedViewLevels())) {

				$item->link = JRoute::_("index.php?option=com_bt_portfolio&view=portfolio&id=" . $item->id .':'.$item->alias .'&catid_rel='.$item->catid.':'.$item->category_alias.$Itemid);
			}
			else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

			$item->date = JHtml::_('date', $item->created, JText::_($dateFormat));

			//title cut
			if ($limit_title_by == 'word' && $titleMaxChars > 0) {

				$item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);

			}
			elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {

				$item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);

			}

			$item->title = htmlspecialchars($item->title);
			if($params->get('content_plugin')){
				$item->description = JHtml::_('content.prepare', $item->description);
			}

			if ($limitDescriptionBy == 'word') {

				$item->description = self::substrword($item->description, $maxDesciption, $replacer, $isStrips, $stringtags);

			}
			else {
				$item->description = self::substring($item->description, $maxDesciption, $replacer, $isStrips, $stringtags);
			}

			$item->categoryLink = JRoute::_("index.php?option=com_bt_portfolio&view=portfolios&catid=" . $item->catid .':'.$item->category_alias.$Itemid);

			//Get name author
			//If set get, else get username by userid
			if ($show_author) {
				$author = &JFactory::getUser($item->created_by);
				$item->author = $author->name;
			}
			$item->thumbnail = "";
			$item->mainImage = "";
			$item->authorLink = "#";
			$url_image = '';
			if ($params->get('show_image')) {
				if(!isset($item->image)){
					$db->setQuery('Select filename from #__bt_portfolio_images WHERE item_id = ' . $item->id . ' and `default` = 1');
					$image = $db->loadResult();
				}else{
					$image = $item->image;
				}
				if (!$image) {
					if ($this->_defaultThumb) $url_image = $this->_defaultThumb;
				}
				else {
					$app = JFactory::getApplication('site');
					$btPortfolioParams = $app->getParams('com_bt_portfolio');
					$imagePath = $btPortfolioParams->get('images_path', 'images/bt_portfolio');
					$url_image = JURI::root() . $imagePath . '/' . $item->id . '/large/' . $image;
				}
				$item->mainImage = $url_image;
				if ($isThumb)
					$item->thumbnail = self::renderThumb($url_image, $thumbWidth, $thumbHeight,$isThumb, $quality);
				else {
					$item->thumbnail = $url_image;
				}
			}
			$item->extra_fields = Bt_portfolioTablePortfolio::loadExtraFields($item->extra_fields,$item->catids);
		}
		return $data;
	}
	public function buildConditionQuery($source,$catids = ''){
		if ($source == 'btportfolio_category') {
			if (empty($catids)){
                $condition = '';
            }else{
				$condition = ' AND  c.id IN("' . implode('","', $catids) . '")';
			}
		}
		else {
			if (!$this->_params->get('btportfolio_article_ids', '')) {
				return '';
			}

			$ids = preg_split('/,/', $this->_params->get('btportfolio_article_ids', ''));

			$tmp = array();

			foreach ($ids as $id) {

				$tmp[] = (int) trim($id);

			}

			$condition = " AND a.id IN('" . implode("','", $tmp) . "')";

		}
		return $condition;
	}
	/*get category id for query function */
	function getCategoryIds(){
		$catids = array();
		if($this->_params->get('auto_category') && JRequest::getVar('option')=='com_bt_portfolio'){
			if(JRequest::getVar('view')=='portfolios'){
				$catid = JRequest::getInt('catid');
				if($catid) $catids = array($catid);
			}
			else
			if(JRequest::getVar('view')=='portfolio'){
				$catid = JRequest::getInt('catid_rel');
				if($catid) $catids = array($catid);
			}
		}
		if(empty($catids)){
			$catids = $this->_params->get('btportfolio_category', array());
		}
		
		//since 2.4.2
		if($this->_params->get('sub_categories', 0) && count($catids)){
			$db = JFactory::getDBO();
			$parents = $catids;
			foreach($parents as $c){
				$db->setQuery('SELECT id FROM #__bt_portfolio_categories WHERE parent_id = ' . $c);
				$children = $db->loadColumn();
				if($children && count($children)){
					$catids = array_merge($catids, $children);
				}
			}
			$catids = array_unique($catids);
		}
		$excluded  = str_replace(' ', '', $this->_params->get('exclude_categories', ''));
		if($excluded){
			$excluded = explode(',', $excluded);
		
			if($excluded && count($excluded)){
				$catids = array_diff($catids, $excluded);
			}
		}
		return $catids;
	}
}
}
