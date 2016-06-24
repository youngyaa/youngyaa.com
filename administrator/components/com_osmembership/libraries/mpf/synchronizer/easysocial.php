<?php
/**
 * @package     MPF
 * @subpackage  Synchronizer
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
class MPFSynchronizerEasysocial
{

	public function getData($userId, $mappings)
	{
		$data = array();
		$db   = JFactory::getDbo();
		$sql  = 'SELECT cf.title , fv.data FROM #__social_fields AS cf ' . ' INNER JOIN #__social_fields_data AS fv ' .
			' ON cf.id = fv.field_id ' . ' WHERE fv.uid = ' . $userId;
		$db->setQuery($sql);
		$rows = $db->loadObjectList('title');
		foreach ($mappings as $fieldName => $mappingFieldName)
		{
			if ($mappingFieldName && isset($rows[$mappingFieldName]))
			{
				if (stristr($rows[$mappingFieldName]->data, ","))
				{
					$rows[$mappingFieldName]->data = explode(',', $rows[$mappingFieldName]->data);
				}
				$data[$fieldName] = $rows[$mappingFieldName]->data;
			}
		}

		return $data;
	}
}