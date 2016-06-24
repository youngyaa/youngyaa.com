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
class Bt_mediaViewCategories extends BTView {

    protected $items;
    protected $pagination;
    protected $state;
    protected $legacy;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->legacy = Bt_mediaLegacyHelper::isLegacy();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        // Preprocess the list of items to find ordering divisions.
        foreach ($this->items as &$item) {
            if($item->parent_id == ""){
                $item->parent_id=0;
            }
            $this->ordering[$item->parent_id][] = $item->id;
        }

        if ($this->legacy) {
            $this->addToolbar();
            $input = JFactory::getApplication()->input;
            $view = $input->getCmd('view', '');
            Bt_mediaHelper::addSubmenu($view);
        } else {
            Bt_mediaHelper::addSubmenu('categories');
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
        require_once JPATH_COMPONENT . '/helpers/bt_media.php';

        $state = $this->get('State');
        $canDo = Bt_mediaHelper::getActions();

        JToolBarHelper::title(JText::_('COM_BT_MEDIA_MENU_CATEGORYS_TITLE'), 'categories.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/category';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('category.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('category.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('categories.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('categories.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('categories.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('categories.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && ($canDo->get('core.delete') || $canDo->get('media.delete.own'))) {
                JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('categories.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_bt_media');
        }

        if (!$this->legacy) {
            JHtmlSidebar::setAction('index.php?option=com_bt_media&view=categories');

            $this->extra_sidebar = '';

            JHtmlSidebar::addFilter(
                    JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
            );
            JHtmlSidebar::addFilter(
                    JText::_('COM_BT_MEDIA_CATEGORY_SELECT'), 'filter_parent', JHtml::_('select.options', Bt_mediaLegacyHelper::getCategoryOptions(), 'value', 'text', $this->state->get('filter.parent'))
            );
            JHtmlSidebar::addFilter(
                    JText::_('COM_BT_MEDIA_LANGUAGE_FILTER'), 'filter_language', JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
            );
            JHtmlSidebar::addFilter(
                    JText::_('COM_BT_MEDIA_ACCESS_FILTER'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
            );
        }
    }

    protected function getSortFields() {
        return array(
            'a.id' => JText::_('JGRID_HEADING_ID'),
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
        );
    }

}