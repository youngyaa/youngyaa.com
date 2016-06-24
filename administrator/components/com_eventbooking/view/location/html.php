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
defined('_JEXEC') or die;

class EventbookingViewLocationHtml extends RADViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$countries = EventbookingHelperDatabase::getAllCountries();
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_COUNTRY'));
		foreach ($countries as $country)
		{
			$options[] = JHtml::_('select.option', $country->name, $country->name);
		}
		$this->lists['country'] = JHtml::_('select.genericlist', $options, 'country', ' class="inputbox" ', 'value', 'text', $this->item->country);

	}
}