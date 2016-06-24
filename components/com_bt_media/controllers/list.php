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
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * List list controller class.
 */
class Bt_mediaControllerList extends Bt_mediaController {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'List', $prefix = 'Bt_mediaModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

}