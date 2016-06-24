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

class EventbookingModelCategories extends RADModelList
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0)
			->set('filter_order', 'tbl.ordering');

		$listLength = (int) EventbookingHelper::getConfigValue('number_categories');
		if ($listLength)
		{
			$this->state->setDefault('limit', $listLength);
		}
	}

	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$rows = parent::getData();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row               = $rows[$i];
				$row->total_events = EventbookingHelper::getTotalEvent($row->id);
			}
			$this->data = $rows;
		}

		return $this->data;
	}

	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*');

		// Adding support for multilingual site
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('name', 'description'), $fieldSuffix);
		}

		return $this;
	}

	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$query->where('tbl.published=1')
			->where('tbl.parent=' . $this->state->id)
			->where('tbl.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');

		return $this;
	}
}