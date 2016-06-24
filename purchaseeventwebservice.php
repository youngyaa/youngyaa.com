<?php
header('Access-Control-Allow-Origin: *');

  require_once('stripeconfigwebservice.php');

if(empty($_POST))
	{
	    $record = array('success'=>'false','msg'=> 'Please send all data');
	    $data = json_encode($record);
	    echo $data;
	    return;	
	}
	
	if(empty($_POST['user_id']))
	{
	   $record=array('success'=>'false','msg'=>'Please send user id');
	  $data = json_encode($record);
	  echo $data;
	  return; 
         }	


if(empty($_POST['token']))
	{
	    $record=array('success'=>'false','msg'=>'Please send token');
	    $data = json_encode($record);
	   echo $data;
	   return; 
         }


   if($_POST['token']=="(null)" || empty($_POST['token']) )
 	{
	    $record=array('success'=>'false','msg'=>'Please send token');
	    $data = json_encode($record);
	   echo $data;
	   return; 
         }



if(empty($_POST['event_id']))
	{
	   $record=array('success'=>'false','msg'=>'Please send event id');
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



$id=$_POST['user_id'];

$event_id=$_POST['event_id'];
	
	$db = JFactory::getDBO();



$query = "SELECT * FROM `a7rtg_users` where id='$id'";
$db->setQuery($query);
$result = $db->query();
$replyAGprov= $db->getNumRows();



if($replyAGprov<=0)
{
 $record=array('success'=>'false','msg'=>'no user found');
	  $data = json_encode($record);
	  echo $data;
	  return; 

}
 $result1 = $db->loadObjectList();

$row=$result1[0];


$chktail = "SELECT * FROM `a7rtg_book_userevent` where event_id='$event_id' and user_id='$id'";
$db->setQuery($chktail);
 $db->query();
$evntAGprov= $db->getNumRows();

if($evntAGprov>0)
{


$record=array('success'=>'false',
 'msg' =>'you already book this event'); 
$data = json_encode($record);
echo $data;
return;
}



else
{












$plandetail = "SELECT * FROM ` a7rtg_myevents` WHERE event_id='$event_id'";
$db->setQuery($plandetail );
 $db->query();
$planAGprov= $db->getNumRows();

if($planAGprov<=0)
{
$record=array('success'=>'false',
 'msg' =>'event not found'); 
$data = json_encode($record);
echo $data;
return;


}


 $planAGprovde= $db->loadObjectList();
$rowplan=$planAGprovde[0];

$price=$rowplan->price*100;

$token=$_POST['token'];




if($price<=0)
{
$record=array('success'=>'false',
 'msg' =>'price 0, cant purchse'); 
$data = json_encode($record);
echo $data;
return;

}






try {
  $charge = \Stripe\Charge::create(array(
    "amount" => $price, // amount in cents, again
    "currency" => "usd",
    "source" => $token,
    "description" => "Example charge"
    ));
} catch(\Stripe\Error\Card $e) {
 $record=array('success'=>'false','msg'=>'cart has been declined');
	  $data = json_encode($record);
	  echo $data;
	  return; 
}


$date=date('Y-m-d H:i:s');




 $queryup= "INSERT INTO `a7rtg_book_userevent`(`user_id`, `event_id`, `token`, `price`, `insertime`) VALUES ('$id','$event_id','$token','".$rowplan->price."','$date')";



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
	
	
	
	
	

	
$record=array('success'=>'true',
 'msg' =>'event Purchased successfully'); 
$data = json_encode($record);
echo $data;
return;



}


}




	?>