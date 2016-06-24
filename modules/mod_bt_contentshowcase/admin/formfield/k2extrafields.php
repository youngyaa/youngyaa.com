<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		2.4.5
 * @created		March 2015
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

class JFormFieldK2extrafields extends JFormField{
	public $type = 'k2extrafields';
	
	public function getInput(){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT enabled FROM #__extensions WHERE name = ' . $db->quote('com_k2'));
		$isEnabled = $db->loadResult();
        if ($isEnabled) { 
			$db->setQuery(
			'SELECT e.id, e.name, g.name as group_name FROM #__k2_extra_fields as e 
			LEFT JOIN #__k2_extra_fields_groups as g  ON e.group = g.id
			WHERE e.published = 1
			ORDER BY g.id ASC, e.ordering ASC');
			$rs = $db->loadObjectList();
			
			$options = array();
			$html = '<select multiple="multiple" id="' . $this->id . '" name="'. $this->name .'" class="' . $this->element['class'] . '">';
			
			$currentGroup = '';
			if(!$this->value) $this->value = array();
			if($rs){
				foreach($rs as $ex){
					
					if($currentGroup == ''){
						$html .= '<optgroup label="' . $ex->group_name . '">';
						$currentGroup = $ex->group_name;
					}else{
						if($ex->group_name != $currentGroup){
							$html .= '</optgroup>';
							$html .= '<optgroup label="' . $ex->group_name . '">';
							$currentGroup = $ex->group_name;
						}
					}	
					$html .= '<option value="' . $ex->id .'"' . (in_array($ex->id, $this->value) ? ' selected="selected" ' : '' ) . '>' . $ex->name . '</option>';	
					
					
				}
				$html .= '</optgroup>';
			}else{
				$html.= '<option value="">' . JText::_('NO_EXTRA_FIELD') . '</option>';
			}
			
			$html .= '</select>';
			return $html;
		}else{
			return '<span class="' . $this->element['class'] . '">' . JText::_('K2_IS_NOT_ENABLED_OR_INSTALLED') . '</span>';
		}
	}
	
	public function getOptions(){
		
		return $options;
	}
}