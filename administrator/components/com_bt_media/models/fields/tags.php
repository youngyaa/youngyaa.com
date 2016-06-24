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

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldTags extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'tags';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
// Initialize variables.
        JLoader::register('Bt_mediaLegacyHelper', JPATH_ADMINISTRATOR . '/components/com_bt_media/helpers/legacy.php');

        $document = JFactory::getDocument();
        $document->addStyleDeclaration('/*Autocomplete*/
        .container { width: 800px; margin: 0 auto; }

        .autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; }
        .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
        .autocomplete-selected { background: #F0F0F0; }
        .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }');
        $header = $document->getHeadData();
        JHTML::_('behavior.framework');
        $loadJquery = true;
        $loadDefault = true;
        $loadAutoComplete = true;
        foreach ($header['scripts'] as $scriptName => $scriptData) {
            if (substr_count($scriptName, '/jquery')) {
                $loadJquery = false;
                break;
            }
        }
        foreach ($header['scripts'] as $scriptName => $scriptData) {
            if (substr_count($scriptName, '/default')) {
                $loadDefault = false;
                break;
            }
        }
        foreach ($header['scripts'] as $scriptName => $scriptData) {
            if (substr_count($scriptName, '/jquery.autocomplete')) {
                $loadAutoComplete = false;
                break;
            }
        }
        if ($loadJquery) {
            $document->addScript(JURI::root() . 'administrator/components/com_bt_media/assets/js/jquery-1.8.3.js');
        }
        if ($loadAutoComplete) {
            $document->addScript(JURI::root() . 'administrator/components/com_bt_media/lib/jQueryAutoComplete/jquery.autocomplete.js');
        }
        if ($loadDefault) {
            $document->addScript(JURI::root() . 'administrator/components/com_bt_media/assets/js/default.js');
        }
        $option = JFactory::getApplication()->input->get('option');
        $item_id = JFactory::getApplication()->input->get('id');
        $value = '';
        if ($item_id) {
            if ($option == 'com_bt_media') {
                $old_value = Bt_mediaLegacyHelper::getAllTagsItem($item_id);
                $tag_name = array();
                if ($old_value) {
                    foreach ($old_value as $tag) {
                        $tag_name[] = $tag->name;
                    }
                    $value = implode(", ", $tag_name);
                }
            } elseif ($option == 'com_menus') {
                $value = Bt_mediaLegacyHelper::getAllTagsMenu($item_id);
            }
        }
        $html = array();

        $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '"/>';
        $document->addScriptDeclaration('
            $BM(document).ready(function (){
            var tags = [' . Bt_mediaLegacyHelper::prepareTagsData() . '];
                        $BM("#' . $this->id . '").autocomplete({
                            lookup: tags,
                            autoSelectFirst: true,
                            delimiter:","
                        });
                        });
         ');

        return implode($html);
    }

}