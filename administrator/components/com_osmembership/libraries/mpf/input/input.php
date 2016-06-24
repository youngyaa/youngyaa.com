<?php
/**
 * @package     MPF
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
define('MPF_INPUT_ALLOWRAW', 2);
define('MPF_INPUT_ALLOWHTML', 4);
/**
 * Extends JInput class to allow getting raw data from Input object. This can be removed when we don't provide support for Joomla 2.5.x
 *
 * @package       MPF
 * @subpackage    Input
 * @since         1.0
 */
class MPFInput extends JInput
{
	/**
	 * Constructor.
	 *
	 * @param   array $source  Source data (Optional, default is $_REQUEST)
	 * @param   array $options Array of configuration parameters (Optional)
	 *
	 */
	public function __construct($source = null, array $options = array())
	{
		if (!isset($options['filter']))
		{
			//Set default filter so that getHtml can be returned properly
			$options['filter'] = JFilterInput::getInstance(null, null, 1, 1);
		}

		parent::__construct($source, $options);

		if (get_magic_quotes_gpc())
		{
			$this->data = self::stripSlashesRecursive($this->data);
		}
	}

	/**
	 *
	 * Get data from the input
	 *
	 * @param int $mask
	 *
	 * @return Ambigous
	 */
	public function getData($mask = MPF_INPUT_ALLOWHTML)
	{
		if ($mask & 2)
		{
			return $this->data;
		}

		return $this->filter->clean($this->data, null);
	}

	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed $name Name of the input object to retrieve.
	 *
	 * @return  JInput  The request input object
	 *
	 * @since   11.1
	 */
	public function __get($name)
	{
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = 'JInput' . ucfirst($name);

		if (class_exists($className))
		{
			$this->inputs[$name] = new $className(null, $this->options);

			return $this->inputs[$name];
		}

		$superGlobal = '_' . strtoupper($name);

		if (isset($GLOBALS[$superGlobal]))
		{
			$this->inputs[$name] = new MPFInput($GLOBALS[$superGlobal], $this->options);

			return $this->inputs[$name];
		}

	}

	/**
	 * Check to see if a variable is avaialble in the input or not
	 *
	 * @param string $name the variable name
	 *
	 * @return boolean
	 */
	public function has($name)
	{
		if (isset($this->data[$name]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Helper method to Un-quotes a quoted string
	 *
	 * @param string $value
	 *
	 * @return Ambigous <multitype:, string>
	 */
	protected static function stripSlashesRecursive($value)
	{
		$value = is_array($value) ? array_map(array('MPFInput', 'stripSlashesRecursive'), $value) : stripslashes($value);

		return $value;
	}
}