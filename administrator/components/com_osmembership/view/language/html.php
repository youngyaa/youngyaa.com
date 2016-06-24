<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewLanguageHtml extends MPFViewHtml
{

	public function display()
	{
		$model = $this->getModel();
		$state = $model->getState();
		$lang = $state->filter_language ? $state->filter_language : 'en-GB';
		$item = $state->filter_item ? $state->filter_item : '';
		$trans     = $model->getTrans($lang, $item);
		$languages = $model->getSiteLanguages();
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('Select Language'));
		foreach ($languages as $language)
		{
			$options[] = JHtml::_('select.option', $language, $language);
		}
		$lists['filter_language'] = JHtml::_('select.genericlist', $options, 'filter_language', ' onchange="submit();" ', 'value', 'text', $lang);
		$options       = array();
		$options[]     = JHtml::_('select.option', '', JText::_('--Select Item--'));
		$options[]     = JHtml::_('select.option', 'com_osmembership', JText::_('OS Membership'));
		$options[]     = JHtml::_('select.option', 'admin.com_osmembership', JText::_('OS Membership - Backend'));
		$lists['filter_item'] = JHtml::_('select.genericlist', $options, 'filter_item', ' onchange="submit();" ', 'value', 'text', $item);
		$this->trans   = $trans;
		$this->lists   = $lists;
		$this->lang    = $lang;
		$this->item    = $item;
		$this->state   = $state;

		parent::display();
	}
}