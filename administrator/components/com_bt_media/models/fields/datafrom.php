<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.0.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

//jimport('joomla.html.html');
//jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldDataFrom extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'datafrom';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getOptions() {
        $options = array();
        if(JFactory::getUser()->authorise('media.upload.image', 'com_bt_media') || JFactory::getUser()->authorise('media.upload.video', 'com_bt_media')){
            $options['fromlocal'] = JText::_('COM_BT_MEDIA_UPLOAD');
        }
        if(JFactory::getUser()->authorise('media.get.image', 'com_bt_media') || JFactory::getUser()->authorise('media.get.video', 'com_bt_media')){
            $options['frominternet'] = JText::_('COM_BT_MEDIA_INTERNET');
        }
        return $options;
    }

    protected function getInput() {
        // Initialize variables.
        $html = array();
        $attr = '';
        if (!is_array($this->value)) {
            $this->value = explode(",", $this->value);
        }
        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
        }
        // Create a regular list.
        else {
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }
        $html[] = '<script type="text/javascript">';
        $html[] = '$Y = jQuery.noConflict();';
        $html[] = '$Y(document).ready(function(){';
        $html[] = '$Y("#' . $this->id . '").change(function(){';
        $html[] = 'var datasource = $Y("#' . $this->id . '").val();';
        $html[] = 'if(datasource == "fromlocal"){
                $Y("#jform_dataurl").parent().fadeOut(400);
            }';
        $html[] = 'if(datasource == "frominternet"){
                $Y("#jform_dataurl").parent().fadeIn(400);
            }';
        $html[] = '}).trigger("change")';
        $html[] = '})';
        $html[] = '</script>';
        return implode($html);
    }

}