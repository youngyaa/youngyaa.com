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
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Bt_mediaViewDetail extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    protected $jinput;
    protected $layout;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();

        $user = JFactory::getUser();
        $this->jinput = $app->input;
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->layout = $this->jinput->get('layout');
        if ($this->layout == 'edit') {
            $this->form = $this->get('Form');
        }

        $this->params = $app->getParams();
        $this->item->category = Bt_mediaHelper::getCategorybyId($this->item->cate_id);
        $registry = new JRegistry();
        $registry->loadString($this->item->params);
        $this->item->params = $registry;
        $this->params->merge($registry);
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        $accessAllow = $user->getAuthorisedViewLevels();

        if (!in_array($this->item->access, $accessAllow)) {
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false), JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
        }
        $theme = $this->params->get('theme', 'default');
        $this->_addPath('template', JPATH_COMPONENT . '/themes/default/layout');
        $this->_addPath('template', JPATH_COMPONENT . '/themes/' . $theme . '/layout');
        $this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_media/default/layout');
        $this->_addPath('template', JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_bt_media/' . $theme . '/layout');

        if ($this->layout == 'edit') {
            if (!$this->item->id) {
                $authorised = $user->authorise('core.create', 'com_bt_media');
            } else {
                $authorised = $user->authorise('core.edit', 'com_bt_media.detail.' . $this->item->id) or ($user->authorise('core.edit.own', 'com_bt_media.detail.' . $this->item->id) and ($user->name == $this->item->created_by));
            }
            if ($authorised !== true) {
                throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
            } else {
                $this->setLayout("mediadetail_edit");
            }
        } else {
            $model = $this->getModel();
            $model->hit();

            $this->setLayout("mediadetail");
            if ($this->jinput->get('ajax')) {
                $this->setLayout("popup");
                parent::display($tpl);
                exit;
            }
        }
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

        $newTitle = '';
        if ($catid && $this->item->cate_id != $catid) {
            if ($this->layout == 'edit') {
                if (JFactory::getApplication()->input->get('id')) {
                    $pathway->addItem(JText::_('Edit media item'));
                } else {
                    $pathway->addItem(JText::_('Add new media item'));
                }
            } else {
                $category = $this->item->category;
                if ($category->parent_id && $category->parent_id != $catid) {
                    $parent = Bt_mediaHelper::getCategorybyId($category->parent_id);
                    $pathway->addItem($parent->name, JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $parent->parent_id . ':' . $parent->alias));
                }
                $pathway->addItem($category->name, JRoute::_('index.php?option=com_bt_media&view=category&catid=' . $category->parent_id . ':' . $category->alias));
                $pathway->addItem($this->item->name, '');
                $newTitle = $this->item->params->get('page_title', $this->item->name);
            }
        } else {
            $newTitle = $this->item->params->get('page_title', $this->item->name);
            $pathway->addItem($this->item->name, '');
        }
        if ($newTitle)
            $title = $newTitle;

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
