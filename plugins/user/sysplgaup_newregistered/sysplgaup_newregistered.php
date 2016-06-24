<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2015 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if(!defined('DS')) {
   define('DS', DIRECTORY_SEPARATOR);
}

jimport('joomla.plugin.plugin');

class plgUserSysplgaup_newregistered extends JPlugin
{

	public function onUserAfterSave($user, $isnew, $succes, $msg) {
		
		if ( $isnew ) {

			$app = JFactory::getApplication();
		
      		$lang = JFactory::getLanguage();
      		$lang->load( 'com_alphauserpoints', JPATH_SITE);
			
			$jnow		= JFactory::getDate();		
			$now		= $jnow->toSql();
		
			require_once(JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
			
			// get params definitions
			$params = JComponentHelper::getParams( 'com_alphauserpoints' );		
			
			$prefixSelfRegister = $params->get('prefix_selfregister');
			$prefixReferralRegister = $params->get('prefix_referralregister');
		
			$referrerid = trim(@$_SESSION['referrerid']);
			unset($_SESSION['referrerid']);
		
			$db	   = JFactory::getDBO();
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_newregistered' AND `published`='1'";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			
			$prefixNewReferreid = ( $referrerid!='' ) ? strtoupper($prefixReferralRegister) : strtoupper($prefixSelfRegister); 
	
			// if rule enabled
			if ( $result ) {			
				
				if ( !$params->get('referralIDtype') ) {
					$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));	
				} 
				else 
				{					
					$newreferreid = $prefixNewReferreid . strtoupper($user['username']);
					$newreferreid = str_replace( ' ', '-', $newreferreid );				
					$newreferreid = str_replace( ',', '-', $newreferreid );
					$newreferreid = str_replace( "'", "-", $newreferreid );
				}
				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');
				
				$row = JTable::getInstance('userspoints');
				// insert this new user into alphauserpoints table
			    $row->id			= NULL;
				$row->userid		= $user['id'];
			    $row->referreid		= $newreferreid;
			    $row->points		= $result[0]->points;
			    $row->max_points	= 0;
				$row->last_update	= $now;
			    $row->referraluser	= $referrerid;
				$row->published		= 1;
				$row->shareinfos	= 1;
				
				if (isset($user['profile']) && (count($user['profile'])))				
				{
					foreach ($user['profile'] as $k => $v)
					{
						$value = str_replace('"', '', $v);
						$row = $this->checkProfileField($k, $row, $value);
					}						
				}
				
				if (!$row->store()) {
					JError::raiseError(500, $row->getError());
				}
				
				// save new points into alphauserpoints table details
				$row2 = JTable::getInstance('userspointsdetails');
			    $row2->id				= NULL;
			    $row2->referreid		= $newreferreid;
			    $row2->points			= $result[0]->points;
				$row2->insert_date		= $now;
			    $row2->expire_date 		= $result[0]->rule_expire;
			    $row2->status			= $result[0]->autoapproved;
				$row2->rule				= $result[0]->id;
			    $row2->approved			= $result[0]->autoapproved;
				$row2->datareference	= JText::_( 'AUP_WELCOME' );
				$row2->enabled			= 1;
			  			
				if (!$row2->store()) {
					JError::raiseError(500, $row2->getError());
				}
				
				// frontend message
				if ( $result[0]->displaymsg ) 
				{
					$msg = str_replace('{username}', $user['username'], $result[0]->msg);
					if ( $msg!='' )
					{								
						$app->enqueueMessage( str_replace ( '{points}', AlphaUserPointsHelper::getFPoints($result[0]->points), JText::_( $msg ) ));
					} else {
						$app->enqueueMessage( sprintf ( JText::_('AUP_CONGRATULATION'), $result[0]->points ));
					}			
				}
				
				// send notification		
				if ( $result[0]->notification ) AlphaUserPointsHelper::sendnotification ( $newreferreid, $result[0]->points, $result[0]->points, $result[0], 1 );
				
				if ( $referrerid ) {
					$data = htmlspecialchars( $user['name'], ENT_QUOTES, 'UTF-8') . " (" . $user['username'] . ") ";
					$data = sprintf ( JText::_('AUP_X_HASJOINEDTHEWEBSITE'), $data );
					$this->sysplgaup_invitewithsuccess( $referrerid, $data );
				}
				
				return true;
				
			} else return false;						
		}
	}
	
	public function onUserAfterDelete($user, $succes, $msg) {

		$db	   = JFactory::getDBO();
		
		$query = "SELECT `id`, `referreid`, `referraluser` FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$result = $db->loadObject();
		$referreid = $result->referreid;
		$referraluser = $result->referraluser;

		$query = "DELETE FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_details WHERE `referreid`='".$referreid."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_details_archive WHERE `referreid`='".$referreid."'";
		$db->setQuery( $query );
		$db->query();		
	
		$query = "DELETE FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_medals WHERE `rid`='".$result->id."'";
		$db->setQuery( $query );
		$db->query();
		
		// if the user has been a referral user
		$query = "UPDATE #__alpha_userpoints SET referraluser='' WHERE referraluser='".$referreid."'";
		$db->setQuery($query);
		$db->query();
		
		// recount referrees for the referral user
		$query = "SELECT referrees FROM #__alpha_userpoints WHERE referreid='".$referraluser."'";
		$db->setQuery($query);
		$numreferrees = $db->loadResult();
		if ( $numreferrees > 0 )
		{
			$query = "UPDATE #__alpha_userpoints SET referrees=referrees-1 WHERE referreid='".$referraluser."'";
			$db->setQuery($query);
			$db->query();
		}
		
	}
	
	public function sysplgaup_invitewithsuccess( $referrerid, $data ) {

		$ip = $_SERVER["REMOTE_ADDR"];
		require_once(JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
		$keyreference = AlphaUserPointsHelper::buildKeyreference( 'sysplgaup_invitewithsuccess', $ip );
		AlphaUserPointsHelper::userpoints( 'sysplgaup_invitewithsuccess', $referrerid, 0, $keyreference, $data );

	}
	
	public function onUserLogin($user, $options = array())
	{
		$app = JFactory::getApplication();
		
		$db	   = JFactory::getDBO();
		
		jimport('joomla.user.helper');
		
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
		}
		
		if ($instance->get('block') == 0) {
			require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
			// start the user session for AlphaUserpoints
			AlphaUserPointsHelper::getReferreid( intval($instance->get('id')) );
						
			if( $app->isSite() ){
			
				// load language component
        $lang = JFactory::getLanguage();
        $lang->load( 'com_alphauserpoints', JPATH_SITE);
				
				// check raffle subscription to showing a reminder message
					
				// check first if rule for raffle is enabled
				$result = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_raffle', 1 );
				if ( $result ) {				
					$resultCurrentRaffle = $this->checkIfCurrentRaffleSubscription(intval($instance->get('id')));
					if ($resultCurrentRaffle=='stillRegistered') {
						$messageAvailable = JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE');
						if ( $messageAvailable!='' ) {
							$messageRaffle = sprintf ( JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE'), $user['username'] );
							$app->enqueueMessage( $messageRaffle );
						}		
					}				
				}
			}
			
			//return true;
		}		
		
	}
	
	public function onUserLogout($user, $options = array()) {
		//Make sure we're a valid user first
		if($user['id'] == 0) return true;

		unset($_SESSION['referrerid']);
		return true;
	}
		
	private function checkIfCurrentRaffleSubscription($userid) {
	
		$db	   = JFactory::getDBO();
		
		$jnow		= JFactory::getDate();		
		$now		= $jnow->toSql();		
		
		$query = "SELECT id FROM #__alpha_userpoints_raffle WHERE published='1' AND inscription='1' AND raffledate>'$now' AND raffledate!='0000-00-00 00:00:00' AND winner1='' AND winner2='' AND winner3='' LIMIT 1";
		$db->setQuery( $query );
		$nextraffle = $db->loadResult();
		
		if ( $nextraffle ) {
			// check if already subscription
			$query = "SELECT COUNT(*) FROM #__alpha_userpoints_raffle_inscriptions WHERE userid='$userid' AND raffleid='$nextraffle'";
			$db->setQuery( $query );
			$alreadySubscription = $db->loadResult();
			if ( $alreadySubscription ) {
				return 'alreadyRegistered';
			} return 'stillRegistered';
			
		} else return 'noRaffleAvailable';
	
	}	
	
	private function checkProfileField($jProfileKey, &$row, $value)
	{
	
		switch ($jProfileKey) 
		{
			case "aboutme":
				$row->aboutme=$value;
				break;
			case "address1":
				$row->address=$value;
				break;
			case "address2":
				$row->address .= ' ' . $value;
				break;
			case "city":
				$row->city=$value;
				break;
			case "country":
				$row->country=$value;
				break;
			case "dob":
				$row->birthdate=$value;
				break;
			case "phone":
				$row->phonehome=$value;
				break;
			case "postal_code":
				$row->zipcode=$value;
				break;
			case "website":
				$row->website=$value;
				break;
			default:
					
		} 
		return $row;
	
	}

}
?>