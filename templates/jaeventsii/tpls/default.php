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
$bodyClass ='';
if ($this->countModules('masthead')) $bodyClass = 'header-trans';

$app = JFactory::getApplication();
$template = $app->getTemplate();
$bodybackground = $this->params->get('bodybackground', 'color');

$style = '';
if($bodybackground=='color') {
  $backgroundcolor = $this->params->get('bodybackgroundcolor', '#d7d9db');
  $style = 'background-color: '.$backgroundcolor;
} else {
  $backgroundimage = $this->params->get('bodybackgroundimage', '');
  $style = 'background-image: url('.$backgroundimage.'); background-size: cover;';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
	  class='<jdoc:include type="pageclass" />'>

<head>
	<jdoc:include type="head" />
	<?php $this->loadBlock('head') ?>
	<?php $this->addCss('layouts/docs') ?>
</head>

<body class="<?php echo $bodyClass; ?>">

<div class="t3-wrapper" style="<?php echo $style; ?>"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->
  <div class="main-container">
    <?php $this->loadBlock('header') ?>

    <?php $this->loadBlock('masthead') ?>

    <?php $this->loadBlock('mainbody') ?>
    
    <?php $this->loadBlock('tabs') ?>

    <?php $this->loadBlock('footer') ?>
  </div>
</div>

</body>

</html>