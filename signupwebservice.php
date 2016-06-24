
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

if(empty($_POST['name']))
{
$record=array('success'=>'false',
 'msg' =>'Send name'); 
$data = json_encode($record);
echo $data;
return;


}



if(empty($_POST['password']))
{
$record=array('success'=>'false',
 'msg' =>'Send password'); 
$data = json_encode($record);
echo $data;
return;


}


if(empty($_POST['email']))
{
$record=array('success'=>'false',
 'msg' =>'Send email'); 
$data = json_encode($record);
echo $data;
return;


}
if(empty($_POST['username']))
{
$record=array('success'=>'false',
 'msg' =>'Send username'); 
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


$realname = $_POST['name'];
$username = $_POST['username'];
$email    = $_POST['email'];
$password = $_POST['password']; 
          jimport( 'joomla.user.helper' );

	$db = JFactory::getDBO();
$chktail = "SELECT * FROM `#__users` where username='$username' ";
$db->setQuery($chktail);
 $db->query();
$evntAGprov= $db->getNumRows();

if($evntAGprov>0)
{


$record=array('success'=>'false',
 'msg' =>'user alrady Exists'); 
$data = json_encode($record);
echo $data;
return;
}




$udata = array(
                  "name"=>$realname,
                  "username"=>$username,
                  "password"=>$password,
                  "password2"=>$password,
                  "email"=>$email,
                  "block"=>0,
                  "groups"=>array("1","2")
              );

  $user = new JUser;
              
			  //Write to database
			  if(!$user->bind($udata)) {

$record=array('success'=>'false',
 'msg' =>'user already exit'); 
$data = json_encode($record);
echo $data;
return;


				//  throw new Exception("Could not bind data. Error: " . $user->getError());
			  }
			  if (!$user->save()) {

$record=array('success'=>'false',
 'msg' =>'error on server'); 
$data = json_encode($record);
echo $data;
return;

				//  throw new Exception("Could not save user. Error: " . $user->getError());
			  }
              

$record=array('success'=>'true',
 'msg' =>'registration successfully'); 
$data = json_encode($record);
echo $data;
return;

           //  echo $new_user_id = $user->password;