<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class Stn_eventsFrontendHelper
 *
 * @since  1.6
 */
class Stn_eventsFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_stn_events/models/' . strtolower($name) . '.php'))
		{
			$model = JModelLegacy::getInstance($name, 'Stn_eventsModel');
		}

		return $model;
	}
}
