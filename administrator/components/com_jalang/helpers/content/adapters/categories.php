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

JalangHelperContent::registerAdapter(
	__FILE__,
	'categories',
	1,//always translate category first
	JText::_('CONTENT_CATEGORIES'),
	JText::_('CONTENT_CATEGORIES')
);

class JalangHelperContentCategories extends JalangHelperContent
{
	public function __construct($config = array())
	{
		$this->table = 'categories';
		$this->edit_context = 'com_categories.edit.category';
		$this->associate_context = 'com_categories.item';
		$this->alias_field = 'alias';
		$this->translate_fields = array('title', 'description', 'metakey', 'metadesc');
		$this->translate_filters = array('`id` <> 1');
		$this->fixed_fields = array('asset_id'=> 0, 'version' => 1);
		$this->nested_field = 'parent_id';
		$this->nested_value = 1;
		parent::__construct($config);
	}

	public function getEditLink($id) {
		if($this->checkout($id)) {
			$row = $this->getRow($id);
			if(JalangHelper::isJoomla32()) {
				return 'index.php?option=com_categories&view=category&layout=modal&tmpl=component&id='.$id.'&extension='.$row->extension;
			} else {
				return 'index.php?option=com_categories&view=category&layout=edit&id='.$id.'&extension='.$row->extension;
			}
		}
		return false;
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 */
	public function getSortFields()
	{
		return array(
			'a.lft' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
	
	/**
	 * Returns an array of fields will be displayed in the table list
	 */
	public function getDisplayFields()
	{
		return array(
			'a.id' => 'JGRID_HEADING_ID',
			'a.title' => 'JGLOBAL_TITLE',
			'a.extension' => 'COMPONENT'
		);
	}

	public function afterTranslate(&$translator) {
		require_once( JPATH_ADMINISTRATOR . '/components/com_categories/tables/category.php' );
		$config = array();
		$modelCat = JTable::getInstance('Category', 'CategoriesTable', $config);
		$modelCat->rebuild();
	}
}