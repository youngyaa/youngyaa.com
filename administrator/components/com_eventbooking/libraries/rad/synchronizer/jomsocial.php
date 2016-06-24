<?php

class RADSynchronizerJomsocial
{

	public function getData($userId, $mappings)
	{
		$data = array();
		$db = JFactory::getDbo();
		$sql = 'SELECT cf.fieldcode , fv.value FROM #__community_fields AS cf ' . ' INNER JOIN #__community_fields_values AS fv ' .
			 ' ON cf.id = fv.field_id ' . ' WHERE fv.user_id = ' . $userId;
		$db->setQuery($sql);
		$rows = $db->loadObjectList('fieldcode');
		foreach ($mappings as $fieldName => $mappingFieldName)
		{
			if ($mappingFieldName && isset($rows[$mappingFieldName]))
			{
				$data[$fieldName] = $rows[$mappingFieldName]->value;
			}
		}		
		return $data;
	}

	public function saveData($userId, $data, $config)
	{
		
	}
}