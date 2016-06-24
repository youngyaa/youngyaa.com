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
 * View class for a list of Stn_events.
 *
 * @since  1.6
 */
class Stn_eventsViewEvents extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

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
		$this->items = $this->get('Items');
		$this->forms = $this->get('Form');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		Stn_eventsHelpersStn_events::addSubmenu('events');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Stn_eventsHelpersStn_events::getActions();

		JToolBarHelper::title(JText::_('COM_STN_EVENTS_TITLE_EVENTS'), 'events.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/event';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::save('saveeventset', 'Save');
			}
		}
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`title`' => JText::_('COM_STN_EVENTS_EVENTS_TITLE'),
			'a.`desription`' => JText::_('COM_STN_EVENTS_EVENTS_DESRIPTION'),
			'a.`startdate`' => JText::_('COM_STN_EVENTS_EVENTS_STARTDATE'),
			'a.`enddate`' => JText::_('COM_STN_EVENTS_EVENTS_ENDDATE'),
		);
	}
}
