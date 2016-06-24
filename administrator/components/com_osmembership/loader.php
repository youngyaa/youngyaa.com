<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
/**
 * Reregister prefix and classes for auto-loading
 */
JLoader::registerPrefix('MPF', JPATH_ADMINISTRATOR . '/components/com_osmembership/libraries/mpf');
JLoader::registerPrefix('OSMembership', JPATH_BASE . '/components/com_osmembership');
JLoader::register('os_payments', JPATH_ROOT . '/components/com_osmembership/plugins/os_payments.php');
JLoader::register('os_payment', JPATH_ROOT . '/components/com_osmembership/plugins/os_payment.php');
JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');

if (JFactory::getApplication()->isAdmin())
{
	JLoader::register('OSMembershipHelper', JPATH_ROOT . '/components/com_osmembership/helper/helper.php');
	JLoader::register('OSMembershipHelperDatabase', JPATH_ROOT . '/components/com_osmembership/helper/database.php');
	JLoader::register('OSMembershipHelperHtml', JPATH_ROOT . '/components/com_osmembership/helper/html.php');
	JLoader::register('OSMembershipHelperEuvat', JPATH_ROOT . '/components/com_osmembership/helper/euvat.php');
	JLoader::register('OSMembershipHelperJquery', JPATH_ROOT . '/components/com_osmembership/helper/jquery.php');
}

$config = OSMembershipHelper::getConfig();
if (empty($config->debug))
{
	error_reporting(0);
}
else
{
	error_reporting(E_ALL);
}

