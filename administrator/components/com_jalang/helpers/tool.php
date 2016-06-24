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


class JalangHelperTool extends JObject
{
	/**
	 * @var code of source language (in Joomla system)
	 */
	public $fromLangTag;
	/**
	 * @var code of destination language (in Joomla system)
	 */
	public $toLangTag;

	public function __construct()
	{
		$this->params = JComponentHelper::getParams('com_jalang');
	}

	final public function moveAllTables($from, $to) {
		$this->fromLangTag = $from;
		$this->toLangTag = $to;

		if(!$this->fromLangTag) {
			$this->sendOutput(JText::_('SOURCE_LANGUAGE_IS_NOT_SPECIFIED_OR_NOT_SUPPORTED'));
			return false;
		}
		if(!$this->toLangTag) {
			$this->sendOutput(JText::_('DESTINATION_LANGUAGE_IS_NOT_SPECIFIED_OR_NOT_SUPPORTED'));
			return false;
		}
		JalangHelper::createLanguageContent($this->toLangTag);


		$adapters = JalangHelperContent::getListAdapters();
		$db = JFactory::getDbo();
		foreach($adapters as $adapter) {
			$component = $adapter['title'];
			$adapter = JalangHelperContent::getInstance($adapter['name']);
			if($adapter->table_type == 'native') {
				if(!$adapter->language_field) continue;
				$this->sendOutput('<h3>'.JText::sprintf('START_TO_MOVE_ITEM_FROM_THE_COMPONENT', $component).'</h3>');

				//Only support native table now
				$query = $db->getQuery(true);
				$query->update('#__'.$adapter->table);
				if($adapter->language_mode == 'id') {
					$query->set($db->quoteName($adapter->language_field).'='.$db->quote(JalangHelper::getLanguageIdFromCode($this->toLangTag)));
					$query->where($db->quoteName($adapter->language_field).'='.$db->quote(JalangHelper::getLanguageIdFromCode($this->fromLangTag)));

				} else {
					$query->set($db->quoteName($adapter->language_field).'='.$db->quote($this->toLangTag));
					$query->where($db->quoteName($adapter->language_field).'='.$db->quote($this->fromLangTag));
				}

				$db->setQuery($query);
				$db->execute();

				$num_items = $db->getAffectedRows();
				$this->sendOutput(JText::sprintf('NUM_ITEMS_ARE_MOVED', $num_items).'<br />');
			}
		}
	}

	final public function removeLanguage($languageTag) {

		if(!$languageTag || $languageTag == '*') {
			$this->sendOutput(JText::_('SOURCE_LANGUAGE_IS_NOT_SPECIFIED_OR_NOT_SUPPORTED'));
			return false;
		}

		if($languageTag == JalangHelper::getDefaultLanguage()) {
			$this->sendOutput(JText::_('ALERT_CANNOT_REMOVE_DEFAULT_LANGUAGE'));
			return false;
		}

		$langId = JalangHelper::getLanguageIdFromCode($languageTag);
		$parts = explode('-', $languageTag);
		$langCode = strtolower(trim($parts[0]));

		$adapters = JalangHelperContent::getListAdapters();
		$db = JFactory::getDbo();
		foreach($adapters as $adapter) {
			$component = $adapter['title'];
			$adapter = JalangHelperContent::getInstance($adapter['name']);
			$table = '#__'.$adapter->table;

			$this->sendOutput('<h3>'.JText::sprintf('START_TO_REMOVE_ITEM_FROM_THE_COMPONENT', $component).'</h3>');
			if($adapter->table_type == 'native' || $adapter->table_type == 'table_ml') {
				if(!$adapter->language_field) continue;

				if($adapter->language_mode == 'id') {
					$where = $db->quoteName($adapter->language_field).'='.$db->quote($langId);
				} else {
					$where = $db->quoteName($adapter->language_field).'='.$db->quote($languageTag);
				}

				if($adapter->table_type == 'native') {
					//delete association data
					$query = "DELETE FROM #__associations
						WHERE id IN (
							SELECT ".$db->quote($adapter->primarykey)."
							FROM ".$table."
							WHERE `context` = ".$db->quote($adapter->associate_context)."
							AND ".$where."
						)";
					$db->setQuery($query);
					$db->execute();
				}

				//delete items
				$query = $db->getQuery(true);
				$query->delete($table);
				$query->where($where);

				$db->setQuery($query);
				$db->execute();

				$num_items = $db->getAffectedRows();
				$this->sendOutput(JText::sprintf('NUM_ITEMS_ARE_REMOVED', $num_items).'<br />');
			} elseif ($adapter->table_type == 'alias') {
				$query = $db->getQuery(true);
				$query->delete($table);
				$query->where($db->quoteName($adapter->alias_field).' LIKE '.$db->quote('%-'.$langCode));

				$db->setQuery($query);
				$db->execute();

				$num_items = $db->getAffectedRows();
				$this->sendOutput(JText::sprintf('NUM_ITEMS_ARE_REMOVED', $num_items).'<br />');
			} elseif ($adapter->table_type == 'table') {
				$tableml = $this->getLangTable($table, $languageTag);
				$query = "DROP TABLE ".$db->quoteName($tableml);
				$db->setQuery($query);
				$db->execute();
				$this->sendOutput(JText::sprintf('DROP_THE_LANGUAGE_TABLE', $tableml).'<br />');
			}
		}

		//remove content language?
	}
	
	// Fix associcate language error when translate and import to FLEXI
	public static function fixFLEXIAssLanguage($rows) {
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$assoc = JLanguageAssociations::isEnabled();
		}
		else
		{
			$assoc = JLanguageMultilang::isEnabled();
		}
		
		if ($assoc)
		{
			$db = JFactory::getDbo();
			for ($i=0;$i<count($rows);$i++) {
				$associations = JalangHelperTool::getAssociations('com_content', '#__content', 'com_content.item', $rows[$i]->id);
				if ($associations != false) {
					$j=0;
					foreach ($associations as $tag => $association)
					{
						if ($j==0) { // Only get the first tag language.
							$assocciation = explode(':', $association->id);
							$query = 'UPDATE #__flexicontent_items_tmp SET `lang_parent_id` = '.$assocciation[0].' WHERE `id` = '.$rows[$i]->id;
							$db->setQuery($query);
							$db->query();
							
							$query = 'UPDATE #__flexicontent_items_ext SET `lang_parent_id` = '.$assocciation[0].' WHERE `item_id` = '.$rows[$i]->id;
							$db->setQuery($query);
							$db->query();
							
							$query = 'UPDATE #__flexicontent_items_tmp SET `language` = "'.$tag.'" WHERE `id` = '.$assocciation[0];
							$db->setQuery($query);
							$db->query();
							
							$query = 'UPDATE #__flexicontent_items_ext SET `language` = "'.$tag.'" WHERE `item_id` = '.$assocciation[0];
							$db->setQuery($query);
							$db->query();
						}
						$j++;
					}
				}
			}
		}
	}
	
	public static function getAssociations($extension, $tablename, $context, $id, $pk = 'id', $aliasField = 'alias', $catField = 'catid')
	{
		$associations = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('c2.language'))
			->from($db->quoteName($tablename, 'c'))
			->join('INNER', $db->quoteName('#__associations', 'a') . ' ON a.id = c.id AND a.context=' . $db->quote($context))
			->join('INNER', $db->quoteName('#__associations', 'a2') . ' ON a.key = a2.key')
			->join('INNER', $db->quoteName($tablename, 'c2') . ' ON a2.id = c2.' . $db->quoteName($pk));

		// Use alias field ?
		if (!empty($aliasField))
		{
			$query->select(
				$query->concatenate(
					array(
						$db->quoteName('c2.' . $pk),
						$db->quoteName('c2.' . $aliasField)
					),
					':'
				) . ' AS ' . $db->quoteName($pk)
			);
		}
		else
		{
			$query->select($db->quoteName('c2.' . $pk));
		}

		// Use catid field ?
		if (!empty($catField))
		{
			$query->join('INNER', $db->quoteName('#__categories', 'ca') . ' ON ' . $db->quoteName('c2.' . $catField) . ' = ca.id AND ca.extension = ' . $db->quote($extension))
				->select(
					$query->concatenate(
						array('ca.id', 'ca.alias'),
						':'
					) . ' AS ' . $db->quoteName($catField)
				);
		}

		$query->where('c.' . $pk . ' = ' . (int) $id);

		$db->setQuery($query);

		try
		{
			$items = $db->loadObjectList('language');
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500);
		}

		if ($items)
		{
			foreach ($items as $tag => $item)
			{
				// Do not return itself as result
				if ((int) $item->{$pk} != $id)
				{
					$associations[$tag] = $item;
				}
			}
		}

		return $associations;
	}

	/**
	 * @param string $table - table name
	 * @param string $languageTag - Joomla content language tag
	 */
	public function getLangTable($table, $languageTag) {
		return $table . '_' . strtolower(str_replace('-', '_', $languageTag));
	}

	public function sendOutput($content) {
		echo $content . "<br />";
		@ob_flush();
		@flush();
		/*@ob_end_flush();
		@ob_flush();
		@flush();
		@ob_start();*/
	}
}