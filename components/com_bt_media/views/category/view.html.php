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
jimport('joomla.application.component.helper');

/**
 * View to edit
 */
class Bt_mediaViewCategory extends JViewLegacy {

    protected $state;
    protected $item;
    protected $items;
    protected $form;
    protected $params;
    protected $user;
    protected $pagination;
    protected $media;
    protected $jinput;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $this->jinput = $app->input;
        $this->user = JFactory::getUser();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->items = $this->get('Items');
        $this->params = $app->getParams();
        $this->pagination = $this->get('Pagination');
        $this->media = new stdClass();

        $registry = new JRegistry();
        $registry->loadString($this->item->params);
        $this->item->params = $registry;
        $this->params->merge($registry);

        $mediaModel = JModelList::getInstance('List', 'Bt_mediaModel');
        $this->media->items = $mediaModel->getItems();
        $this->media->pagination = $mediaModel->getPagination();
        $this->media->state = $mediaModel->getState();
        $this->media->params = $app->getParams('com_bt_media');


        $model = $this->getModel();
        $model->hit();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }


        //Check access permission
        $accessAllow = $this->user->getAuthorisedViewLevels();
        if (!in_array($this->item->access, $accessAllow)) {
            return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        if ($this->_layout == 'edit') {

            $authorised = $this->user->authorise('core.create', 'com_bt_media');

            if ($authorised !== true) {
                throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
            }
        }

        $theme = $this->params->get('theme', 'default');
        $this->_addPath('template', JPATH_COMPONENT . '/themes/default/layout');
        $this->_addPath('template', JPATH_COMPONENT . '/themes/' . $theme . '/layout');
        $this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_media/default/layout');
        $this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_media/' . $theme . '/layout');
        $this->setLayout("category");

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $pathway = $app->getPathway();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        $catid = @$menu->query['catid'];
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('com_bt_media_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');

        if ($this->item->id != $catid) {
            if ($this->item->parent_id && $this->item->parent_id != $catid) {
                $parent = Bt_mediaHelper::getCategorybyId($this->item->parent_id);
                $pathway->addItem($parent->name, JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $this->item->parent_id . ':' . $parent->alias));
            }
            $pathway->addItem($this->item->name, '');
            $newTitle = $this->item->params->get('page_title', $this->item->name);
            if ($newTitle)
                $title = $newTitle;
        }


        if (empty($title)) {
            $title = $this->item->name;
        }
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('metadesc')) {
            $this->document->setDescription($this->params->get('metadesc'));
        } elseif (!$this->params->get('metadesc') && $this->item->description) {
			$description = $this->item->description;
            $this->document->setDescription(JFilterOutput::cleanText($description));
        } elseif (!$this->params->get('metadesc') && !$this->item->description && $this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('metakey')) {
            $this->document->setMetadata('keywords', $this->params->get('metakey'));
        } elseif (!$this->params->get('metakey') && $this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }


        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        } elseif (!$this->params->get('robots') && $this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($this->params->get('rights')) {
            $this->document->setMetaData('rights', $this->params->get('rights'));
        }

        if ($app->getCfg('MetaAuthor') == '1' && $this->params->get('author')) {
            $this->document->setMetaData('author', $this->params->get('author'));
        }
    }

}
