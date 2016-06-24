<?php
/**
 * @package     RAD
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Base class for a Joomla Model
 *
 * @package       RAD
 * @subpackage    Model
 * @since         2.0
 */
class RADModel
{
	/**
	 * Full name of the component com_foobar
	 *
	 * @var string
	 */
	protected $option;

	/**
	 * The model name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	/**
	 * The database driver.
	 *
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 * Model configuration data
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The name of the database table
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Ignore request or not. If set to Yes, model states won't be set when it is created
	 *
	 * @var boolean
	 */
	public $ignoreRequest = false;

	/**
	 * Remember model states value in session
	 *
	 * @var boolean
	 */
	public $rememberStates = false;

	/**
	 * @param string $name   The name of model to instantiate
	 *
	 * @param string $prefix Prefix for the model class name, ComponentnameModel
	 *
	 * @param array  $config Configuration array for model
	 *
	 * @return RADModel A model object
	 */
	public static function getInstance($name, $prefix, $config = array())
	{
		$name  = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$class = ucfirst($prefix) . ucfirst($name);
		if (!class_exists($class))
		{
			if (isset($config['default_model_class']))
			{
				$class = $config['default_model_class'];
			}
			else
			{
				$class = 'RADModel';
			}
		}

		return new $class($config);
	}

	/**
	 * Constructor
	 *
	 * @param array $config An array of configuration options
	 *
	 * @throws Exception
	 */
	public function __construct($config = array())
	{

		if (isset($config['option']))
		{
			$this->option = $config['option'];
		}
		else
		{
			$className = get_class($this);
			$pos       = strpos($className, 'Model');
			if ($pos !== false)
			{
				$this->option = 'com_' . strtolower(substr($className, 0, $pos));
			}
			else
			{
				throw new Exception(JText::_('Could not detect the component for model'), 500);
			}
		}

		// Set the model name
		if (isset($config['name']))
		{
			$this->name = $config['name'];
		}
		else
		{
			$className = get_class($this);
			$pos       = strpos($className, 'Model');
			if ($pos !== false)
			{
				$this->name = substr($className, $pos + 5);
			}
			else
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}
		}

		if (isset($config['db']))
		{
			$this->db = $config['db'];
		}
		else
		{
			$this->db = JFactory::getDbo();
		}

		// Set the model state
		if (isset($config['state']))
		{
			$this->state = $config['state'];
		}
		else
		{
			$this->state = new RADModelState();
		}

		$component = substr($this->option, 4);

		if (empty($config['class_prefix']))
		{
			$config['class_prefix'] = ucfirst($component);
		}

		if (empty($config['language_prefix']))
		{
			$config['language_prefix'] = strtoupper($component);
		}

		if (isset($config['table']))
		{
			$this->table = $config['table'];
		}
		else
		{
			if (isset($config['table_prefix']))
			{
				$tablePrefix = $config['table_prefix'];
			}
			else
			{
				$tablePrefix = '#__' . $component . '_';
			}
			$this->table = $tablePrefix . strtolower(RADInflector::pluralize($this->name));
		}


		if (isset($config['ignore_request']))
		{
			$this->ignoreRequest = $config['ignore_request'];
		}

		if (isset($config['remember_states']))
		{
			$this->rememberStates = $config['remember_states'];
		}

		$this->config = $config;

		//Add include path to find table class
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/table');
	}

	/**
	 * Populate model state from input
	 *
	 * @param RADInput $input
	 */
	public function populateState($input)
	{
		$data = $input->getData();

		// Try to get the state properties data from user session
		if ($this->rememberStates)
		{
			$properties = $this->state->getProperties();
			if (count($properties))
			{
				$context = $this->option . '.' . $input->get('view', $this->config['default_view']) . '.';
				foreach ($properties as $property)
				{
					$newState = $this->getUserStateFromRequest($input, $context . $property, $property);
					if ($newState != null)
					{
						$data[$property] = $newState;
					}
				}
			}
		}

		$this->setState($data);
	}

	/**
	 * Get JTable object for the model
	 *
	 * @param string $name
	 *
	 * @return JTable
	 */
	public function getTable($name = '')
	{
		if (!$name)
		{
			$name = RADInflector::singularize($this->name);
		}

		return JTable::getInstance($name, $this->config['class_prefix'] . 'Table');
	}

	/**
	 * Set the model state properties
	 *
	 * @param string|array The   name of the property, an array
	 *
	 * @param              mixed The value of the property
	 *
	 * @return RADModel
	 */
	public function setState($property, $value = null)
	{
		$changed = false;
		if (is_array($property))
		{
			foreach ($property as $key => $value)
			{
				if (isset($this->state->$key) && $this->state->$key != $value)
				{
					$changed = true;
					break;
				}
			}

			$this->state->setData($property);
		}
		else
		{
			if (isset($this->state->$property) && $this->state->$property != $value)
			{
				$changed = true;
			}

			$this->state->$property = $value;
		}

		if ($changed)
		{
			// Reset the data
			$this->data  = null;
			$this->total = null;
		}

		return $this;
	}

	/**
	 * Get the model state properties
	 *
	 * If no property name is given then the function will return an associative array of all properties.
	 *
	 * @param string $property The name of the property
	 *
	 * @param string $default  The default value
	 *
	 * @return mixed <string, RADModelState>
	 */
	public function getState($property = null, $default = null)
	{
		$result = $default;

		if (is_null($property))
		{
			$result = $this->state;
		}
		else
		{
			if (isset($this->state->$property))
			{
				$result = $this->state->$property;
			}
		}

		return $result;
	}

	/**
	 * Reset all cached data and reset the model state to it's default
	 *
	 * @param boolean If TRUE use defaults when resetting. Default is TRUE
	 *
	 * @return RADModel
	 */
	public function reset($default = true)
	{
		$this->data  = null;
		$this->total = null;
		$this->state->reset($default);
		$this->query = $this->db->getQuery(true);

		return $this;
	}

	/**
	 * Get the dbo
	 *
	 * @return JDatabaseDriver
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Get name of the model
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Supports a simple form Fluent Interfaces.
	 * Allows you to set states by
	 * using the state name as the method name.
	 *
	 * For example : $model->filter_order('name')->filter_order_Dir('DESC')->limit(10)->getData();
	 *
	 * @param string Method name
	 *
	 * @param array  Array containing all the arguments for the original call
	 *
	 * @return RADModel
	 */
	public function __call($method, $args)
	{
		if (isset($this->state->$method))
		{
			return $this->set($method, $args[0]);
		}

		return null;
	}


	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   RADInput $input   The input object
	 * @param   string   $key     The key of the user state variable.
	 * @param   string   $request The name of the variable passed in a request.
	 * @param   string   $default The default value for the variable if not found. Optional.
	 * @param   string   $type    Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  object  The request user state.
	 */
	protected function getUserStateFromRequest($input, $key, $request, $default = null, $type = 'none')
	{
		$app = JFactory::getApplication();

		$currentState = $app->getUserState($key, $default);
		$newState     = $input->get($request, null, $type);

		// Save the new value only if it was set in this request.
		if ($newState !== null)
		{
			$app->setUserState($key, $newState);
		}
		else
		{
			$newState = $currentState;
		}

		return $newState;
	}

	/**
	 * Clean the cache
	 *
	 * @param   string  $group     The cache group
	 * @param   integer $client_id The ID of the client
	 *
	 * @return  void
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf    = JFactory::getConfig();
		$options = array(
			'defaultgroup' => ($group) ? $group : $this->option,
			'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
		// Trigger the onContentCleanCache event.
		if (!empty($this->eventCleanCache))
		{
			$dispatcher = JDispatcher::getInstance();;
			$dispatcher->trigger($this->event_clean_cache, $options);
		}
	}

}
