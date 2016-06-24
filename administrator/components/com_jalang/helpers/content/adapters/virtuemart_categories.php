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

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/category.php')) {
	//Register if K2 is installed
	JalangHelperContent::registerAdapter(
		__FILE__,
		'virtuemart_categories',
		3,
		JText::_('VIRTUEMART_CATEGORY'),
		JText::_('VIRTUEMART_CATEGORY')
	);


	class JalangHelperContentVirtuemartCategories extends JalangHelperContent
	{
		public function __construct($config = array())
		{
			$this->table_type = 'table';
			$this->table = 'virtuemart_categories';
			$this->primarykey = 'virtuemart_category_id';
			$this->edit_context = 'virtuemart.edit.category';
			$this->associate_context = 'virtuemart.category';
			$this->translate_fields = array('category_name', 'category_description', 'metakey', 'metadesc');
			$this->translate_filters = array();
			$this->alias_field = '';
			$this->title_field = 'category_name';
			parent::__construct($config);
		}

		public function getEditLink($id) {
			return 'index.php?option=com_virtuemart&view=category&task=edit&cid='.$id;
		}

		/**
		 * Returns an array of fields the table can be sorted by
		 */
		public function getSortFields()
		{
			return array(
				'a.category_name' => JText::_('JGLOBAL_TITLE')
			);
		}

		/**
		 * Returns an array of fields will be displayed in the table list
		 */
		public function getDisplayFields()
		{
			return array(
				'a.virtuemart_category_id' => 'JGRID_HEADING_ID',
				'a.category_name' => 'JGLOBAL_TITLE'
			);
		}
	}
}
