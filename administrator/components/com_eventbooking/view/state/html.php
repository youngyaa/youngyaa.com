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

class EventbookingViewStateHtml extends RADViewItem
{

	protected function prepareView()
	{
		parent::prepareView();

		$options   = array();
		$options[] = JHtml::_('select.option', 0, ' - ' . JText::_('EB_SELECT_COUNTRY') . ' - ', 'id', 'name');
		$options   = array_merge($options, EventbookingHelperDatabase::getAllCountries());

		$this->lists['country_id'] = JHtml::_('select.genericlist', $options, 'country_id', ' class="inputbox"', 'id', 'name', $this->item->country_id);
	}
}