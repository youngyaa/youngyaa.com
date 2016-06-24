<?php
class RADSynchronizerJoomla
{

	public function getData($userId, $mappings)
	{
		$data = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('profile_key, profile_value')
			->from('#__user_profiles')
			->where('user_id=' . $userId);
		$db->setQuery($query);
		$rows = $db->loadObjectList('profile_key');		
		foreach ($mappings as $fieldName => $mappingFieldName)
		{
			$key = 'profile.' . $mappingFieldName;
			if ($mappingFieldName && isset($rows[$key]))
			{
				$data[$fieldName] = json_decode($rows[$key]->profile_value, true);
			}
		}
		return $data;
	}	
}