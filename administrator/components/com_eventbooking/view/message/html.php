<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EventbookingViewMessageHtml extends RADViewHtml
{

	public function display()
	{
		$languages       = EventbookingHelper::getLanguages();
		$this->message   = EventbookingHelper::getMessages();
		$this->languages = $languages;
		$this->addToolbar();

		parent::display();
	}


	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Emails & Messages'), 'generic.png');
		JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel');
	}
}