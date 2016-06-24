<?php
/**
 * ------------------------------------------------------------------------
 * JA Multilingual Component for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

/**
 * View class for a list of articles.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jalang
 * @since       1.6
 */
class JalangViewItems extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		JalangHelper::addSubmenu('items');

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		if(JalangHelper::isJoomla3x()) {
			$this->sidebar = JHtmlSidebar::render();
		}
		
		$fields = array();
		$adapter = JalangHelper::getHelperContent();
		$this->adapter = $adapter;
		if($adapter) {
			$fields = $adapter->getDisplayFields();
		}
		
		$this->fields = $fields;
		$this->languages = JalangHelper::getListContentLanguages();
		$this->mainlanguage = $app->getUserState('com_jalang.mainlanguage', '*');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('TRANSLATION_MANAGER'), 'article.png');

		JToolbarHelper::preferences('com_jalang');

		if(JalangHelper::isJoomla3x()) {
			JHtmlSidebar::setAction('index.php?option=com_jalang&view=items');
		}
		
		$app = JFactory::getApplication();
		$itemtype = $app->getUserState('com_jalang.itemtype', 'content');
		$adapters = JalangHelperContent::getListAdapters();

		$options = array();
		$types = array();
		foreach ($adapters as $props) {
			$types[$props['name']] = $props['title'];
		}
		//Sort by Alphabet
		ksort($types);
		foreach($types as $name => $title) {
			$options[]	= JHtml::_('select.option', $name, $title);
		}

		$mainlanguage = $app->getUserState('com_jalang.mainlanguage', '*');
		if(JalangHelper::isJoomla3x()) {
			JHtmlSidebar::addFilter(
				JText::_('SELECT_ITEM_TYPE'),
				'itemtype',
				JHtml::_('select.options', $options, 'value', 'text', $itemtype)
			);

			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_LANGUAGE'),
				'mainlanguage',
				JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $mainlanguage)
			);
		} else {
			$this->filterByItemtype = JHtml::_('select.options', $options, 'value', 'text', $itemtype);
			$this->filterByLanguage = JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $mainlanguage);
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		$adapter = JalangHelper::getHelperContent();
		if(!$adapter) return array();
		
		return $adapter->getSortFields();
	}
}
