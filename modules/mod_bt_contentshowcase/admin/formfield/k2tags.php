<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.3
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

class JFormFieldK2Tags extends JFormFieldList{
	public $type = 'k2tags';
	
	public function getInput(){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT enabled FROM #__extensions WHERE name = ' . $db->quote('com_k2'));
		$isEnabled = $db->loadResult();
        if ($isEnabled) { 
			return parent::getInput();
		}else{
			return '<span class="' . $this->element['class'] . '">' . JText::_('K2_IS_NOT_ENABLED_OR_INSTALLED') . '</span>';
		}
	}
	
	public function getOptions(){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id, name FROM #__k2_tags WHERE published = 1');
		$rs = $db->loadObjectList();
		$options = array();
		if($rs){
			foreach($rs as $tag){
				$options[] = JHtml::_('select.option', $tag->id, $tag->name);
			}
		}
		return $options;
	}
}