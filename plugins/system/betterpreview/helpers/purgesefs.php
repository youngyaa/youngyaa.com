<?php
/**
 * @package         Better Preview
 * @version         5.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if (!JFactory::getApplication()->isAdmin())
{
	die('No Access!');
}

// need to set the user agent, to prevent breaking when debugging is switched on
$_SERVER['HTTP_USER_AGENT'] = '';

$db = JFactory::getDbo();

$query = $db->getQuery(true)
	->delete('#__betterpreview_sefs');
$db->setQuery($query);
$db->execute();
