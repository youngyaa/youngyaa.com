<?php
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>

<?php

/**
 * Mainbody 3 columns, content in center: sidebar1 - content - sidebar2
 */

// positions configuration
$sidebar1 = 'sidebar-1';
$sidebar2 = 'sidebar-2';

$sidebar1 = $this->countModules($sidebar1) ? $sidebar1 : false;
$sidebar2 = $this->countModules($sidebar2) ? $sidebar2 : false;

// detect layout
if ($sidebar1 && $sidebar2) {
	$this->loadBlock('mainbody/two-sidebar', array('sidebar1' => $sidebar1, 'sidebar2' => $sidebar2));
} elseif ($sidebar1) {
	$this->loadBlock('mainbody/one-sidebar-left', array('sidebar' => $sidebar1));
} elseif ($sidebar2) {
	$this->loadBlock('mainbody/one-sidebar-right', array('sidebar' => $sidebar2));
} else {
	$this->loadBlock('mainbody/no-sidebar');
}
