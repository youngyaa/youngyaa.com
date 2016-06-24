<?php
/**
 * @package     MPF
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Joomla CMS Item View Class. This class is used to display details information of an item
 * or display form allow add/editing items
 *
 * @package     MPF
 * @subpackage  View
 * @since       2.0
 */
class MPFViewItem extends MPFViewHtml
{

	/**
	 * The model state.
	 *
	 * @var MPFModelState
	 */
	protected $state;

	/**
	 * The record which is being added/edited
	 *
	 * @var Object
	 */
	protected $item;

	/**
	 * The array which keeps list of "list" options which will be displayed on the form
	 *
	 * @var Array
	 */
	protected $lists;

	/**
	 * Method to display the view
	 *
	 * @see MPFViewHtml::display()
	 */
	public function display()
	{
		$this->prepareView();
		parent::display();
	}

	/**
	 * Method to prepare all the data for the view before it is displayed
	 */
	protected function prepareView()
	{
		$this->state = $this->model->getState();
		$this->item  = $this->model->getData();
		if (property_exists($this->item, 'published'))
		{
			$this->lists['published'] = JHtml::_('select.booleanlist', 'published', ' ', $this->item->published);
		}
		if (property_exists($this->item, 'access'))
		{
			$this->lists['access'] = JHtml::_('access.level', 'access', $this->item->access, ' ', false);
		}

		if (property_exists($this->item, 'language'))
		{
			$this->lists['language'] = JHtml::_('select.genericlist', JHtml::_('contentlanguage.existing', true, true), 'language', ' ', 'value', 'text', $this->item->language);
		}

		if ($this->isAdminView)
		{
			$this->addToolbar();
		}
		
		$this->languages = OSMembershipHelper::getLanguages();
	}

	/**
	 * Add toolbar buttons for add/edit item form
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
			$canDo = call_user_func(array('MPFHelper', 'getActions'), $this->viewConfig['option'], $this->name, $this->state);
		}
		if ($this->item->id)
		{
			$toolbarTitle = $this->viewConfig['language_prefix'] . '_' . $this->name . '_EDIT';
		}
		else
		{
			$toolbarTitle = $this->viewConfig['language_prefix'] . '_' . $this->name . '_NEW';
		}
		JToolBarHelper::title(JText::_(strtoupper($toolbarTitle)));

		if (($canDo->get('core.edit') || ($canDo->get('core.create'))) && !in_array('save', $this->hideButtons))
		{
			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		}

		if ($canDo->get('core.create') && !in_array('save2new', $this->hideButtons))
		{
			JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if ($this->item->id && $canDo->get('core.create') && !in_array('save2copy', $this->hideButtons))
		{
			JToolbarHelper::save2copy('save2copy');
		}

		if ($this->item->id)
		{
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		}
		else
		{
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');
		}
	}
}
