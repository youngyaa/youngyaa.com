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

if(class_exists('JFormFieldTag')){
	class JFormFieldJoomlaTags extends JFormFieldTag {
		public $type = 'joomlatags';
		
	}
}else{
	class JFormFieldJoomlaTags extends JFormField {
		public $type = 'joomlatags';
		protected function getInput(){
			return '<span class="' . $this->element['class'] . '">' . JText::_('ERROR_CURRENT_VERSION_DOES_NOT_SUPPORT_TAGS_COMPONENT') . '</span>';
		}
	}
}