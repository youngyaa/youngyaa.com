<?php
/**
 * @package     RAD
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Joomla CMS View List class, used to render list of records from front-end or back-end of your component
 *
 * @package      RAD
 * @subpackage   View
 * @since        2.0
 */
class RADViewList extends RADViewHtml
{

	/**
	 * The model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	/**
	 * List of records which will be displayed
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * The array which keeps list of "list" options which will used to display as the filter on the list
	 *
	 * @var Array
	 */
	protected $lists = array();

	/**
	 * Method to instantiate the view.
	 *
	 * @param   array $config The configuration data for the view
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to display a view
	 *
	 * @see RADViewHtml::display()
	 */
	public function display()
	{
		$this->prepareView();
		parent::display();
	}

	/**
	 * Prepare the view before it is displayed
	 *
	 */
	protected function prepareView()
	{
		$this->state      = $this->model->getState();
		$this->items      = $this->model->getData();
		$this->pagination = $this->model->getPagination();
		if ($this->isAdminView)
		{
			$this->lists['filter_state']    = str_replace('class="inputbox"', 'class="input-medium"', JHtml::_('grid.state', $this->state->filter_state));
			$this->lists['filter_access']   = JHtml::_('access.level', 'filter_access', $this->state->filter_access, 'onchange="submit();"', false);
			$this->lists['filter_language'] = JHtml::_('select.genericlist', JHtml::_('contentlanguage.existing', true, true), 'filter_language',
				' onchange="submit();" ', 'value', 'text', $this->state->filter_language);
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				EventbookingHelperHtml::renderSubmenu($this->name);
			}
			else
			{
				EventbookingHelper::addSubMenus($this->name);
			}
			$this->addToolbar();
		}
	}

	/**
	 * Method to add toolbar buttons
	 *
	 */
	protected function addToolbar()
	{
		$helperClass = $this->viewConfig['class_prefix'] . 'Helper';
		if (is_callable($helperClass . '::getActions'))
		{
			$canDo = call_user_func(array($helperClass, 'getActions'), $this->name, $this->state);
		}
		else
		{
			$canDo = call_user_func(array('RADHelper', 'getActions'), $this->option, $this->name, $this->state);
		}
		
		$languagePrefix = $this->viewConfig['language_prefix'];
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JToolBarHelper::title(JText::_(strtoupper($languagePrefix . '_' . RADInflector::singularize($this->name) . '_MANAGEMENT')), 'link ' . $this->name);
		}
		else 
		{
			JToolBarHelper::title(JText::_(strtoupper($languagePrefix . '_' . RADInflector::singularize($this->name) . '_MANAGEMENT')));
		}
		
		if ($canDo->get('core.create') && !in_array('add', $this->hideButtons))
		{
			JToolBarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit') && isset($this->items[0]) && !in_array('edit', $this->hideButtons))
		{
			JToolBarHelper::editList('edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete') && isset($this->items[0]) && !in_array('delete', $this->hideButtons))
		{
			JToolBarHelper::deleteList(JText::_($languagePrefix . '_DELETE_CONFIRM'), 'delete');
		}

		if ($canDo->get('core.edit.state') && !in_array('publish', $this->hideButtons))
		{
			if (isset($this->items[0]->published) || isset($this->items[0]->state))
			{
				JToolbarHelper::publish('publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences($this->option);
		}
	}
}
