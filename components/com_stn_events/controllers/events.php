<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Events list controller class.
 *
 * @since  1.6
 */
class Stn_eventsControllerEvents extends Stn_eventsController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Events', $prefix = 'Stn_eventsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	public function eventdetailbyid(){
		die('aaa');
		define( '_JEXEC', 1 );
		ob_start();	
		ob_end_clean();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data = $_POST;
		$query = "SELECT * FROM #__stn_events_timeslotes WHERE id = ".$data['eventId'];
		//a7rtg_stn_events_timeslotes
		//echo $query; die;
		$db->setQuery($query);
		$result = $db->loadAssoc();
		//$db->execute();
		//echo '<pre>';
		//print_r($result);
		echo json_encode($result);
		die;
	}
}
