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
 * BtK2DataSource Class
 */

require_once JPATH_SITE . '/components/com_k2/helpers/route.php';
require_once(JPath::clean(JPATH_SITE . '/components/com_k2/helpers/utilities.php'));
require_once 'btsource.php';

if(!class_exists('BtK2DataSource')){
class BtK2DataSource extends BTSource {

    /**
     * looking for image inside the media folder.
     * heave size XS, XL, S, M, L, Generic
     */
    public function lookingForK2Image($item, $size = 'XL') {
		//Image
		$item->imageK2Image = '';
		if (JFile::exists(JPATH_SITE .  '/media/k2/items/cache/' . md5("Image" . $item->id) . '_' . $size . '.jpg'))
			$item->imageK2Image = JURI::base() . 'media/k2/items/cache/' . md5("Image" . $item->id) . '_' . $size . '.jpg';
		return $item;
	}

    /**
     * parser a image in the content of article.
     *
     * @param poiter $row .
     * @return void
     */
    public function parseImages($row) {
        //Get first
        //Get k2 item image form media/k2/
        $row = $this->lookingForK2Image($row);
        if ($row->imageK2Image != '') {
            $row->mainImage = $row->imageK2Image;
			$row->thumbnail = $row->mainImage;
            return $row;
        }

        $text = $row->introtext;
        $row->mainImage = $this->_defaultThumb;
        $regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/Us";

        if (!$this->_params->get('check_image_exist', 1)) {
            preg_match($regex, $text, $matches);
            $images = (count($matches)) ? $matches : array();
            if (count($images)) {
                $row->mainImage = $images[1];
                $row->introtext = str_replace($images[0], "", $row->introtext);
            }
        } else {
            preg_match_all($regex, $text, $matches);
            foreach ($matches[1] as $key => $match) {
                @$url = getimagesize($match);
                if (is_array($url)) {
                    $row->mainImage = $match;
                    $row->introtext = str_replace($matches[0][$key], "", $row->introtext);
                    break;
                }
            }
        }
		$row->thumbnail = $row->mainImage;
        return $row;
    }

    /* ---------------------------------- */

    /**
     * get the list of k2 items
     *
     * @param JParameter $params;
     * @return Array
     */
    public function getList() {

		// check k2 existing
        if (!is_file(JPATH_SITE . "/components/com_k2/k2.php")) {
			return array();
		}
        $params = &$this->_params;

        // Init vars
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
        $limit = $params->get('limit_items', 12);

        // Set ordering
        $ordering = explode('-', $ordering);
        if (trim($ordering[0]) == 'rand') {
            $ordering = ' RAND() ';
        } else {
            $ordering = $ordering[0] . ' ' . $ordering[1];
        }

        $user = JFactory::getUser();
		$isThumb = $params->get('image_thumb', 1);
        $thumbWidth = (int) $params->get('thumbnail_width', 280);
        $thumbHeight = (int) $params->get('thumbnail_height', 150);
		$quality = (int) $params->get('image_quality', 80);
        $db = JFactory::getDBO();
        $date = JFactory::getDate();
        $now = $date->toSQL();
        $dateFormat = $params->get('date_format', 'DATE_FORMAT_LC3');
        $show_author = $params->get('show_author', 0);


		// main query
        $query = "SELECT  a.*, c.name as category_title,
						c.id as categoryid, c.alias as categoryalias, c.params as categoryparams, c.image as category_image" . " FROM #__k2_items as a" . " LEFT JOIN #__k2_categories c ON c.id = a.catid";

        $query .= " WHERE a.published = 1" . " AND a.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")" . " AND a.trash = 0" . " AND c.published = 1" . " AND c.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")" . " AND c.trash = 0 ";

		// filter by user
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

		// filter by featured params
        if ($params->get('show_featured', '1') == 2) {
            $query .= " AND a.featured != 1";
        } elseif ($params->get('show_featured', '1') == 3) {
            $query .= " AND a.featured = 1";
        }

		// valid publish date
        $jnow = JFactory::getDate();
        $now = $jnow->toSql();
        $nullDate = $db->getNullDate();
        $query .= " AND ( a.publish_up = " . $db->Quote($nullDate) . " OR a.publish_up <= " . $db->Quote($now) . " )";
        $query .= " AND ( a.publish_down = " . $db->Quote($nullDate) . " OR a.publish_down >= " . $db->Quote($now) . " )";

		//filter by language
		$languageTag = JFactory::getLanguage()->getTag();
		$query .= " AND a.language IN (".$db->quote($languageTag).",".$db->quote('*').") AND c.language IN (".$db->quote($languageTag).",".$db->quote('*').")" ;

		//Get data
        $data = array();
        $source = trim($this->_params->get('source', 'k2_category'));
		$catids = self::getCategoryIds();
        if($source == 'k2_category' && !empty($catids) && $this->_params->get('limit_items_for_each')) {
			$db->setQuery('SELECT id from #__k2_categories where id in ('.implode($catids,',').') order by ordering');
			$catids = $db->loadColumn();
            foreach ($catids as $catid){
                $condition = ' AND  a.catid = ' . $catid . ' ';
                $db->setQuery($query . $condition . ' ORDER BY ' . $ordering . ($limit ? ' LIMIT ' . $limit : ''));
                $data = array_merge($data, $db->loadObjectlist());
            }
        } else {
            $condition = $this->buildConditionQuery($source,$catids);
            $db->setQuery($query . $condition . ' ORDER BY ' . $ordering . ($limit ? ' LIMIT ' . $limit : ''));
            $data = array_merge($data, $db->loadObjectlist());
        }

		// Rebuild data
        foreach ($data as $key => &$item) {

			// authorise
            if (in_array($item->access, $user->getAuthorisedViewLevels())) {
                $item->link = JRoute::_(K2HelperRoute::getItemRoute($item->id . ':' . $item->alias, $item->catid . ':' . $item->categoryalias));
            } else {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }

			// format date
            $item->date = JHtml::_('date', $item->created, JText::_($dateFormat));

            //cut title
            if ($limit_title_by == 'word' && $titleMaxChars > 0) {

                $item->title_cut = self::substrword($item->title, $titleMaxChars, $replacer, $isStrips);
            } elseif ($limit_title_by == 'char' && $titleMaxChars > 0) {

                $item->title_cut = self::substring($item->title, $titleMaxChars, $replacer, $isStrips);
            }

			// escape html characters
			$item->title = htmlspecialchars($item->title);

			// import joomla content prepare plugin
            if($params->get('content_plugin')){
                $item->introtext = JHtml::_('content.prepare', $item->introtext);
            }

			// cut introtext
            if ($limitDescriptionBy == 'word') {

                $item->description = self::substrword($item->introtext, $maxDesciption, $replacer, $isStrips, $stringtags);
            } else {

                $item->description = self::substring($item->introtext, $maxDesciption, $replacer, $isStrips, $stringtags);
            }
            $item->categoryLink = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid . ':' . urlencode($item->categoryalias))));

			// get author name & link
            if ($show_author) {
                if (!empty($item->created_by_alias)) {
                    $item->author = $item->created_by_alias;
                } else {
                    $author = JFactory::getUser($item->created_by);
                    $item->author = $author->name;
                }
                $item->authorLink = JRoute::_(K2HelperRoute::getUserRoute($item->created_by));
            }

			// make thumbnail
            $item->thumbnail = '';
            $item->mainImage = '';
            if ($params->get('show_image')) {
                $item = $this->generateImages($item, $isThumb, $quality);
            }
			//get extrafields
			$showExtrafields = $params->get('show_extrafields', array());
			if(count($showExtrafields) && $item->extra_fields){
				$item->extra_fields = json_decode($item->extra_fields);
				if(count($item->extra_fields)){
					$exIds = array();
					foreach($item->extra_fields as $ex){
						$exIds[] = $ex->id;
					}
					$exIds = implode(',', $exIds);
					$query = 'SELECT name FROM #__k2_extra_fields WHERE id IN (' . $exIds . ') ORDER BY ordering';
					$db->setQuery($query);
					$rs = $db->loadObjectlist();
					
					foreach($item->extra_fields as $key => &$ex){
						$ex->name = $rs[$key]->name;
					}
				}
			}
			
        }

        return $data;
    }

	/* build condition for query */
    public function buildConditionQuery($source,$catids = '') {

        if ($source == 'k2_category') {
            if (empty($catids)){
                $condition = '';
            }else{
				$condition = ' AND  a.catid IN("' . implode('","', $catids) . '")';
			}
        } else if($source == 'k2_article_ids'){
            if (!$this->_params->get('k2_article_ids', '')) {
                return '';
            }

            $ids = preg_split('/,/', $this->_params->get('k2_article_ids', ''));

            $tmp = array();

            foreach ($ids as $id) {

                $tmp[] = (int) trim($id);
            }
            $condition = " AND a.id IN('" . implode("','", $tmp) . "')";
        } else{
			$tagIds = $this->_params->get('k2_tags');
			
			if(!$tagIds) return '';
			else{
				if(!is_array($tagIds)) $tagIds = array($tagIds);
				$condition = ' AND a.id IN (SELECT itemID FROM #__k2_tags_xref WHERE tagID IN ('. implode(',',$tagIds) .'))';
			}
		}
        return $condition;
    }

	/*get category id for query function */
	function getCategoryIds(){
		$catids = array();
		if($this->_params->get('auto_category') && JRequest::getVar('option')=='com_k2'){
			if(JRequest::getVar('view')=='itemlist'){
				$catid = JRequest::getInt('id');
				if($catid) $catids = array($catid);
			}
			else{
				if(JRequest::getVar('view')=='item'){
					$db = JFactory::getDBO();
					$itemid = JRequest::getInt('id');
					$query = 'SELECT catid from #__k2_items where id='.$itemid;
					$db->setQuery($query);
					$catid = $db->loadResult();
					if($catid) $catids = array($catid);
				}
			}
		}
		if(empty($catids)){
			$catids = $this->_params->get('k2_category', array());
		}
		
		//since 2.4.2
		if($this->_params->get('sub_categories', 0) && count($catids)){
			$db = JFactory::getDBO();
			$parents = $catids;
			foreach($parents as $c){
				$db->setQuery('SELECT id FROM #__k2_categories WHERE parent = ' . $c);
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
