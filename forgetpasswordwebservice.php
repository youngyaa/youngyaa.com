
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
 'msg' =>'Send user name'); 
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

$id = $_GET['username'];



$db    = JFactory::getDbo();
$query = $db->getQuery(true)
    ->select('*')
    ->from('#__users')
    ->where('username=' . $db->quote($id));

$db->setQuery($query);
$result = $db->loadObject();



if ($result)
{









$udata = array(
                  "password"=>'1234',
"password2"=>'1234'
                  
              );

			  
  $user = new JUser;
  
  
  $user->bind($udata);
  
$newpass=$user->password;

$userid=$result->id;



$querym= "UPDATE `#__users` SET `password`='$newpass' WHERE `id`='$userid'";



$db->setQuery($querym);


$result5 = $db->query();

if($result5)
{



$subject = 'Forgot password mail';






$body = '<html><head><title>Forget password mail</title></head><body><p>Below are the details!</p><table border="1" width="100%">
<tr><td>username  Is: </td><td>'.$result->username.'</td>
    </tr>
    
<tr>
        <td>password Is: </td><td>1234</td>
    </tr>
    
  </table>
</body>
</html>';
$to = $result->email;
$from = array("youngdecade@youngdecadeprojects.biz", "youngyaa");


$mailer = JFactory::getMailer();


$mailer->setSender($from);


$mailer->addRecipient($to);

$mailer->setSubject('Forgot password mail');
$mailer->setBody($body);


$mailer->isHTML();



if($mailer->send())
{



  $record=array('success'=>'true',
 'msg' =>'password is send on your mail'); 
$data = json_encode($record);
echo $data;
return;

}

else{
   $record=array('success'=>'false',
 'msg' =>'mail not send'); 
$data = json_encode($record);
echo $data;
return;
}

}

else{
   $record=array('success'=>'false',
 'msg' =>'error on server'); 
$data = json_encode($record);
echo $data;
return;
}







}




else
{

 $record=array('success'=>'false',
 'msg' =>'user not found'); 
$data = json_encode($record);
echo $data;
return;



}

	

?>

























































