
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

jimport( 'joomla.user.helper' ); 

$userId = $_GET['user_id'];




$user = JFactory::getUser($userId);


$userProfile = JUserHelper::getProfile( $userId );

$db = JFactory::getDBO();

$query = "SELECT *"
. " FROM #__osmembership_subscribers"
. " WHERE user_id = $userId"

;
$db->setQuery($query);
$rows = $db->loadObjectList();
$itemrow = $rows[0];
$membership_id = $itemrow->membership_id;






if(empty($itemrow->first_name))
{
	$first_name='na';
}
else
{
	$first_name=$itemrow->first_name;
	
}
if(empty($itemrow->last_name))
{
	$last_name='na';
}
else{
	$last_name=$itemrow->last_name;
	
}


if(empty($itemrow->organization))
{
	$organization='na';
	
}
else{
	
	$organization=$itemrow->organization;
}

if(empty($itemrow->address))
{
	$address='na';
}
else
{
	
	$address=$itemrow->address;
}


if(empty($itemrow->address2))
{
	$address2='na';
}
else{
	
	
$address2=$itemrow->address2;
}

if(empty($itemrow->city))
{
	$city='na';
}

else{
	
	$city=$itemrow->city;
}

if(empty($itemrow->state))
{
$state='na';
}
else{
	
	$state=$itemrow->state;
	
}

if(empty($itemrow->zip))
{
$zip='na';
}
else{
	
	$zip=$itemrow->zip;
	
}




if(empty($itemrow->country))
{
$country='na';
}
else{
	
	$country=$itemrow->country;
	
}



if(empty($itemrow->phone))
{
$phone='na';
}
else{
	
	$phone=$itemrow->phone;
	
}

if(empty($itemrow->fax))
{
$fax='na';
}
else{
	
	$fax=$itemrow->fax;
	
}


if(empty($itemrow->email))
{
$email='na';
}
else{
	
	$email=$itemrow->email;
	
}

if(empty($itemrow->comment))
{
$comment='na';
}
else{
	
	$comment=$itemrow->comment;
	
}


if(empty($itemrow->email))
{
$email='na';
}
else{
	
	$email=$itemrow->email;
	
}





$red[]=array('user_id'=>$user->id,'name'=>$user->name,'email'=>$email,'username'=>$user->username,
'first_name'=>$first_name,'last_name'=>$last_name,'address'=>$address,'address2'=>$address2,
'state'=>$state,'phone'=>$phone,'zip'=>$zip,
'country'=>$country,'city'=>$city,'organization'=>$organization,'fax'=>$fax,'comment'=>$comment,'membership_id'=>$membership_id);



 $dataset = array('success'=>'true','msg' =>'data match','data'=>$red);
 $data = json_encode($dataset);	
	echo $data;
 return;	
