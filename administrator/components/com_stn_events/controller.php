<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Class Stn_eventsController
 *
 * @since  1.6
 */
class Stn_eventsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return   JController This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = JFactory::getApplication()->input->getCmd('view', 'events');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	public function publishunpublh()
	{
		define( '_JEXEC', 1 );
		ob_start();	
		ob_end_clean();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data = $_POST;
		$sql = "UPDATE #__stn_events_dates SET status = '".$data['status']."' WHERE id = ".$data['id'];
		//echo $sql;
		$db->setQuery($sql);
		$db->execute();
		die();
	}
}
