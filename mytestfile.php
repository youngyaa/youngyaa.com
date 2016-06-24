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

if(empty($_GET['username']))
{
$record=array('success'=>'false',
 'msg' =>'Send username'); 
$data = json_encode($record);
echo $data;
return;


}



if(empty($_GET['password']))
{
$record=array('success'=>'false',
 'msg' =>'Send password'); 
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



$credentials['username'] = $_GET['username'];
$credentials['password'] = $_GET['password'];

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
$db    = JFactory::getDbo();
$query = $db->getQuery(true)
    ->select('id, password')
    ->from('#__users')
    ->where('username=' . $db->quote($credentials['username']));

$db->setQuery($query);
$result = $db->loadObject();

if ($result)
{
    $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);

    if ($match === true)
    {
        // Bring this in line with the rest of the system
        $user = JUser::getInstance($result->id);




$red[]=array('user_id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'username'=>$user->username);

 $dataset = array('success'=>'true','msg' =>'data match','data'=>$red);
 $data = json_encode($dataset);	
	echo $data;
 return;	


      //  var_dump($user);
        //echo 'Joomla! Authentication was successful!';
    }
    else
    {
       $record=array('success'=>'false',
 'msg' =>'password not match'); 
$data = json_encode($record);
echo $data;
return;

        //die('Invalid password');
    }
} else {
   $record=array('success'=>'false',
 'msg' =>'user not found'); 
$data = json_encode($record);
echo $data;
return;

   // die('Cound not find user in the database');
}
