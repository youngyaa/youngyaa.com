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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldAsset extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'asset';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getLabel() {
        return null;
    }

    protected function getInput() {
        $document = JFactory::getDocument();
        JHTML::_('behavior.framework');
        //Import css
        $document->addStyleSheet(JUri::root() . 'administrator/components/com_bt_media/assets/css/bt_media.css');
        $document->addStyleSheet(JUri::root() . 'administrator/components/com_bt_media/assets/css/bt.css');

        $document->addStyleSheet(JUri::root() . 'administrator/components/com_bt_media/assets/css/jquery-ui.css');
        $document->addStyleSheet(JUri::root() . 'administrator/components/com_bt_media/lib/uploadify/uploadify.css');
        if (!version_compare(JVERSION, '3.0', 'ge')) {
            $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery-1.8.2.min.js');
            $document->addStyleSheet(JUri::root() . 'administrator/components/com_bt_media/assets/css/chosen.css');
            $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/chosen.jquery.min.js');
        }
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery-ui.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/lib/uploadify/jquery.uploadify.min.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/com_bt_media.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery.masonry.min.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/jquery.form.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/lib/jQueryAutoComplete/jquery.autocomplete.js');
        $document->addScript(JUri::root() . 'administrator/components/com_bt_media/assets/js/default.js');
        $document->addScriptDeclaration(
                'var btMediaCfg = {siteURL: "' . JUri::base() . '"}'
        );
        $html = '
            <div id="' . $this->id . '"></div>
            <script type="text/javascript">
                $BM(document).ready(function(){
                    $BM("#' . $this->id . '").parent().css("display","none");
                });
            </script>
';
        return $html;
    }

}