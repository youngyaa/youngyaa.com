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
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');


if (version_compare(JVERSION, '3.0', 'ge')) {

    class Bt_mediaController extends JControllerLegacy {

        public function __construct($config = array()) {
            $this->input = JFactory::getApplication()->input;

            // Bt media frontpage Editor article proxying:
            if ($this->input->get('view') === 'list' && $this->input->get('layout') === 'modal') {
                //JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
                $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            }

            parent::__construct($config);
        }

    }

} else if (version_compare(JVERSION, '2.5', 'ge')) {

    class Bt_mediaController extends JController {

        public function __construct($config = array()) {
            $this->input = JFactory::getApplication()->input;

            // Bt media frontpage Editor article proxying:
            if ($this->input->get('view') === 'list' && $this->input->get('layout') === 'modal') {
                //JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
                $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            }

            parent::__construct($config);
        }

    }

} else {

    class Bt_mediaController extends JController {

        public function __construct($config = array()) {
            $this->input = JFactory::getApplication()->input;

            // Bt media frontpage Editor article proxying:
            if ($this->input->get('view') === 'list' && $this->input->get('layout') === 'modal') {
                //JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
                $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            }

            parent::__construct($config);
        }

    }

}