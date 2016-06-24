<?php
/**
 * ------------------------------------------------------------------------
 * JA Masshead Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once (dirname(__FILE__).'/helper.php');
$helper = ModJAMassheadHelper::getInstance();
// Get masshead information
$masshead = $helper->getMasshead($params);

// Display
require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));