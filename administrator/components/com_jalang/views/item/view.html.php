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
class JalangViewItem extends JViewLegacy
{

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->item		= $this->get('Item');

		$adapter = JalangHelper::getHelperContent();
		if($adapter) {
			$this->primarykey = $adapter->primarykey;
			$this->alias_field = $adapter->alias_field;
			$this->translate_fields = $adapter->translate_fields;
			$this->reference_fields = $adapter->reference_fields;
		} else {
			$this->primarykey = null;
			$this->alias_field = null;
			$this->translate_fields = array();
			$this->reference_fields = array();
		}

		parent::display($tpl);
	}
}
