<?php
/**
 * @package     MPF
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Class for handling model states
 *
 * @package       MPF
 * @subpackage    Model
 * @since         2.0
 */
class MPFModelState
{

	/**
	 * The state data container
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Set data for a state
	 *
	 * @param string $name The name of state
	 * @param fix    $value
	 */
	public function set($name, $value)
	{
		if (isset($this->data[$name]))
		{
			$this->data[$name]->value = $value;
		}
	}

	/**
	 * Retrieve data for a state
	 *
	 * @param string Name of the state
	 *
	 * @param mixed  Default value if no data has been set for that state
	 *
	 * @return mixed The state value
	 */
	public function get($name, $default = null)
	{
		$result = $default;
		if (isset($this->data[$name]))
		{
			$result = $this->data[$name]->value;
		}

		return $result;
	}

	/**
	 * Insert a new state
	 *
	 * @param string The name of the state
	 *
	 * @param mixed  Filter, the name of filter which will be used to sanitize the state value using JFilterInput
	 *
	 * @param mixed  The default value of the state
	 *
	 * @return MPFModelState
	 */
	public function insert($name, $filter, $default = null)
	{
		$state             = new stdClass();
		$state->name       = $name;
		$state->filter     = $filter;
		$state->value      = $default;
		$state->default    = $default;
		$this->data[$name] = $state;

		return $this;
	}

	/**
	 * Remove an existing state
	 *
	 * @param string The name of the state which will be removed
	 *
	 * @return MPFModelState
	 */
	public function remove($name)
	{
		unset($this->data[$name]);

		return $this;
	}

	/**
	 * Reset all state data and revert to the default state
	 *
	 * @param boolean If TRUE use defaults when resetting. If FALSE then null value will be used.Default is TRUE
	 *
	 * @return MPFModelState
	 */
	public function reset($default = true)
	{
		foreach ($this->data as $state)
		{
			$state->value = $default ? $state->default : null;
		}

		return $this;
	}

	/**
	 * Set the state data
	 *
	 * This function will only filter values if we have a value. If the value
	 * is an empty string it will be filtered to NULL.
	 *
	 * @param array An associative array of state values by name
	 *
	 * @return MPFModelState
	 */
	public function setData(array $data)
	{
		$filterInput = JFilterInput::getInstance();

		// Special code for handle ajax ordering in Joomla 3
		if (!empty($data['filter_full_ordering']))
		{
			$parts                = explode(' ', $data['filter_full_ordering']);
			$sort                 = $parts[0];
			$direction            = isset($parts[1]) ? $parts[1] : '';
			$data['filter_order'] = $sort;
			$data['$sort']        = $direction;
		}

		// Filter data
		foreach ($data as $key => $value)
		{
			if (isset($this->data[$key]))
			{
				$filter = $this->data[$key]->filter;

				// Only filter if we have a value
				if ($value !== null)
				{
					if ($value !== '')
					{
						$value = $filterInput->clean($value, $filter);
					}
					else
					{
						$value = null;
					}
					$this->data[$key]->value = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Get the state data
	 *
	 * This function only returns states that have been been set.
	 *
	 * @return array An associative array of state values by name
	 */
	public function getData()
	{
		$data = array();

		foreach ($this->data as $name => $state)
		{
			$data[$name] = $state->value;
		}

		return $data;
	}

	/**
	 * Get default value of a state
	 *
	 * @param string $name
	 *
	 * @return mixed the default state value
	 */
	public function getDefault($name)
	{
		return $this->data[$name]->default;
	}

	/**
	 * Change default value (and therefore value) of an existing state
	 *
	 * @param $name
	 * @param $default
	 *
	 * @return MPFModelState to support chaining
	 */
	public function setDefault($name, $default)
	{
		if (isset($this->data[$name]))
		{
			$this->data[$name]->default = $default;
			$this->data[$name]->value   = $default;
		}

		return $this;
	}

	/**
	 * Magic method to get state value
	 *
	 * @param string
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * Set state value
	 *
	 * @param string The user-specified state name.
	 *
	 * @param mixed  The user-specified state value.
	 *
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->set($name, $value);
	}

	/**
	 * Test existence of a state variable
	 *
	 * @param string
	 *
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	/**
	 * Unset a state value
	 *
	 * @param string The column key.
	 *
	 * @return void
	 */
	public function __unset($name)
	{
		if (isset($this->data[$name]))
		{
			$this->data[$name]->value = $this->data[$name]->default;
		}
	}
}