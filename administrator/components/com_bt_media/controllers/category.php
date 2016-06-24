<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.1.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Category controller class.
 */
class Bt_mediaControllerCategory extends JControllerForm {

    function __construct() {
        $this->view_list = 'categories';
        parent::__construct();
    }

}