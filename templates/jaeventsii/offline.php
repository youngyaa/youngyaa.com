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
$app = JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/offline.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/fonts/font-awesome/css/font-awesome.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
  <script src="<?php echo $this->baseurl ?>/media/jui/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body>
	<div class="offline-body">
		<div id="frame" class="outline">

			<jdoc:include type="message" />
			
			<div class="offline-container clearfix">
				<?php if ($app->getCfg('offline_image') && file_exists($app->getCfg('offline_image'))) : ?>
					<div id="offline-img"><img src="<?php echo $app->getCfg('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->getCfg('sitename')); ?>" /></div>
				<?php endif; ?>
				
				<!-- Title offline -->
				<div id="offline-title">
					<h1><?php echo htmlspecialchars($app->getCfg('sitename')); ?></h1>
				</div>
				
				<!-- Message offline -->
				<?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
				<div class="offline-msg">
						<?php echo $app->getCfg('offline_message'); ?>
				</div>

				<?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
					<div>
						<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
					</div>
				<?php  endif; ?>
				<!-- //Message offline -->
					<div id="offline-content">

						<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
							<div class="input">
									<div class="form-offline">
										<div id="form-login-username" class="form-group" >
											<label class="control-label" for="username"><span class="fa fa-user"></span><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
											<input class="control-input" name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
										</div>
										<div id="form-login-password" class="form-group" >
											<label class="control-label" for="passwd"><span class="fa fa-key"></span><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
											<input class="control-input" type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd"/>
										</div>
									</div>
                
                <div class="form-action">
                  <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                  <div id="form-login-remember">
                    <input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
                    <label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
                  </div>
                  <?php  endif; ?>
                  
                  <div id="submit-buton">
                    <input type="submit" name="Submit" class="button login" value="<?php echo JText::_('JLOGIN') ?>" />
                  </div>
                </div>
										
								<input type="hidden" name="option" value="com_users" />
								<input type="hidden" name="task" value="user.login" />
								<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
								<?php echo JHtml::_('form.token'); ?>
							</div>
						</form>
				</div>
			</div>

		</div>
	</div>
</body>
</html>
