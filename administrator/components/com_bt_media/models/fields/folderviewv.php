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
jimport('joomla.filesystem.folder');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldFolderViewv extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'folderviewv';

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {
        $html = array();

        //path to directory to scan
        $directory = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images';

        //get all files in specified directory
        $files = JFolder::folders($directory);

        $html[] = '<ul class="list-folder">';
        //print each file name
        foreach ($files as $file) {
            if ($file != 'bt_media') {
                $html[] = '<li class="fview" ondblclick="loadSubFolder(this, \'' . $file . '\')" onclick="select(this, \'' . $file . '\')"><img src="' . JURI::base() . 'components/com_bt_media/assets/images/folder.gif"/><br/>' . $file . '<input type="hidden" value=""/></li>';
            }
        }
        $html[] = '</ul>';
        $html[] = '<input type="checkbox" id="chkSubFolderv"/> <span class="get-sub-folder">Get file from sub folder</span>';
        $html[] = '<div id="btnUploadFromFolderv" class="custom-button bt-disable" style="float:left; margin-right:15px; margin-top: 20px;"><strong>Load Videos</strong></div>';

        // Let's get the id for the current item, either category or content item.
//        return implode($html);
        return implode($html);
    }

}