<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
define('OSM_DEFAULT_RENEW_OPTION_ID', 999);

//Disable error reporting
include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';
require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

OSMembershipHelper::prepareRequestData();

$input = new MPFInput();
$task  = $input->getCmd('task', '');

//Handle BC for existing payment plugins
if ($task == 'payment_confirm' || $task == 'recurring_payment_confirm')
{
	//Lets Register controller handle these tasks
	$input->set('task', 'register.' . $task);
}

MPFController::getInstance($input->getCmd('option', null), $input, $MPConfig)
	->execute()
	->redirect();
