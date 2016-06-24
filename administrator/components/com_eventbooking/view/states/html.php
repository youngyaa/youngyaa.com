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

class EventbookingViewStatesHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$options                          = array();
		$options[]                        = JHtml::_('select.option', 0, ' - ' . JText::_('EB_SELECT_COUNTRY') . ' - ', 'id', 'name');
		$options                          = array_merge($options, EventbookingHelperDatabase::getAllCountries());
		$this->lists['filter_country_id'] = JHtml::_('select.genericlist', $options, 'filter_country_id', ' onchange="submit();" ', 'id', 'name', $this->state->filter_country_id);

		return true;
	}
}