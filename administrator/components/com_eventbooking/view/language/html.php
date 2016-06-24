<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EventbookingViewLanguageHtml extends RADViewHtml
{
	/**
	 * All language items
	 *
	 * @var array
	 */
	protected $items;
	/**
	 * Model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	public function display()
	{
		$this->state = $this->model->getState();
		$languages   = $this->model->getSiteLanguages();
		$options     = array();
		$options[]   = JHtml::_('select.option', '', JText::_('Select Language'));
		foreach ($languages as $language)
		{
			$options[] = JHtml::_('select.option', $language, $language);
		}
		$lists['filter_language'] = JHtml::_('select.genericlist', $options, 'filter_language', ' class="inputbox"  onchange="submit();" ', 'value', 'text', $this->state->filter_language);
		$options                  = array();
		$options[]                = JHtml::_('select.option', 'com_eventbooking', JText::_('EB_FRONT_END_LANGUAGE'));
		$options[]                = JHtml::_('select.option', 'admin.com_eventbooking', JText::_('EB_BACK_END_LANGUAGE'));
		$lists['filter_item']     = JHtml::_('select.genericlist', $options, 'filter_item', ' class="inputbox"  onchange="submit();" ', 'value', 'text', $this->state->filter_item);
		$this->items              = $this->model->getData();
		$this->pagination         = $this->model->getPagination();
		$this->lists              = $lists;

		$this->addToolbar();
		parent::display();
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Translation management'), 'generic.png');
		JToolBarHelper::addNew('new_item', 'New Item');
		JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel');
	}
}