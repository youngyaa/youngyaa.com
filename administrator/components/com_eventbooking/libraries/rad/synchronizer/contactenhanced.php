<?php
class RADSynchronizerContactenhanced
{

	public function getData($userId, $mappings)
	{
		$data = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__ce_details')
			->where('user_id=' . $userId);
		$db->setQuery($query);
		$row = $db->loadObject();
		if ($row)
		{
			foreach ($mappings as $fieldName => $mappingFieldName)
			{
				if ($mappingFieldName && isset($row->{$mappingFieldName}))
				{
					$data[$fieldName] = $row->{$mappingFieldName};
				}
			}
		}

		return $data;
	}	
}