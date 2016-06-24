 <?php



header('Access-Control-Allow-Origin: *');

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

	jimport( 'joomla.user.helper' ); 
	
	$db = JFactory::getDBO();



$query = "SELECT * FROM `a7rtg_osmembership_countries`";
$db->setQuery($query);
$result = $db->query();
$replyAGprov= $db->getNumRows();









if($replyAGprov<=0)
{
$record=array('success'=>'false',
 'msg' =>'No country found'); 
$data = json_encode($record);
echo $data;
return;


}
else
{


 $result1 = $db->loadObjectList();
   



 foreach($result1 AS $result)
{


//$jhfg=strip_tags($result->short_description);

//$description=strip_tags($result->description);

$red[]=array('id'=>$result->id,'name'=>$result->name,'country_3_code'=>$result->country_3_code,'country_2_code'=>$result->country_2_code,'country_id'=>$result->country_id);
}



 $dataset = array('success'=>'true','msg' =>'data match','data'=>$red);
 $data = json_encode($dataset);	
	echo $data;
 return;	


     
    }
 
















