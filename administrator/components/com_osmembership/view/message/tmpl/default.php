<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
	
JToolBarHelper::title(   JText::_( 'OSM_EMAIL_MESSAGES' ), 'generic.png' );
JToolBarHelper::apply();
JToolBarHelper::save('save');
JToolBarHelper::cancel('cancel');
$editor = JFactory::getEditor() ;
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
?>
<form action="index.php?option=com_osmembership&view=message" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('OSM_GENERAL_MESSAGES'); ?></a></li>
			<li><a href="#renewal-page" data-toggle="tab"><?php echo JText::_('OSM_RENEWAL_MESSAGES'); ?></a></li>
			<li><a href="#upgrade-page" data-toggle="tab"><?php echo JText::_('OSM_UPGRADE_MESSAGES'); ?></a></li>
			<li><a href="#recurring-page" data-toggle="tab"><?php echo JText::_('OSM_RECURRING_MESSAGES'); ?></a></li>
			<li><a href="#reminder-page" data-toggle="tab"><?php echo JText::_('OSM_REMINDER_MESSAGES'); ?></a></li>
			<?php
			if ($translatable)
			{
			?>
				<li><a href="#translation-page" data-toggle="tab"><?php echo JText::_('OSM_TRANSLATION'); ?></a></li>
			<?php
			}
			?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="general-page">
				<table class="admintable adminform" style="width:100%;">																											
					<tr>
						<td class="key" width="20%">
							<?php echo JText::_('OSM_ADMIN_EMAIL_SUBJECT'); ?>
						</td>
						<td width="60%">
							<input type="text" name="admin_email_subject" class="input-xlarge" value="<?php echo $this->item->admin_email_subject; ?>" size="50" />
						</td>
						<td>
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ADMIN_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'admin_email_body',  $this->item->admin_email_body , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('User email subject'); ?>
						</td>
						<td>					
							<input type="text" name="user_email_subject" class="input-xlarge" value="<?php echo $this->item->user_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_USER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_email_body',  $this->item->user_email_body , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_USER_EMAIL_BODY_OFFLINE_PAYMENT'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_email_body_offline',  $this->item->user_email_body_offline , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>		
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="subscription_approved_email_subject" class="input-xlarge" value="<?php echo $this->item->subscription_approved_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'subscription_approved_email_body',  $this->item->subscription_approved_email_body , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<strong>Available Tags :[PAYMENT_DETAIL], [FORM_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
						</td>
					</tr>						
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SUBSCRIPTION_FORM_MESSAGE'); ?>														
						</td>
						<td>			
							<?php echo $editor->display( 'subscription_form_msg',  $this->item->subscription_form_msg , '100%', '250', '75', '8' ) ;?>							
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_SUBSCRIPTION_FORM_MESSAGE_EXPLAIN'); ?></strong>
						</td>
					</tr>																											
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_THANK_MESSAGE'); ?>					
						</td>
						<td>			
							<?php echo $editor->display( 'thanks_message',  $this->item->thanks_message , '100%', '250', '75', '8' ) ;?>							
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_THANK_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>								
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_THANK_MESSAGE_OFFLINE'); ?>					
						</td>
						<td>			
							<?php echo $editor->display( 'thanks_message_offline',  $this->item->thanks_message_offline , '100%', '250', '75', '8' ) ;?>							
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_THANK_MESSAGE_OFFLINE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_PAYMENT_CANCEL_MESSAGE'); ?>					
						</td>
						<td>
							<?php echo $editor->display( 'cancel_message',  $this->item->cancel_message , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_PAYMENT_CANCEL_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_PAYMENT_FAILURE_MESSAGE'); ?>					
						</td>
						<td>
							<?php echo $editor->display( 'failure_message',  $this->item->failure_message , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_PAYMENT_FAILURE_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_CONTENT_RESTRICTED_MESSAGE'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'content_restricted_message',  $this->item->content_restricted_message , '100%', '250', '75', '8' ) ;?>					
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_CONTENT_RESTRICTED_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>											
				</table>
			</div>
			<div class="tab-pane" id="renewal-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td class="key" width="20%">
							<?php echo JText::_('OSM_SUBSCRIPTION_RENEW_FORM_MESSAGE'); ?>
						</td>
						<td width="60%">
							<?php echo $editor->display( 'subscription_renew_form_msg',  $this->item->subscription_renew_form_msg , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_SUBSCRIPTION_RENEW_FORM_MESSAGE_EXPLAIN'); ?></strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_NENEW_ADMIN_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="admin_renw_email_subject" class="input-xlarge" value="<?php echo $this->item->admin_renw_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_ADMIN_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'admin_renew_email_body',  $this->item->admin_renew_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_USER_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="user_renew_email_subject" class="input-xlarge" value="<?php echo $this->item->user_renew_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_USER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_renew_email_body',  $this->item->user_renew_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_USER_EMAIL_BODY_OFFLINE'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_renew_email_body_offline',  $this->item->user_renew_email_body_offline , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_THANK_MESSAGE'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'renew_thanks_message',  $this->item->renew_thanks_message , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_OFFLINE'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'renew_thanks_message_offline',  $this->item->renew_thanks_message_offline , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_OFFLINE_EXPLAIN'); ?>
						</td>
					</tr>

				</table>
			</div>
			<div class="tab-pane" id="upgrade-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td class="key" width="20%">
							<?php echo JText::_('OSM_SUBSCRIPTION_UPGRADE_FORM_MESSAGE'); ?>
						</td>
						<td width="60%">
							<?php echo $editor->display( 'subscription_upgrade_form_msg',  $this->item->subscription_upgrade_form_msg , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_SUBSCRIPTION_UPGRADE_FORM_MESSAGE_EXPLAIN'); ?></strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_UPGRADE_ADMIN_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="admin_upgrade_email_subject" class="input-xlarge" value="<?php echo $this->item->admin_upgrade_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [TO_PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_UPGRADE_ADMIN_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'admin_upgrade_email_body',  $this->item->admin_upgrade_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_UPGRADE_USER_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="user_upgrade_email_subject" class="input-xlarge" value="<?php echo $this->item->user_upgrade_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [TO_PLAN_TITLE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_UPGRADE_USER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_upgrade_email_body',  $this->item->user_upgrade_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_UPGRADE_THANK_MESSAGE'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'upgrade_thanks_message',  $this->item->upgrade_thanks_message , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_UPGRADE_THANK_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="recurring-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td class="key" width="20%">
							<?php echo JText::_('OSM_RECURRING_SUBSCRIPTION_CANCEL_MESSAGE'); ?>
						</td>
						<td width="60%">
							<?php echo $editor->display( 'recurring_subscription_cancel_message',  $this->item->recurring_subscription_cancel_message , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_RECURRING_SUBSCRIPTION_CANCEL_MESSAGE_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_USER_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="user_recurring_subscription_cancel_subject" class="input-xlarge" value="<?php echo $this->item->user_recurring_subscription_cancel_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_USER_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_SUBJECT_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_USER_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'user_recurring_subscription_cancel_body',  $this->item->user_recurring_subscription_cancel_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_USER_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_BODY_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ADMIN_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="admin_recurring_subscription_cancel_subject" class="input-xlarge" value="<?php echo $this->item->admin_recurring_subscription_cancel_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_ADMIN_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_SUBJECT_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ADMIN_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'admin_recurring_subscription_cancel_body',  $this->item->admin_recurring_subscription_cancel_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<?php echo JText::_('OSM_ADMIN_RECURRING_SUBSCRIPTION_CANCEL_EMAIL_BODY_EXPLAIN'); ?>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="reminder-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td class="key" width="20%">
							<?php echo JText::_('OSM_FIRST_REMINDER_EMAIL_SUBJECT'); ?>
						</td>
						<td width="60%">
							<input type="text" name="first_reminder_email_subject" class="input-xlarge" value="<?php echo $this->item->first_reminder_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [NUMBER_DAYS]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_FIRST_REMINDER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'first_reminder_email_body',  $this->item->first_reminder_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [NUMBER_DAYS], [EXPIRE_DATE]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SECOND_REMINDER_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="second_reminder_email_subject" class="input-xlarge" value="<?php echo $this->item->second_reminder_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [NUMBER_DAYS]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SECOND_REMINDER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'second_reminder_email_body',  $this->item->second_reminder_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [NUMBER_DAYS], [EXPIRE_DATE]</strong>
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_THIRD_REMINDER_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="third_reminder_email_subject" class="input-xlarge" value="<?php echo $this->item->third_reminder_email_subject; ?>" size="50" />
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [NUMBER_DAYS]</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_THIRD_REMINDER_EMAIL_BODY'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'third_reminder_email_body',  $this->item->third_reminder_email_body , '100%', '250', '75', '8' ) ;?>
						</td>
						<td valign="top">
							<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [NUMBER_DAYS], [EXPIRE_DATE]</strong>
						</td>
					</tr>
				</table>
			</div>
	<?php 
	if ($translatable)
	{
	?>
		<div class="tab-pane" id="translation-page">
			<ul class="nav nav-tabs">
				<?php
					$i = 0;
					foreach ($this->languages as $language) {						
						$sef = $language->sef;
						?>
						<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#translation-page-<?php echo $sef; ?>" data-toggle="tab"><?php echo $language->title; ?>
							<img src="<?php echo JUri::root(); ?>media/com_osmembership/flags/<?php echo $sef.'.png'; ?>" /></a></li>
						<?php
						$i++;	
					}
				?>			
			</ul>		
			<div class="tab-content">			
				<?php	
					$i = 0;
					foreach ($this->languages as $language)
					{												
						$sef = $language->sef;
					?>
						<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
							<table class="admintable adminform" style="width:100%;">																											
								<tr>
									<td class="key" width="20%">
										<?php echo JText::_('OSM_ADMIN_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="admin_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'admin_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_ADMIN_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'admin_email_body_'.$sef,  $this->item->{'admin_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('User email subject'); ?>
									</td>
									<td>					
										<input type="text" name="user_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'user_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_USER_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'user_email_body_'.$sef,  $this->item->{'user_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_USER_EMAIL_BODY_OFFLINE_PAYMENT'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'user_email_body_offline_'.$sef,  $this->item->{'user_email_body_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>		
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="subscription_approved_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'subscription_approved_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'subscription_approved_email_body_'.$sef,  $this->item->{'subscription_approved_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong>Available Tags :[PAYMENT_DETAIL], [FORM_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
									</td>
								</tr>						
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SUBSCRIPTION_FORM_MESSAGE'); ?>														
									</td>
									<td>			
										<?php echo $editor->display( 'subscription_form_msg_'.$sef,  $this->item->{'subscription_form_msg_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<strong><?php echo JText::_('OSM_SUBSCRIPTION_FORM_MESSAGE_EXPLAIN'); ?></strong>
									</td>
								</tr>																											
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_THANK_MESSAGE'); ?>					
									</td>
									<td>			
										<?php echo $editor->display( 'thanks_message_'.$sef,  $this->item->{'thanks_message_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<?php echo JText::_('OSM_THANK_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>								
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_THANK_MESSAGE_OFFLINE'); ?>					
									</td>
									<td>			
										<?php echo $editor->display( 'thanks_message_offline_'.$sef,  $this->item->{'thanks_message_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<?php echo JText::_('OSM_THANK_MESSAGE_OFFLINE_EXPLAIN'); ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_PAYMENT_CANCEL_MESSAGE'); ?>					
									</td>
									<td>
										<?php echo $editor->display( 'cancel_message_'.$sef,  $this->item->{'cancel_message_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<?php echo JText::_('OSM_PAYMENT_CANCEL_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_PAYMENT_FAILURE_MESSAGE'); ?>					
									</td>
									<td>
										<?php echo $editor->display( 'failure_message_'.$sef,  $this->item->{'failure_message_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<?php echo JText::_('OSM_PAYMENT_FAILURE_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>
								
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SUBSCRIPTION_RENEW_FORM_MESSAGE'); ?>														
									</td>
									<td>			
										<?php echo $editor->display( 'subscription_renew_form_msg_'.$sef,  $this->item->{'subscription_renew_form_msg_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<strong><?php echo JText::_('OSM_SUBSCRIPTION_RENEW_FORM_MESSAGE_EXPLAIN'); ?></strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_NENEW_ADMIN_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="admin_renw_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'admin_renw_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_ADMIN_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'admin_renew_email_body_'.$sef,  $this->item->{'admin_renew_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_USER_EMAIL_SUBJECT'); ?>
									</td>
									<td>					
										<input type="text" name="user_renew_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'user_renew_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_USER_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'user_renew_email_body_'.$sef,  $this->item->{'user_renew_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_USER_EMAIL_BODY_OFFLINE'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'user_renew_email_body_offline_'.$sef,  $this->item->{'user_renew_email_body_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>

								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_THANK_MESSAGE'); ?>					
									</td>
									<td>			
										<?php echo $editor->display( 'renew_thanks_message_'.$sef,  $this->item->{'renew_thanks_message_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>

								<tr>
									<td class="key">
										<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_OFFLINE'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'renew_thanks_message_offline_'.$sef,  $this->item->{'renew_thanks_message_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>
									</td>
									<td>
										<?php echo JText::_('OSM_RENEW_THANK_MESSAGE_OFFLINE_EXPLAIN'); ?>
									</td>
								</tr>

								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SUBSCRIPTION_UPGRADE_FORM_MESSAGE'); ?>														
									</td>
									<td>			
										<?php echo $editor->display( 'subscription_upgrade_form_msg_'.$sef,  $this->item->{'subscription_upgrade_form_msg_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<strong><?php echo JText::_('OSM_SUBSCRIPTION_UPGRADE_FORM_MESSAGE_EXPLAIN'); ?></strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_UPGRADE_ADMIN_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="admin_upgrade_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'admin_upgrade_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [TO_PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_UPGRADE_ADMIN_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'admin_upgrade_email_body_'.$sef,  $this->item->{'admin_upgrade_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_UPGRADE_USER_EMAIL_SUBJECT'); ?>
									</td>
									<td>					
										<input type="text" name="user_upgrade_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'user_upgrade_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [TO_PLAN_TITLE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_UPGRADE_USER_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'user_upgrade_email_body_'.$sef,  $this->item->{'user_upgrade_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> :[SUBSCRIPTION_DETAIL], [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT], [TRANSACTION_ID], [PAYMENT_METHOD]</strong>
									</td>
								</tr>	
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_UPGRADE_THANK_MESSAGE'); ?>					
									</td>
									<td>			
										<?php echo $editor->display( 'upgrade_thanks_message_'.$sef,  $this->item->{'upgrade_thanks_message_'.$sef} , '100%', '250', '75', '8' ) ;?>							
									</td>
									<td>
										<?php echo JText::_('OSM_UPGRADE_THANK_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>						
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_FIRST_REMINDER_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="first_reminder_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'first_reminder_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [NUMBER_DAYS]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_FIRST_REMINDER_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'first_reminder_email_body_'.$sef,  $this->item->{'first_reminder_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [NUMBER_DAYS], [EXPIRE_DATE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SECOND_REMINDER_EMAIL_SUBJECT'); ?>
									</td>
									<td>
										<input type="text" name="second_reminder_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->item->{'second_reminder_email_subject_'.$sef}; ?>" size="50" />
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [NUMBER_DAYS]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_SECOND_REMINDER_EMAIL_BODY'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'second_reminder_email_body_'.$sef,  $this->item->{'second_reminder_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<strong><?php echo JText::_('OSM_AVAILABLE_TAGS'); ?> [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [NUMBER_DAYS], [EXPIRE_DATE]</strong>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_CONTENT_RESTRICTED_MESSAGE'); ?>
									</td>
									<td>
										<?php echo $editor->display( 'content_restricted_message_'.$sef,  $this->item->{'content_restricted_message_'.$sef} , '100%', '250', '75', '8' ) ;?>					
									</td>
									<td>
										<?php echo JText::_('OSM_CONTENT_RESTRICTED_MESSAGE_EXPLAIN'); ?>
									</td>
								</tr>												
							</table>
						</div>										
					<?php				
						$i++;		
					}
				?>
			</div>	
		</div>
	<?php				
	}
?>										
	</div>		
	<div class="clearfix"></div>
	<input type="hidden" name="task" value="" />	
</form>