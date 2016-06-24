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
class JalangViewTool extends JViewLegacy
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

		JalangHelper::addSubmenu('tool', $this->getLayout());
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
		//
		$this->adapters = JalangHelperContent::getListAdapters();
		
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

		if (JalangHelper::isInstalled('com_flexicontent')) {
			JToolBarHelper::custom('tool.bindFLEXI', 'copy', '', JText::_('Transfer to FLEXI'));
		}
		if(JalangHelper::isJoomla3x()) {
			JHtmlSidebar::setAction('index.php?option=com_jalang&view=tool');
		}
	}
}
