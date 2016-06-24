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

jimport('joomla.application.component.controllerform');

/**
 * Event controller class.
 *
 * @since  1.6
 */
class Stn_eventsControllerEvent extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'events';
		parent::__construct();
	}
	public function updateTimeSloates(){
		$mainframe = &JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$data = JRequest::get( 'post' );
		$model = $this->getModel();
		$return = $model->saveeventTimeSloates($data['jform']);
		if($return == 1){
			$success = JText::_('COM_STN_EVENTS_SAVE_SUCCESS_EVENT_SCLING');
			$mainframe->redirect(JRoute::_('index.php?option=com_stn_events'),$success,'message');
		} else {
			$mainframe->redirect($_SERVER['HTTP_REFERER'],$return,'error');
		}
	}
}
