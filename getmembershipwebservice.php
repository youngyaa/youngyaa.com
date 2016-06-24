 <?php

header('Access-Control-Allow-Origin: *');
if(!$_GET)
{
$record=array('success'=>'false',
 'msg' =>'Send all data'); 
$data = json_encode($record);
echo $data;
return;


}

if(empty($_GET['user_id']))
{
$record=array('success'=>'false',
 'msg' =>'Send user_id'); 
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



/**
 * Code adapted from plugins/authentication/joomla/joomla.php
 *
 * @package     Joomla.Plugin
 * @subpackage  Authentication.joomla
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Get a database object

	
	
	$db = JFactory::getDBO();



$query = "SELECT * FROM `a7rtg_osmembership_plans`";
$db->setQuery($query);
//$result = $db->query();

 $result1 = $db->loadObjectList();


$id=$_GET['user_id'];


if (!empty($result1))
{


$query1 = "SELECT * FROM `a7rtg_osmembership_subscribers` where  user_id='$id'";
$db->setQuery($query1);
$rowsAGprov = $db->query();
$replyAGprov= $db->getNumRows();



if($replyAGprov<=0)
{

$membership_id='0';
$userplan='no';

$userplanid=0;
}
else{

$result111 = $db->loadObjectList();
$userplan='yes';
$row=$result111[0];
$userplanid=$row->plan_id;
$membership_id=$row->membership_id;

   
}





$query11 = "SELECT * FROM `a7rtg_user_strip` where  user_id='$id'";
$db->setQuery($query11);
$rowsAGprov1 = $db->query();
$replyAGprov1= $db->getNumRows();

if($replyAGprov1<=0)
{
$stripeconnect='no';
}
else
{
$stripeconnect='yes';
}


    foreach($result1 AS $result)
{


$jhfg=strip_tags($result->short_description);

$description=strip_tags($result->description);

$red[]=array('id'=>$result->id,'title'=>$result->title,'description'=>$description,'price'=>$result->price,'short_description'=>$jhfg);
}



 $dataset = array('success'=>'true','msg' =>'data match','userplan'=>$userplan,'userplanid'=>$userplanid,'membership_id'=>$membership_id,'stripeconnect'=>$stripeconnect,'data'=>$red);
 $data = json_encode($dataset);	
	echo $data;
 return;	


      //  var_dump($user);
        //echo 'Joomla! Authentication was successful!';
    }
    else
    {
       $record=array('success'=>'false',
 'msg' =>'No plan found'); 
$data = json_encode($record);
echo $data;
return;

        //die('Invalid password');
    }























