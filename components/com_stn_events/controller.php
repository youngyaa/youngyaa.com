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

jimport('joomla.application.component.controller');

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
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
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
	public function eventdetailbyid(){
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
	public function grabit(){
		define( '_JEXEC', 1 );
		ob_start();	
		ob_end_clean();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		if (!$user->guest) {
			$userid = $user->id;
			$username = $user->name;
			$email = $user->email;
		} else{
			$userid = 0;
			$username = 'Guest User';
			$email = 0;
		}
		$query = $db->getQuery(true);
		$data = $_POST;
		$query = "SELECT count(id) FROM #__stn_events_grabers WHERE timesloat_id = '".$data['eventId']."' AND user_id = '".$userid."'";
		$db->setQuery($query);
		$count = $db->loadResult();
		
		$query = "SELECT count(id) FROM #__stn_events_grabers WHERE timesloat_id = '".$data['eventId']."'";
		$db->setQuery($query);
		$count1 = $db->loadResult();
		
		$mainframe = JFactory::getApplication();
		$mailer = JFactory::getMailer();
		//echo $mainframe->getCfg('helpurl');
		
		$query = "SELECT * FROM #__stn_events_timeslotes WHERE id = '".$data['eventId']."'";
		$db->setQuery($query);
		$eventdata = $db->loadObject();
		date_default_timezone_set('Australia/Sydney');
		if($count == 0){
			$datetime = date('Y-m-d H:i:s');
			$query = $db->getQuery(true);
			$query = "INSERT INTO #__stn_events_grabers (`timesloat_id`, `user_id`, `created`) VALUES ('".$data['eventId']."', '".$userid."', '".$datetime."')";
			$db->setQuery($query);
			$db->execute();
			echo $count1;
		} else {
			echo 'alradyplay';
		}
		die;
	}
	
	public function mailawardWinner(){
		define( '_JEXEC', 1 );
		ob_start();	
		ob_end_clean();		
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$data = $_REQUEST;
		$query = "SELECT * FROM #__stn_events_timeslotes WHERE id = '".$data['eventId']."'";
		$db->setQuery($query);
		$eventdata = $db->loadObject();
		//echo '<pre>';
		//print_r($eventdata); //die;
		date_default_timezone_set('Australia/Sydney');
		/* Admin Email Format */
		
		$mailer = JFactory::getMailer();
		$body = "<table>";
		$body .= "<tr><td>Winner : </td><td>".$user->name."</td></tr>";
		$body .= "<tr><td>Winner E-mail : </td><td>".$user->email."</td></tr>";
		$body .= "<tr><td>Time : </td><td>".date('Y-m-d H:i:s')."</td></tr>";				
		$body .= "<tr><td>Award : </td><td>".$eventdata->prize."</td></tr></table>";
		//echo $body; //die;
		$subject = "An award was just sent out";
		$to = array($mainframe->mailfrom,'info@youngyaa.com','pawan@searchtechnow.com');
		$from = array($mainframe->mailfrom, "Event Winner");
		$mailer->setSender($from);
		$mailer->addRecipient($to);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->isHTML();
		$mailer->send();
		
		/* User Email Format */
		
		$mailer = JFactory::getMailer();
		$body1 = "<h2 style='text-align:center'>Congratulation! You successfully grab ";
		$body1 .= $eventdata->prize." on ".date('Y-m-d H:i:s')."</h2>";
		$body1 .= "Please send email to <a href='mailto:info@youngyaa.com'>info@youngyaa.com</a>";
		$body1 .= "with your following detail, so that we could verify your identification:<br><br>";
		$body1 .= "Subject: The First Young Yaa Seckilling Event Winner<br>";
		$body1 .= "Content :<br>";
		$body1 .= "<span style='margin-left:20px;'>Real Name:</span><br>";
		$body1 .= "<span style='margin-left:20px;'>E-mail:</span><br>";
		$body1 .= "<span style='margin-left:20px;'>Phone:</span><br>";
		$body1 .= "<span style='margin-left:20px;'>Address:</span><br><br>";
		$body1 .= "You will be contacted by our staff in 1-2 business days about receiving the award.<br>";
		$body1 .= "Please also feel free to contact Young Yaa PTY.LTD for information about the award on any business time.";
		$body1 .= "<br><br><b>Young Yaa PTY.LTD</b><br>";
		$body1 .= "<b>Opening Hours:</b> 09:30 - 12:00, 13:30 - 17:00 (AEST), Monday - Friday<br>";
		$body1 .= "<b>Address:</b> 911/301 George St, Sydney, NSW, Australia<br>";
		$body1 .= "<b>Phone:</b> +61 (02) 8065 1192<br>";
		$body1 .= "<b>Mail:</b> info@youngyaa.com<br>";
		$body1 .= "<b>Website:</b> www.youngyaa.com.au";
		//echo $body; //die;
		$subject1 = "Congratulation! You successfully grabbed an Award on The First Young Yaa Seckilling Event";
		$to1 = $user->email;
		$from1 = array($mainframe->mailfrom, "Young Yaa PTY.LTD");
		$mailer->setSender($from1);
		$mailer->addRecipient($to1);
		$mailer->setSubject($subject1);
		$mailer->setBody($body1);
		$mailer->isHTML();
		$mailer->send();
		die;
	}
	
	public function grabittest(){
		define( '_JEXEC', 1 );
		ob_start();	
		ob_end_clean();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		if (!$user->guest) {
			$userid = $user->id;
			$username = $user->name;
			$email = $user->email;
		} else{
			$userid = 0;
			$username = 'Guest User';
			$email = 0;
		}
		
		$mainframe = JFactory::getApplication();
		$mailer = JFactory::getMailer();
		//echo $mainframe->getCfg('helpurl');
		$body = "<p>Event Winner for ".date('Y-m-d')." Time Sloat </p>";
		$body .= "<table><tr><td>Event Date : </td><td>".date('Y-m-d')."</td></tr>";
		$body .= "<tr><td>Event Time Sloat : </td><td> To </td></tr>";
		$body .= "<tr><td>Prize : </td><td>fdsfsd</td></tr>";
		$body .= "<tr><td>Prize Provider : </td><td>dsfdsfds</td></tr>";
		$body .= "<tr><td>User : </td><td>dsfdsf</td></tr>";
		$body .= "<tr><td>User Email : </td><td>dsfdsf</td></tr></table>";
		
		$subject = "Event Winner of dsfdsfdsfsd";
		//$to = $mainframe->mailfrom;
		$to = array('goyalpawan89@gmail.com','smtpstn@gmail.com');
		$from = array($mainframe->mailfrom, "Event Winner");
		$mailer->setSender($from);
		$mailer->addRecipient($to);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->isHTML();
		$mailer->send();
		die;
	}
}
