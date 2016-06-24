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

jimport('joomla.application.component.view');

/**
 * View class for a list of Bt_media.
 */
class Bt_mediaViewControlPanel extends BTView {

    protected $legacy;
    protected $canDo;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->legacy = Bt_mediaLegacyHelper::isLegacy();
        $this->canDo = Bt_mediaHelper::getActions();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }


        if ($this->legacy) {
            $this->addToolbar();
            $input = JFactory::getApplication()->input;
            $view = $input->getCmd('view', '');
            Bt_mediaHelper::addSubmenu($view);
        } else {
            Bt_mediaHelper::addSubmenu('controlpanel');
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {

        JToolBarHelper::title(JText::_('COM_BT_MEDIA_MENU_CPANEL_TITLE'), 'media.png');

        if ($this->canDo->get("core.admin")) {
            JToolBarHelper::preferences('com_bt_media');
        }

        if (!$this->legacy) {
            JHtmlSidebar::setAction('index.php?option=com_bt_media&view=controlpanel');
        }
    }

}
