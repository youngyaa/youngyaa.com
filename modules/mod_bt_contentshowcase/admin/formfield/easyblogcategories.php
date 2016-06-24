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
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldEasyblogcategories extends JFormFieldList {
	protected $type = 'easyblogcategories'; //the form field type
    var $options = array();
	
	protected function getInput(){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT enabled, manifest_cache FROM #__extensions WHERE name = 'com_easyblog'");
		$rs = $db->loadAssoc();
		if($rs['enabled']){
			$cache = new JRegistry($rs['manifest_cache']);
			$this->version = $cache->get('version');
			return parent::getInput();
		}else{
			return '<span class="' . $this->element['class'] . '">' . JText::_('EASYBLOG_IS_NOT_ENABLED_OR_INSTALLED') . '</span>';
		}
	}
    protected function getOptions() {
		if(version_compare($this->version, '5.0', 'ge')){
			require_once( JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php' );
			$catModel = EB::getModel( 'Categories' );
		}else{
			require_once( JPATH_ROOT . '/components/com_easyblog/constants.php' );
			require_once( EBLOG_HELPERS . '/helper.php' );
			$catModel	 = EasyBlogHelper::getModel( 'Categories' );
		}
		
		$parentCat	= $catModel->getParentCategories('', 'all', true);
		if(! empty($parentCat))
		{
			for($i = 0; $i < count($parentCat); $i++)
			{
				$parent = $parentCat[$i];
				//reset
				$parent->childs = null;
				$this->buildNestedCategories($parent, false, true);
			}
			foreach($parentCat as $category)
			{
				$this->options[] = JHtml::_('select.option', $category->id, JText::_( $category->title ));
				$this->accessNestedCategories($category, '0');
			}
		}
		return $this->options;
	}
	
	public function buildNestedCategories(& $parent, $ignorePrivate = false, $isPublishedOnly = true)
	{
		$catModel			= EasyBlogHelper::getModel( 'Categories' );
		$childs				= $catModel->getChildCategories($parent->id, $isPublishedOnly);
		$accessibleCatsIds	= EasyBlogHelper::getAccessibleCategories( $parent->id );

		if(! empty($childs))
		{
			for($j = 0; $j < count($childs); $j++)
			{
				$child	= $childs[$j];
				$child->childs = null;

				if(! $ignorePrivate)
				{
					if( count( $accessibleCatsIds ) > 0)
					{
						$access = false;
						foreach( $accessibleCatsIds as $canAccess)
						{
							if( $canAccess->id == $child->id)
							{
								$access = true;
							}
						}
						if( !$access ) continue;
					}
					else
					{
						continue;
					}
				}

				if(! $this->buildNestedCategories($child, $ignorePrivate, $isPublishedOnly))
				{
					$parent->childs[]   = $child;
				}
			}// for $j
		}
		else
		{
			return false;
		}

	}
	/**
	 * Get nested categories
	 */
	public function accessNestedCategories($category, $deep='0')
	{
		if(isset($category->childs) && is_array($category->childs))
		{
			$sup	= '- ';
			$deep++;
			for($d=0; $d < $deep; $d++)
			{
				$sup .= '- ';
			}
			for($j	= 0; $j < count($category->childs); $j++)
			{
				$child	= $category->childs[$j];
				$this->options[] = JHtml::_('select.option', $child->id, $sup . JText::_( $child->title ));
				EasyBlogHelper::accessNestedCategories($child, $deep);
			}
		}
		else
		{
			return false;
		}
	}
	
 	// bind function to save
    function bind( $array, $ignore = '' ) {
        if (key_exists( 'field-name', $array ) && is_array( $array['field-name'] )) {
        	$array['field-name'] = implode( ',', $array['field-name'] );
        }
        
        return parent::bind( $array, $ignore );
    }
}
