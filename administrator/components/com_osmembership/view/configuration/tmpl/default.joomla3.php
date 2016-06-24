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
	
JToolBarHelper::title(   JText::_( 'Configuration' ), 'generic.png' );
JToolBarHelper::apply();
JToolBarHelper::save('save');
JToolBarHelper::cancel('cancel');

if (JFactory::getUser()->authorise('core.admin', 'com_osmembership'))
{
    JToolBarHelper::preferences('com_osmembership');
}

JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");
$editor = JFactory::getEditor() ;
$config = $this->config;
?>
<form action="index.php?option=com_osmembership&view=configuration" method="post" name="adminForm" id="adminForm" class="form-horizontal osm-configuration">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('OSM_GENERAL');?></a></li>					
			<li><a href="#invoice-page" data-toggle="tab"><?php echo JText::_('OSM_INVOICE_SETTINGS');?></a></li>
		</ul>	
		<div class="tab-content">			
			<div class="tab-pane active row-fluid" id="general-page">
				<div class="span6">
					<fieldset class="form-horizontal">
						<legend><?php echo JText::_('OSM_SUBSCRIPTION_SETTINGS'); ?></legend>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('registration_integration', JText::_('OSM_REGISTRATION_INTEGRATION'), JText::_('OSM_REGISTRATION_INTEGRATION_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('registration_integration', $config->registration_integration); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('use_email_as_username', JText::_('OSM_USE_EMAIL_AS_USERNAME'), JText::_('OSM_USE_EMAIL_AS_USERNAME_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('use_email_as_username', $config->use_email_as_username); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('create_account_when_membership_active', JText::_('OSM_CREATE_ACCOUNT_WHEN_MEMBERSHIP_ACTIVE'), JText::_('OSM_CREATE_ACCOUNT_WHEN_MEMBERSHIP_ACTIVE_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('create_account_when_membership_active', $config->create_account_when_membership_active); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('send_activation_email', JText::_('OSM_SEND_ACTIVATION_EMAIL'), JText::_('OSM_SEND_ACTIVATION_EMAIL_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('send_activation_email', $config->send_activation_email); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('auto_login', JText::_('OSM_AUTO_LOGIN'), JText::_('OSM_AUTO_LOGIN_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('auto_login', $config->auto_login); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('auto_reload_user', JText::_('OSM_AUTO_RELOAD_USER'), JText::_('OSM_AUTO_RELOAD_USER_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('auto_reload_user', $config->auto_reload_user); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('synchronize_data', JText::_('OSM_SYNCHRONIZE_DATA'), JText::_('OSM_SYNCHRONIZE_DATA_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('synchronize_data', $config->synchronize_data); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('show_login_box_on_subscribe_page', JText::_('OSM_SHOW_LOGIN_BOX'), JText::_('OSM_SHOW_LOGIN_BOX')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('show_login_box_on_subscribe_page', $config->show_login_box_on_subscribe_page); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('number_days_before_renewal', JText::_('OSM_ALLOW_RENEWAL'), JText::_('OSM_ALLOW_RENEWAL_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="number_days_before_renewal" class="input-mini" value="<?php echo (int)$this->config->number_days_before_renewal; ?>" size="10" />
								<?php echo JText::_('OSM_DAYS_BEFORE_SUBSCRIPTION_EXPIRED'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('enable_captcha', JText::_('OSM_ENABLE_CAPTCHA'), ''); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('enable_captcha', $config->enable_captcha); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('enable_coupon', JText::_('OSM_ENABLE_COUPON'), ''); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('enable_coupon', $config->enable_coupon); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('auto_generate_membership_id', JText::_('OSM_GENERATE_MEMBERSHIP_ID'), JText::_('OSM_GENERATE_MEMBERSHIP_ID_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('auto_generate_membership_id', $config->auto_generate_membership_id); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('membership_id_prefix', JText::_('OSM_MEMBERSHIP_ID_PREFIX'), JText::_('OSM_MEMBERSHIP_ID_PREFIX_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="membership_id_prefix" class="input-medium" value="<?php echo $this->config->membership_id_prefix; ?>"/>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('reset_membership_id', JText::_('OSM_RESET_MEMBERSHIP_ID'), JText::_('OSM_RESET_MEMBERSHIP_ID_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('reset_membership_id', $config->reset_membership_id); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('membership_id_start_number', JText::_('OSM_MEMBERSHIP_ID_START_NUMBER'), JText::_('OSM_MEMBERSHIP_ID_START_NUMBER_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="membership_id_start_number" class="inputbox" value="<?php echo $config->membership_id_start_number ? $config->membership_id_start_number : 1000; ?>" size="10" />
							</div>
						</div>
					</fieldset>
					<fieldset class="form-horizontal">
						<legend><?php echo JText::_('OSM_MAIL_SETTINGS'); ?></legend>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('from_name', JText::_('OSM_FROM_NAME'), JText::_('OSM_FROM_NAME_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="from_name" class="input-xlarge" value="<?php echo $this->config->from_name; ?>" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('from_email', JText::_('OSM_FROM_EMAIL'), JText::_('OSM_FROM_EMAIL_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="from_email" class="input-xlarge" value="<?php echo $this->config->from_email; ?>" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('disable_notification_to_admin', JText::_('OSM_DISABLE_NOTIFICATION_TO_ADMIN'), JText::_('OSM_DISABLE_NOTIFICATION_TO_ADMIN_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('disable_notification_to_admin', $config->disable_notification_to_admin); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('notification_emails', JText::_('OSM_NOTIFICATION_EMAILS'), JText::_('OSM_NOTIFICATION_EMAILS_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="notification_emails" class="input-xlarge" value="<?php echo $this->config->notification_emails; ?>" />
							</div>
						</div>
					</fieldset>
				</div>
				<div class="span6">
					<fieldset class="form-horizontal">
						<legend><?php echo JText::_('OSM_THEME_SETTINGS'); ?></legend>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('load_jquery', JText::_('OSM_LOAD_JQUERY_IN_FRONTEND'), JText::_('OSM_LOAD_JQUERY_IN_FRONTEND_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('load_jquery', isset($config->load_jquery) ? $config->load_jquery : 1); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('load_twitter_bootstrap_in_frontend', JText::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND'), JText::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('load_twitter_bootstrap_in_frontend', isset($config->load_twitter_bootstrap_in_frontend) ? $config->load_twitter_bootstrap_in_frontend : 1); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('twitter_bootstrap_version', JText::_('OSM_TWITTER_BOOTSTRAP_VERSION'), JText::_('OSM_TWITTER_BOOTSTRAP_VERSION_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['twitter_bootstrap_version'];?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('show_price_including_tax', JText::_('OSM_SHOW_PRICE_INCLUDING_TAX'), ''); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('show_price_including_tax', $config->show_price_including_tax); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('hide_details_button', JText::_('OSM_HIDE_DETAILS_BUTTON'), JText::_('OSM_HIDE_DETAILS_BUTTON_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('hide_details_button', $config->hide_details_button); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('date_format', JText::_('OSM_DATE_FORMAT'), ''); ?>
							</div>
							<div class="controls">
								<input type="text" name="date_format" class="inputbox" value="<?php echo $this->config->date_format; ?>" size="10" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('date_field_format', JText::_('OSM_DATE_FIELD_FORMAT'), JText::_('OSM_DATE_FIELD_FORMAT_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['date_field_format']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('currency_code', JText::_('OSM_CURRENCY')); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['currency_code']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('currency_symbol', JText::_('OSM_CURRENCY_SYMBOL'), ''); ?>
							</div>
							<div class="controls">
								<input type="text" name="currency_symbol" class="inputbox" value="<?php echo $this->config->currency_symbol; ?>" size="10" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('decimals', JText::_('OSM_DECIMALS'), JText::_('OSM_DECIMALS_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="decimals" class="inputbox" value="<?php echo isset($this->config->decimals) ? $this->config->decimals : 2; ?>" size="10" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('dec_point', JText::_('OSM_DECIMAL_POINT'), JText::_('OSM_DECIMAL_POINT_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="dec_point" class="inputbox" value="<?php echo isset($this->config->dec_point) ? $this->config->dec_point : '.'; ?>" size="10" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('thousands_sep', JText::_('OSM_THOUNSANDS_SEP'), JText::_('OSM_THOUNSANDS_SEP_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="thousands_sep" class="inputbox" value="<?php echo isset($this->config->thousands_sep) ? $this->config->thousands_sep : ','; ?>" size="10" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('currency_position', JText::_('OSM_CURRENCY_POSITION'), ''); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['currency_position']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('number_columns', JText::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT'), JText::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="number_columns" class="inputbox" value="<?php echo $this->config->number_columns ? $this->config->number_columns : 3 ; ?>" size="10" />
							</div>
						</div>
					</fieldset>
					<fieldset class="form-horizontal">
						<legend><?php echo JText::_('OSM_OTHER_SETTINGS'); ?></legend>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('send_attachments_to_admin', JText::_('OSM_SEND_ATTACHMENTS_TO_ADMIN'), JText::_('OSM_SEND_ATTACHMENTS_TO_ADMIN_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('send_attachments_to_admin', $config->send_attachments_to_admin); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('use_https', JText::_('OSM_ACTIVATE_HTTPS'), ''); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('use_https', $config->use_https); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('country_list', JText::_('OSM_DEFAULT_COUNTRY'), ''); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['country_list']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('eu_vat_number_field', JText::_('OSM_EU_VAT_NUMBER_FIELD'), JText::_('OSM_EU_VAT_NUMBER_FIELD_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['eu_vat_number_field']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('article_id', JText::_('OSM_TERMS_AND_CONDITIONS_ARTICLE'), ''); ?>
							</div>
							<div class="controls">
								<?php echo $this->lists['article_id']; ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('fix_terms_and_conditions_popup', JText::_('OSM_FIX_TERMS_AND_CONDITIONS'), JText::_('OSM_FIX_TERMS_AND_CONDITIONS_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('fix_terms_and_conditions_popup', $config->fix_terms_and_conditions_popup); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('allowed_file_types', JText::_('OSM_ALLOWED_FILE_TYPES'), JText::_('OSM_ALLOWED_FILE_TYPES_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<input type="text" name="allowed_file_types" class="input-xlarge" value="<?php echo $this->config->allowed_file_types; ?>" size="50" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('conversion_tracking_code', JText::_('OSM_CONVERSION_TRACKING_CODE'), JText::_('OSM_CONVERSION_TRACKING_CODE_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<textarea name="conversion_tracking_code" class="input-xlarge" rows="10"><?php echo $this->config->conversion_tracking_code;?></textarea>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo OSMembershipHelperHtml::getFieldLabel('debug', JText::_('OSM_DEBUG'), JText::_('OSM_DEBUG_EXPLAIN')); ?>
							</div>
							<div class="controls">
								<?php echo OSMembershipHelperHtml::getBooleanInput('debug', $config->debug); ?>
							</div>
						</div>
					</fieldset>
				</div>
			</div>			
			<div class="tab-pane" id="invoice-page">
				<fieldset class="form-horizontal">
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('activate_invoice_feature', JText::_('OSM_ACTIVATE_INVOICE_FEATURE'), JText::_('OSM_ACTIVATE_INVOICE_FEATURE_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<?php echo OSMembershipHelperHtml::getBooleanInput('activate_invoice_feature', $config->activate_invoice_feature); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('send_invoice_to_customer', JText::_('OSM_SEND_INVOICE_TO_SUBSCRIBERS'), JText::_('OSM_SEND_INVOICE_TO_SUBSCRIBERS_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<?php echo OSMembershipHelperHtml::getBooleanInput('send_invoice_to_customer', $config->send_invoice_to_customer); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('send_invoice_to_admin', JText::_('OSM_SEND_COPY_OF_INVOICE_TO_ADMIN'), JText::_('OSM_SEND_COPY_OF_INVOICE_TO_ADMIN_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<?php echo OSMembershipHelperHtml::getBooleanInput('send_invoice_to_admin', $config->send_invoice_to_admin); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('invoice_start_number', JText::_('OSM_INVOICE_START_NUMBER'), JText::_('OSM_INVOICE_START_NUMBER_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<input type="text" name="invoice_start_number" class="inputbox" value="<?php echo $this->config->invoice_start_number ? $this->config->invoice_start_number : 1; ?>" size="10" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('reset_invoice_number', JText::_('OSM_RESET_INVOICE_NUMBER_EVERY_YEAR'), JText::_('OSM_RESET_INVOICE_NUMBER_EVERY_YEAR_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<?php echo OSMembershipHelperHtml::getBooleanInput('reset_invoice_number', $config->reset_invoice_number); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('invoice_prefix', JText::_('OSM_INVOICE_PREFIX'), JText::_('OSM_INVOICE_PREFIX_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<input type="text" name="invoice_prefix" class="inputbox" value="<?php echo isset($this->config->invoice_prefix) ? $this->config->invoice_prefix : 'IV'; ?>" size="10" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('invoice_number_length', JText::_('OSM_INVOICE_NUMBER_LENGTH'), JText::_('OSM_INVOICE_NUMBER_LENGTH_EXPLAIN')); ?>
						</div>
						<div class="controls">
							<input type="text" name="invoice_number_length" class="inputbox" value="<?php echo $this->config->invoice_number_length ? $this->config->invoice_number_length : 5; ?>" size="10" />
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo OSMembershipHelperHtml::getFieldLabel('invoice_format', JText::_('OSM_INVOICE_FORMAT'), ''); ?>
						</div>
						<div class="controls">
							<?php echo $editor->display( 'invoice_format',  $this->config->invoice_format , '100%', '550', '75', '8' ) ;?>
						</div>
					</div>
				</fieldset>
			</div>
		</div>	
	</div>
	<input type="hidden" name="task" value="" />
	<div class="clearfix"></div>	
</form>