<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ('');
error_reporting(0);
JFactory::getDocument()->addStylesheet(JUri::base(true) . '/media/com_eventbooking/assets/css/style.css', 'text/css', null, null);
require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/database.php';
EventbookingHelper::loadLanguage();

$input        = JFactory::getApplication()->input;
$showCategory = $params->get('show_category', 1);
$showLocation = $params->get('show_location', 0);

$categoryId = $input->getInt('category_id', 0);
$locationId = $input->getInt('location_id', 0);
$text       = $input->getString('search');
if (empty($text))
{
	$text = JText::_('EB_SEARCH_WORD');
}
$text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
//Build Category Drodown
if ($showCategory)
{
	$db          = JFactory::getDbo();
	$query       = $db->getQuery(true);
	$fieldSuffix = EventbookingHelper::getFieldSuffix();
	$query->select('id, parent, parent AS parent_id')
		->select("name" . $fieldSuffix . " AS name, name" . $fieldSuffix . " AS title")
		->from('#__eb_categories')
		->where('published = 1')
		->where('`access` IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
		->order('name');
	$db->setQuery($query);
	$rows     = $db->loadObjectList();
	$children = array();
	if ($rows)
	{
		// first pass - collect children
		foreach ($rows as $v)
		{
			$pt   = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
	}
	$list      = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
	$options   = array();
	$options[] = JHTML::_('select.option', 0, JText::_('EB_SELECT_CATEGORY'));
	foreach ($list as $listItem)
	{
		$options[] = JHTML::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;' . $listItem->treename);
	}
	$lists['category_id'] = JHtml::_('select.genericlist', $options, 'category_id', array(
		'option.text.toHtml' => false,
		'list.attr'          => 'class="inputbox category_box" ',
		'option.text'        => 'text',
		'option.key'         => 'value',
		'list.select'        => $categoryId,
	));
}

//Build location dropdown
if ($showLocation)
{	
	$options   			  = array();
	$options[]            = JHtml::_('select.option', 0, JText::_('EB_SELECT_LOCATION'), 'id', 'name');
	$options              = array_merge($options, EventbookingHelperDatabase::getAllLocations());
	$lists['location_id'] = JHtml::_('select.genericlist', $options, 'location_id', ' class="inputbox location_box" ', 'id', 'name', $locationId);
}
$itemId = (int) $params->get('item_id');
if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}

$layout = $params->get('layout_type', 'default');

require(JModuleHelper::getLayoutPath('mod_eb_search', $params->get('module_layout', 'default')));