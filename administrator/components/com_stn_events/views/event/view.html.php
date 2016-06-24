<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class Stn_eventsViewEvent extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->datedetl  = $this->get('DateDetail');
		$this->timeslotes  = $this->get('Timeslots');
		$this->timeslotdate  = $this->get('TimeslotDate');
		$this->timeslotgrabers  = $this->get('Timeslotgrabers');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		$canDo = Stn_eventsHelpersStn_events::getActions();
		JToolBarHelper::title(JText::_('COM_STN_EVENTS_TITLE_EVENT'), 'event.png');
		JToolBarHelper::apply('eventsloatsave', 'JTOOLBAR_APPLY');
		JToolBarHelper::custom('eventsloatnew', 'save-new.png', 'save-new_f2.png', 'Add New', false);
		JToolBarHelper::cancel('eventcancel', 'JTOOLBAR_CANCEL');
	}
}
