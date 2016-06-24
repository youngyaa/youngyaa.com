<?php
/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin for Joomla 2.5 & J3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
/**
 *
 * JA Fetch for Map
 * @author JoomlArt
 *
 */
class JFormFieldJamap extends JFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    var $_type = 'Jamap';


    /**
     *
     * Construction Fetch
     */
    function getInput()
    {
    	$func = (string) $this->element['function'];
    	if(!$func) {
    		$func = 'mapkey';
    	}
    	
    	if(method_exists($this, $func)) {
    		return call_user_func_array(array($this, $func), array());
    	}
    	return null;
    }


    /**
     * return - map_key, function="@map_key"
     */
    function mapkey()
    {
        //popup
        JHtml::_('JABehavior.jquery');
        JHtml::_('behavior.modal');
        //
        $doc = JFactory::getDocument();
        $path = JUri::root(true).'/plugins/system/jagooglemap/assets/';
        $doc->addStyleSheet($path . 'style.css?v=1');
        $doc->addScript($path . 'script.js?v=1');
        $doc->addScript($path . 'jagencode.js?v=1');
        //google map
        //$map_js = 'http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=' . $value;
        $map_js = '//maps.googleapis.com/maps/api/js?sensor=true&amp;key=' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');//v3
        $doc->addScript($map_js);
        //
        $html = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="input-xxlarge" value="'
            . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
        return $html;
    }


    /**
     * return - map_code, function="map_code"
     */
    function mapcode()
    {
        $paramname = $this->name;
        $id = $this->name;
        $cols = (isset($this->element['cols']) && $this->element['cols'] != '') ? 'cols="' . intval($this->element['cols']) . '"' : '';
        $rows = (isset($this->element['rows']) && $this->element['rows'] != '') ? 'rows="' . intval($this->element['rows']) . '"' : '';
        $value = $this->value ? $this->value : (string) $this->element['default'];

        $html = "";
        $html .= "\n\t<div>";
        $html .= "\n\t<a name=\"mapPreview\"></a>";
        $html .= "\n\t<textarea name=\"{$paramname}\" id=\"{$id}\" style=\"width:100%; max-width:650px; height: 100px;\" >{$value}</textarea><br />";
        $html .= "\n\t" . '<a href="javascript: CopyToClipboard(\'' . $id . '\');">' . JText::_('SELECT_ALL') . '</a>';
        $html .= "\n\t" . '&nbsp;|&nbsp;';
        $html .= "\n\t" . '<a id="jaMapPreview" href="#mapPreview" >' . JText::_('PREVIEW_MAP') . '</a>';
        $html .= '<div id="map-preview-container"></div>';
        $html .= '</div>';
        return $html;

    }
}
