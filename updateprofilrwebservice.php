 <?php
header('Access-Control-Allow-Origin: *');
if(!$_POST)
{
$record=array('success'=>'false',
 'msg' =>'Send all data'); 
$data = json_encode($record);
echo $data;
return;


}

if(empty($_POST['user_id']))
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

$id = $_POST['user_id'];




$db    = JFactory::getDbo();
$query = $db->getQuery(true)
    ->select('*')
    ->from('#__users')
    ->where('id=' . $db->quote($id));

$db->setQuery($query);

$chkeentresult = $db->query();
 $replyAGprov= $db->getNumRows();

if($replyAGprov<=0)
{
$record=array('success'=>'false',
 'msg' =>'user not found'); 
$data = json_encode($record);
echo $data;
return;

}
else{ 

 $result = $db->loadObjectList();



$itemrow324 = $result[0];


 
 
 
 
 

	 $query2 = "SELECT *"
. " FROM #__osmembership_subscribers"
. " WHERE user_id = $id"

;
$db->setQuery($query2);
$rows = $db->loadObjectList();
$itemrow = $rows[0];

 








if(!empty($_POST['password']))
{
if(empty($_POST['oldpassword']))
{
$record=array('success'=>'false',
 'msg' =>'Send old password'); 
$data = json_encode($record);
echo $data;
return;


}


$credentials['password'] = $_POST['oldpassword'];


    $match = JUserHelper::verifyPassword($credentials['password'], $itemrow324->password, $itemrow324->id);





 

    if ($match === true)
    {




$udata = array(
                  "password"=>$_POST['password'],
"password2"=>$_POST['password']
                  
              );

			  
  $user = new JUser;
  
  
  $user->bind($udata);
  
$newpass=$user->password;



$query= "UPDATE `#__users` SET `password`='$newpass' WHERE `id`='$id'";



$db->setQuery($query);


$result51 = $db->query();








}
else
{
   $record=array('success'=>'false',
 'msg' =>'password not matched'); 
$data = json_encode($record);
echo $data;
return;
}


}


	
	
	


	


	
	




	
	
	
	





if(!empty($_POST['first_name']))
{
$first_name = $_POST['first_name'];


}
else{
	
	 $first_name=$itemrow->first_name;
}

if(!empty($_POST['last_name']))
{
$last_name = $_POST['last_name'];


}
else{
	
	$last_name=$itemrow->last_name;
}
if(!empty($_POST['organization']))
{
$organization =$_POST['organization'];


}
else{
	
		$organization=$itemrow->organization;
}


if(!empty($_POST['address']))
{


$address= $_POST['address'];

}
else{
	
	
	
	$address=$itemrow->address;

}

if(!empty($_POST['address2']))
{
$address2= $_POST['address2'];

}
else{
	
	$address2=$itemrow->address2;
}


if(!empty($_POST['city']))
{
$city= $_POST['city'];


}
else{
	
	
	$city=$itemrow->city;

}

if(!empty($_POST['state']))
{
$state= $_POST['state'];


}
else{
	
	$state=$itemrow->state;
}

if(!empty($_POST['zip']))
{
$zip= $_POST['zip'];


}else{
		$zip=$itemrow->zip;
}
if(!empty($_POST['country']))
{
$country= $_POST['country'];


}else{
	
		$country=$itemrow->country;
}


if(!empty($_POST['phone']))
{
$phone= $_POST['phone'];


}else{
	
	$phone=$itemrow->phone;
}

if(!empty($_POST['fax']))
{
$fax= $_POST['fax'];


}else{
	$fax=$itemrow->fax;
}
if(!empty($_POST['email']))
{
$email= $_POST['email'];


}else{
		$email=$itemrow->email;
}

if(!empty($_POST['comment']))
{
$comment= $_POST['comment'];

}
else{
	
		$comment=$itemrow->comment;
}






	
	

	




















$db = JFactory::getDbo();
$query = $db->getQuery(true);
// Fields to update.

//echo $query->update($db->quoteName('#__user_profiles'))->set($fields)->where($conditions);













$query= "UPDATE `#__osmembership_subscribers` SET `first_name`='$first_name',`last_name`='$last_name',`organization`='$organization',`address`='$address',`address2`='address2',`city`='$city',`state`='$state',`zip`='$zip',`country`='$country',`phone`='$phone',`fax`='$fax',`email`='$email',`comment`='$comment' WHERE `user_id`='$id'
";



$db->setQuery($query);


$result5 = $db->query();




if($result5)
{
   //$affectedRows = $db->getAffectedRows($result5);
  $record=array('success'=>'true',
 'msg' =>'update profile successfully'); 
$data = json_encode($record);
echo $data;
return;


}else{
   $record=array('success'=>'false',
 'msg' =>'error on server'); 
$data = json_encode($record);
echo $data;
return;
}





}









