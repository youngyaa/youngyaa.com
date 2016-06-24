<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewSubscribersHtml extends MPFViewList
{

	protected function prepareView()
	{
		parent::prepareView();
		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('OSM_ALL'));
		$options[] = JHtml::_('select.option', 0, JText::_('OSM_PENDING'));
		$options[] = JHtml::_('select.option', 1, JText::_('OSM_ACTIVE'));
		$options[] = JHtml::_('select.option', 2, JText::_('OSM_EXPIRED'));
		$options[] = JHtml::_('select.option', 3, JText::_('OSM_CANCELLED_PENDING'));
		$options[] = JHtml::_('select.option', 4, JText::_('OSM_CANCELLED_REFUNDED'));
		$lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="inputbox" onchange="submit();" ', 'value', 'text', $this->state->published);
		$this->config = OSMembershipHelper::getConfig();
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
			$canDo = call_user_func(array('MPFHelper', 'getActions'), $this->viewConfig['option'], $this->name, $this->state);
		}
		$languagePrefix = $this->viewConfig['language_prefix'];
		JToolBarHelper::title(JText::_(strtoupper($languagePrefix . '_' . $this->name . '_MANAGEMENT')), 'link ' . $this->name);
		if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			JToolBarHelper::editList('edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences($this->viewConfig['option']);
		}
	}
}