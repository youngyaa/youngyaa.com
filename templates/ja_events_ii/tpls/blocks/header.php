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

// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', T3Path::getUrl('images/logo.png', '', true)) : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm', T3Path::getUrl('images/logo-sm.png', '', true)) : false;

if (!$sitename) {
	$sitename = JFactory::getConfig()->get('sitename');
}

$headright = $this->countModules('head-search or languageswitcherload or head-btn');


?>

<!-- HEADER -->
<header id="t3-header" class="wrap t3-header-wrap affix-top cpanel-close" data-spy="affix" data-offset-top="1">
<?php if ($this->countModules('head-search')) : ?>
<div class="t3-cpanel t3-cpanel-search">
	<div class="container">
	<jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" style="raw" />
	</div>
</div>
<?php endif ?>

<div class="t3-header">
	<div class="container">
		<div class="row">

			<!-- LOGO -->
			<div class="col-xs-6 col-sm-2 logo">
				<div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
					<a href="<?php echo JURI::base(true) ?>" title="<?php echo strip_tags($sitename) ?>">
						<?php if($logotype == 'image'): ?>
							<img class="logo-img" src="<?php echo JURI::base(true) . '/' . $logoimage ?>" alt="<?php echo strip_tags($sitename) ?>" />
						<?php endif ?>
						<?php if($logoimgsm) : ?>
							<img class="logo-img-sm" src="<?php echo JURI::base(true) . '/' . $logoimgsm ?>" alt="<?php echo strip_tags($sitename) ?>" />
						<?php endif ?>
						<span><?php echo $sitename ?></span>
					</a>
					<small class="site-slogan"><?php echo $slogan ?></small>
				</div>
			</div>
			<!-- //LOGO -->

			<!-- MAIN NAVIGATION -->
			<nav id="t3-mainnav" class="col-xs-2 col-sm-7 navbar navbar-default t3-mainnav">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
					
						<?php if ($this->getParam('navigation_collapse_enable', 1) && $this->getParam('responsive', 1)) : ?>
							<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".t3-navbar-collapse">
								<i class="fa fa-bars"></i>
							</button>
						<?php endif ?>

						<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
							<?php $this->loadBlock ('off-canvas') ?>
						<?php endif ?>

					</div>

					<?php if ($this->getParam('navigation_collapse_enable')) : ?>
						<div class="t3-navbar-collapse navbar-collapse collapse"></div>
					<?php endif ?>

					<div class="t3-navbar navbar-collapse collapse">
						<jdoc:include type="<?php echo $this->getParam('navigation_type', 'megamenu') ?>" name="<?php echo $this->getParam('mm_type', 'mainmenu') ?>" />
					</div>
			</nav>
			<!-- //MAIN NAVIGATION -->

			<?php if ($headright): ?>
				<div class="col-xs-4 col-sm-3 head-actions pull-right">
					<?php if ($this->countModules('languageswitcherload')) : ?>
						<!-- LANGUAGE SWITCHER -->
						<div class="languageswitcherload pull-right">
							<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
						</div>
						<!-- //LANGUAGE SWITCHER -->
					<?php endif ?> 
					
					<?php if ($this->countModules('head-btn')) : ?>
						<!-- HEAD BTN -->
						<div class="head-btn <?php $this->_c('head-btn') ?>">
							<jdoc:include type="modules" name="<?php $this->_p('head-btn') ?>" style="raw" />
						</div>
						<!-- //HEAD BTN -->
					<?php endif ?>

					<?php if ($this->countModules('head-search')) : ?>
						<!-- HEAD SEARCH -->
						<div class="head-search">
							<button class="btn btn-search"><i class="fa fa-search"></i></button>
						</div>
						<!-- //HEAD SEARCH -->
					<?php endif ?>
				</div>
			<?php endif ?>

		</div>
	</div>
</div>
</header>
<!-- //HEADER -->
