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

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<!-- Modal -->
<div class="modal fade" id="ja-login-form" tabindex="-1" role="dialog" aria-labelledby="ja-login-form">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="tab-wrapper">
      	<div class="tab-nav-wrapper">
            <ul class="nav nav-tabs clearfix" role="tablist">
                <li class="active" role="presentation" id="ja-user-login-tab"><a href="#ja-user-login" aria-controls="ja-user-login" role="tab" data-toggle="tab"><?php echo JText::_('TXT_LOGIN');?></a></li>
                <li class="" role="presentation" id="ja-user-register-tab"><a href="#ja-user-register" aria-controls="ja-user-register" role="tab" data-toggle="tab"><?php echo JText::_('REGISTER');?></a></li>
            </ul>
        </div>
        <div class="tab-content clearfix">
	        <!-- LOGIN FORM CONTENT-->
					<div class="tab-pane active" id="ja-user-login">
						<?php if(JPluginHelper::isEnabled('authentication', 'openid')) : ?>
				    	<?php JHTML::_('script', 'openid.js'); ?>
				    <?php endif; ?>
					  <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="form-login" id="login-form" >
							<div class="pretext">
								<?php echo $params->get('pretext'); ?>
							</div>
							<fieldset class="userdata">
								<p id="form-login-username">
									<label for="modlgn-username"><?php echo JText::_('JAUSERNAME') ?></label>
									<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
								</p>
								<p id="form-login-password">
									<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
									<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
								</p>
								<?php if (!is_null($tfa) && $tfa != array()):?>
								<p class="login-input secretkey">
									<label class="" for="secretkey" id="secretkey-lbl" aria-invalid="false"><?php echo JText::_('JASECRETKEY') ?></label>
									<input type="text" size="25" value="" id="secretkey" name="secretkey">
								</p>
								<?php endif; ?>
								<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
								<p id="form-login-remember">
									<label for="modlgn-remember"><?php echo JText::_('JAREMEMBER_ME') ?></label>
									<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
								</p>
								<?php endif; ?>
							</fieldset>
							<div class="action-button">
								<ul class="list-styled style-3">
									<li>
										<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
										<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
									</li>
									<li>
										<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
										<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
									</li>
									<?php
									$usersConfig = JComponentHelper::getParams('com_users');
									if ($usersConfig->get('allowUserRegistration')) : ?>
									<li>
										<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
											<?php echo JText::_('REGISTER'); ?></a>
									</li>
									<?php endif; ?>
								</ul>
						        <?php echo $params->get('posttext'); ?>
						    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><?php echo JText::_('CLOSE'); ?></button>
								<button class="button btn btn-primary" ><?php echo JText::_('JABUTTON_LOGIN'); ?></button>
								<input type="hidden" name="option" value="com_users" />
								<input type="hidden" name="task" value="user.login" />
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<?php echo JHTML::_('form.token'); ?>
							</div>
					    </form>
				    </div>
				    <!-- //LOGIN FORM CONTENT-->
	      
				    <!-- Register FORM content-->
						<div class="tab-pane <?php if(!empty($captchatext)) echo 'hascaptcha'; ?>" id="ja-user-register"  >
							<?php
							JHTML::_('behavior.keepalive');
							JHTML::_('behavior.formvalidation');
							?>

							<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
								<fieldset>
								<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
									<legend><?php echo JText::_($fieldset->label);?></legend>
								<?php endif;?>
									<dl>
										<dt>
											<label  class="required" for="jform_name" id="jform_name-lbl" title=""><?php echo JText::_( 'JANAME' ); ?>:</label>
											<em> (*)</em>
										</dt>						
										<dd><input type="text" size="30" class="required inputbox" value="" id="jform_name" name="jform[name]"></dd>

										<dt>
											<label title="" class="required" for="jform_username" id="jform_username-lbl"><?php echo JText::_( 'JAUSERNAME' ); ?>:</label>
											<em> (*)</em>	
										</dt>						
										<dd><input type="text" size="30" class="validate-username required inputbox" value="" id="jform_username" name="jform[username]"></dd>

										<dt>
											<label title="" class="required" for="jform_password1" id="jform_password1-lbl"><?php echo JText::_( 'JGLOBAL_PASSWORD' ); ?>:</label>
											<em> (*)</em>
										</dt>						
										<dd><input type="password" size="30" class="validate-password required inputbox" autocomplete="off" value="" id="jform_password1" name="jform[password1]"></dd>
										
										<dt>
											<label title="" class="required" for="jform_password2" id="jform_password2-lbl"><?php echo JText::_( 'JGLOBAL_REPASSWORD' ); ?>:</label>
											<em> (*)</em>
										</dt>						
										<dd><input type="password" size="30" class="validate-password required inputbox" autocomplete="off" value="" id="jform_password2" name="jform[password2]"></dd>
										
										<dt>
											<label title="" class="required" for="jform_email1" id="jform_email1-lbl"><?php echo JText::_( 'JAEMAIL' ); ?>:</label>
											<em> (*)</em>	
										</dt>						
										<dd><input type="text" size="30" class="validate-email required inputbox" value="" id="jform_email1" name="jform[email1]"></dd>
										
										<dt>
											<label title="" class="required" for="jform_email2" id="jform_email2-lbl"><?php echo JText::_( 'JACONFIRM_EMAIL_ADDRESS'); ?>:</label>
											<em> (*)</em>	
										</dt>						
										<dd><input type="text" size="30" class="validate-email required inputbox" value="" id="jform_email2" name="jform[email2]"></dd>
										
										<?php  if(!empty($captchatext)) { ?>
										<dt>
											<label title="" class="required"  id="jform_captcha-lbl"><?php echo JText::_( 'JACAPTCHA'); ?>:</label>
											<em> (*)</em>	
										</dt>						
										<dd><?php echo $captchatext; ?></dd>
										<?php } ?>
									</dl>
								</fieldset>
								
								<div class="action-button">
									<p><?php echo JText::_("DESC_REQUIREMENT"); ?></p>
									<button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><?php echo JText::_('CLOSE'); ?></button>
									<button type="submit" class="validate btn btn-primary"><?php echo JText::_('JAREGISTER');?></button>
									<div>
										<input type="hidden" name="option" value="com_users" />
										<input type="hidden" name="task" value="registration.register" />
										<?php echo JHTML::_('form.token');?>
									</div>
								</div>
							</form>
								<!-- Old code -->
						</div>
						<!-- //Register FORM content-->
					</div>
      </div>
    </div>
  </div>
</div>
<ul class="ja-login<?php echo $params->get('moduleclass_sfx','')?>">
	<?php if($type == 'logout') : ?>
		<li>
			<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="form-login" id="login-form">
				<?php if ($params->get('greeting')) : ?>
					<div class="login-greeting">
					<?php if($params->get('name') == 0) :
						echo JText::sprintf('HINAME', $user->get('username'));
					 else :
						echo JText::sprintf('HINAME', $user->get('name'));
					 endif; ?>
					</div>
				<?php endif; ?>
				<div class="logout-button">
					<input type="submit" name="Submit" class="button btn" value="<?php echo JText::_('JLOGOUT'); ?>" />
				</div>

				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.logout" />
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
				<?php echo JHTML::_('form.token');?>
			</form>
		</li>
		<?php else : ?>
		<li>
			<a class="btn btn-primary login-switch" href="#" data-toggle="modal" data-target="#ja-login-form" title="<?php echo JText::_('TXT_LOGIN');?>"><span><?php echo JText::_('TXT_LOGIN');?></span></a>
		</li>
		<?php
		$option = JRequest::getCmd('option');
		$task = JRequest::getCmd('task');
		if($option!='com_user' && $task != 'register' && $params->get('show_register_form', 1)) 
			{ ?>
		<li>
			<a class="register-switch" href="#" data-toggle="modal" data-target="#ja-login-form" ><span><?php echo JText::_('REGISTER');?></span></a>
		</li>
		<?php } ?>
	<?php endif; ?>
</ul>

<script>
(function($){
$(document).ready(function(){
		$('.login-switch').on('click',  function(){
			$('#ja-user-login-tab, #ja-user-register-tab, #ja-user-login, #ja-user-register').removeClass('active');
	    $('#ja-user-login-tab, #ja-user-login').addClass('active');
		});

		$('.register-switch').on('click',  function(){
			$('#ja-user-login-tab, #ja-user-register-tab, #ja-user-login, #ja-user-register').removeClass('active');
	    $('#ja-user-register-tab, #ja-user-register').addClass('active');
		});
	});
})(jQuery);
</script>