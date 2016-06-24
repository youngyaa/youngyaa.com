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

class EventbookingModelLocation extends EventbookingModelList
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

		$this->state->insert('location_id', 'int', 0);
	}

	/**
	 * Get location information from database, using for add/edit page
	 *
	 * @return JTable|mixed
	 */
	public function getLocationData()
	{
		if ($this->state->id)
		{
			return EventbookingHelperDatabase::getLocation($this->state->id);
		}
		else
		{
			$row          = $this->getTable();
			$config       = EventbookingHelper::getConfig();
			$row->country = $config->default_country;

			return $row;
		}
	}

	/**
	 * Method to store a location
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function store($data)
	{
		$row          = $this->getTable();
		$user         = JFactory::getUser();
		$row->user_id = $user->id;
		if ($data['id'])
		{
			$row->load($data['id']);
		}
		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		// Calculate location here
		$ch = curl_init();
		if (!$row->lat && !$row->long)
		{
			$address = array();
			if ($row->address)
			{
				$address[] = $row->address;
			}
			if ($row->city)
			{
				$address[] = $row->city;
			}
			if ($row->state)
			{
				$address[] = $row->state;
			}
			if ($row->zip)
			{
				$address[] = $row->zip;
			}
			if ($row->country)
			{
				$address[] = $row->country;
			}
			$address = implode('+', $address);
			$address = str_replace(' ', '+', $address);
			$address = urlencode($address);
			$url     = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' . $address;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			$arrData = json_decode($data, true);
			if ($arrData['status'] == 'OK')
			{
				$location  = $arrData['results'][0]['geometry']['location'];
				$row->lat  = $location['lat'];
				$row->long = $location['lng'];
			}
		}
		if (!$row->store())
		{
			$this->setError($this->db->getErrorMsg());

			return false;
		}

		return true;
	}

	/**
	 * Delete the selected location
	 *
	 * @param array $cid
	 *
	 * @return boolean
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$cids  = implode(',', $cid);
			$query->delete('#__eb_locations')
				->where('id IN (' . $cids . ')')
				->where('user_id = ' . (int) JFactory::getUser()->id);
			$db->setQuery($query);
			if (!$db->execute())
			{
				return false;
			}
		}

		return true;
	}
} 