<?php  
date_default_timezone_set("Australia/Sydney");
if(empty($_GET))
	{
	    $record = array('success'=>'false','msg'=> 'Please send all data');
	    $data = json_encode($record);
	    echo $data;
	    return;	
	}
	
	if(empty($_GET['user_id']))
	{
	   $record=array('success'=>'false','msg'=>'Please send user id');
	  $data = json_encode($record);
	  echo $data;
	  return; 
         }	

if(empty($_GET['event_id']))
	{
	   $record=array('success'=>'false','msg'=>'Please send event id');
	  $data = json_encode($record);
	  echo $data;
	  return; 
         }	

if(empty($_GET['slot_id']))
	{
	   $record=array('success'=>'false','msg'=>'Please send slot id');
	  $data = json_encode($record);
	  echo $data;
	  return; 
         }	


/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';



// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');


 require_once (JPATH_BASE .'/libraries/joomla/factory.php');

jimport( 'joomla.user.helper' ); 



$id=$_GET['user_id'];


	$db = JFactory::getDBO();










$query = "SELECT * FROM `a7rtg_users` where id='$id'";
$db->setQuery($query);
$db->query();
$replyAGprov= $db->getNumRows();



if($replyAGprov<=0)
{
 $record=array('success'=>'false','msg'=>'no user found');
	  $data = json_encode($record);
	  echo $data;
	  return; 

}
else
{
$result111 = $db->loadObjectList();

$row=$result111[0];
$username=$row->username;

$email=$row->email;

}
 

$date= date('Y-m-d H:i:s');

$chktail = "SELECT * FROM `a7rtg_secklioneevent` where id='".$_GET['event_id']."'";
$db->setQuery($chktail);
 $db->query();
$evntAGprov= $db->getNumRows();

if($evntAGprov<=0)
{


$record=array('success'=>'false',
 'msg' =>'No Seckilling event found'); 
$data = json_encode($record);
echo $data;
return;
}




$chktailprice = "SELECT * FROM `a7rtg_secklioneevent_price` where event_id='".$_GET['event_id']."' and id='".$_GET['slot_id']."'";
$db->setQuery($chktailprice);
 $db->query();
$evntprice= $db->getNumRows();

if($evntprice<=0)
{


$record=array('success'=>'false',
 'msg' =>'No slot id found'); 
$data = json_encode($record);
echo $data;
return;
}



else
{

$resultprice = $db->loadObjectList();

$rowprice=$resultprice [0];
$price=$rowprice->price;




}










$chktailal = "SELECT * FROM `a7rtg_grab_event` where event_id='".$_GET['event_id']."' and slot_id='".$_GET['slot_id']."'";
$db->setQuery($chktailal);
 $db->query();
$evntAGproval= $db->getNumRows();

if($evntAGproval>0)
{


$record=array('success'=>'false',
 'msg' =>'This price already allotted other user'); 
$data = json_encode($record);
echo $data;
return;
}







 $queryup= "INSERT INTO `a7rtg_grab_event`(`event_id`, `slot_id`, `user_id`,`insertime`) VALUES ('".$_GET['event_id']."','".$_GET['slot_id']."','$id','$date')
";



$db->setQuery($queryup);


$resultup = $db->query();

if(!$resultup){ 
$record=array('success'=>'false',
 'msg' =>'Error to Update data'); 
$data = json_encode($record);
echo $data;
return;

}



else{
	
//$to = $email;

// subject
$subject = 'Win Seckilling  event price';






$body = '<html>
<head>
  <title>Congrats you win the seckling event price</title>
</head>
<body>
  <p>Below are the details!</p>
  <table border="1" width="100%">
    <tr>
        <td>Price Is: </td><td>'.$price.'</td>
    </tr>
    
  </table>
</body>
</html>';
$to = "dipika.youngdecade@gmail.com";
$from = array("youngdecade@youngdecadeprojects.biz", "youngyaa");


$mailer = JFactory::getMailer();


$mailer->setSender($from);


$mailer->addRecipient($to);

$mailer->setSubject('Win Seckilling event price');
$mailer->setBody($body);


$mailer->isHTML();



if($mailer->send())
{

$record=array('success'=>'true',
 'msg' =>'Price request send successfully'); 
$data = json_encode($record);
echo $data;
return;
}
else
{
$record=array('success'=>'false',
 'msg' =>'mail not send'); 
$data = json_encode($record);
echo $data;
return;

}

}






	?>

