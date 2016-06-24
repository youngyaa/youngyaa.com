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
 * View to edit
 */
class Bt_mediaViewDetail extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $tags;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $canDo = Bt_mediaHelper::getActions();
        $canUploadImage = $canDo->get('media.upload.image');
        $canGetImage = $canDo->get('media.get.image');
        $canUploadVideo = $canDo->get('media.upload.video');
        $canGetVideo = $canDo->get('media.get.video');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $layout = $this->getLayout();
        if ($layout == 'edit') {
            if ($this->item->id && (int) $this->item->id > 0) {
                if (!$canDo->get('core.edit')) {
                    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
                }
            } else {
                if (!$canDo->get('core.create')) {
                    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
                }
            }
        }

        if ($layout == 'add' && (!$canDo->get('core.create') || (!$canGetImage && !$canUploadImage && !$canGetVideo && !$canUploadVideo))) {
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_bt_media&view=list', FALSE), JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
        }


        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        $canDo = Bt_mediaHelper::getActions();

        JToolBarHelper::title(JText::_('COM_BT_MEDIA_ITEMS_TITLE_MEDIAINFORMATION'), 'detail.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create')))) {
            JToolBarHelper::apply('detail.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('detail.save', 'JTOOLBAR_SAVE');
        }
//        if (!$checkedOut && ($canDo->get('core.create'))) {
//            //JToolBarHelper::custom('detail.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
//        }
        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            $layout = $this->getLayout();
            if ($layout == 'edit') {
                JToolBarHelper::custom('detail.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('detail.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('detail.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
