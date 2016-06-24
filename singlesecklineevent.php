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
 

$date= date('Y-m-d');

$chktail = "SELECT * FROM `a7rtg_secklioneevent` where end_date>='$date' and id='".$_GET['event_id']."'";
$db->setQuery($chktail);
 $db->query();
$evntAGprov= $db->getNumRows();

if($evntAGprov<=0)
{


$record=array('success'=>'false',
 'msg' =>'no seckline event found'); 
$data = json_encode($record);
echo $data;
return;
}








else{

		 $result1= $db->loadObjectList();
	foreach($result1 AS $result)
{





if(!empty($result->rule))
{
$rule=explode(',', $result->rule);
for($i=0;$i<count($rule);$i++)
{
$rulered1[]=array('rule'=>$rule[$i]);

$rulered=$rulered1;

}

}

	
$event_id=$result->id;
$plandetail = "SELECT * FROM `a7rtg_secklioneevent_price` WHERE event_id='$event_id'";
$db->setQuery($plandetail );
 $db->query();
$planAGprov= $db->getNumRows();

if($planAGprov<=0)
{

$pricerec1=[];


}
else
{

 $planAGprovde= $db->loadObjectList();









foreach($planAGprovde AS $price)
{
$start_time=$price->start_time;


$end_time=$price->end_time;



$todatdate= strtotime(date('Y-m-d H:i:s'));
$edate=strtotime($result->end_date.' '.$end_time);

$sdate=strtotime($result->start_date.' '.$start_time);

if($todatdate> $edate)
{
$expire='expire';


$differenceInSeconds = 0;
$active_flag='no';
}


elseif(($todatdate<$edate)&&($todatdate>$sdate))
{

$expire='lefttime';



$differenceInSeconds = $edate-$todatdate ;
$active_flag='yes';

}


elseif(($todatdate <$sdate))
{

$expire='starttime'; 




$differenceInSeconds = $sdate-$todatdate  ;
$active_flag='no';
}



$pricerec[]=array('id'=>$price->id,'event_id'=>$price->event_id,'start_time'=>$price->start_time,'end_time'=>$price->end_time,'image'=>$price->image,'price'=>$price->price,'provider'=>$price->provider,'description'=>$price->description,'active'=>$active_flag,'differenceInSeconds'=>$differenceInSeconds,'expire'=>$expire);

$pricerec1=$pricerec;


} 
unset($pricerec);}







$red[]=array('id'=>$result->id,'title'=>$result->title,'description'=>$result->description,'image'=>$result->image,'start_date'=>$result->start_date,'end_date'=>$result->end_date,'insertime'=>$result->insertime,'rule'=>$rulered,'price_detail'=>$pricerec1);





}
	
	






 $dataset = array('success'=>'true','msg' =>'data match','data'=>$red);
 $data = json_encode($dataset);	
	echo $data;
 return;	





}






	?>
