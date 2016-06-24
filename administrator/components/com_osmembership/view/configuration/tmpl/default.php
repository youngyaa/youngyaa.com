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
$editor = JFactory::getEditor() ;
?>
<form action="index.php?option=com_osmembership&view=configuration" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('OSM_GENERAL');?></a></li>					
			<li><a href="#invoice-page" data-toggle="tab"><?php echo JText::_('OSM_INVOICE_SETTINGS');?></a></li>
		</ul>	
		<div class="tab-content">			
			<div class="tab-pane active" id="general-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_LOAD_JQUERY_IN_FRONTEND'); ?>
						</td>
						<td>
							<?php echo $this->lists['load_jquery']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_LOAD_JQUERY_IN_FRONTEND_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND'); ?>
						</td>
						<td>
							<?php echo $this->lists['load_twitter_bootstrap_in_frontend']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="15%">
							<?php echo JText::_('OSM_TWITTER_BOOTSTRAP_VERSION') ; ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['twitter_bootstrap_version'];?>
						</td>
						<td>
							<?php echo JText::_('OSM_TWITTER_BOOTSTRAP_VERSION_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_REGISTRATION_INTEGRATION'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['registration_integration']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_REGISTRATION_INTEGRATION_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_USE_EMAIL_AS_USERNAME'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['use_email_as_username']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_USE_EMAIL_AS_USERNAME_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_AUTO_LOGIN'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['auto_login']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_AUTO_LOGIN_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_AUTO_RELOAD_USER'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['auto_reload_user']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_AUTO_RELOAD_USER_EXPLAIN'); ?>
						</td>
					</tr>

                    <tr>
                        <td  class="key" style="width:25%">
                            <?php echo JText::_('OSM_CREATE_ACCOUNT_WHEN_MEMBERSHIP_ACTIVE'); ?>
                        </td>
                        <td width="40%">
                            <?php echo $this->lists['create_account_when_membership_active']; ?>
                        </td>
                        <td>
                            <?php echo JText::_('OSM_CREATE_ACCOUNT_WHEN_MEMBERSHIP_ACTIVE_EXPLAIN'); ?>
                        </td>
                    </tr>

					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_SEND_ACTIVATION_EMAIL'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['send_activation_email']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SEND_ACTIVATION_EMAIL_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_SYNCHRONIZE_DATA'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['synchronize_data']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SYNCHRONIZE_DATA_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="25%">
							<?php echo JText::_('OSM_FROM_NAME'); ?>					
						</td>
						<td>
							<input type="text" name="from_name" class="inputbox" value="<?php echo $this->config->from_name; ?>" size="50" />
						</td>
						<td>
							<strong><?php echo JText::_('OSM_FROM_NAME_EXPLAIN'); ?></strong>
						</td>
					</tr>			
					<tr>
						<td class="key" width="25%">
							<?php echo JText::_('OSM_FROM_EMAIL'); ?>					
						</td>
						<td>
							<input type="text" name="from_email" class="inputbox" value="<?php echo $this->config->from_email; ?>" size="50" />
						</td>
						<td>
							<strong><?php echo JText::_('OSM_FROM_EMAIL_EXPLAIN'); ?></strong>
						</td>
					</tr>
					<tr>
						<td class="key" width="25%">
							<?php echo JText::_('OSM_DISABLE_NOTIFICATION_TO_ADMIN'); ?>
						</td>
						<td>
							<?php echo $this->lists['disable_notification_to_admin'];?>
						</td>
						<td>
							<strong><?php echo JText::_('OSM_DISABLE_NOTIFICATION_TO_ADMIN_EXPLAIN'); ?></strong>
						</td>
					</tr>
					<tr>
						<td class="key" width="25%">
							<?php echo JText::_('OSM_NOTIFICATION_EMAILS'); ?>					
						</td>
						<td>
							<input type="text" name="notification_emails" class="inputbox" value="<?php echo $this->config->notification_emails; ?>" size="50" />
						</td>
						<td>
							<strong><?php echo JText::_('OSM_NOTIFICATION_EMAILS_EXPLAIN'); ?></strong>
						</td>
					</tr>				
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_GENERATE_MEMBERSHIP_ID'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['auto_generate_membership_id']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_GENERATE_MEMBERSHIP_ID_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_MEMBERSHIP_ID_PREFIX'); ?>
						</td>
						<td width="40%">
							<input type="text" name="membership_id_prefix" class="inputbox" value="<?php echo $this->config->membership_id_prefix; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_MEMBERSHIP_ID_PREFIX_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_RESET_MEMBERSHIP_ID'); ?>
						</td>
						<td width="40%">
							<?php echo $this->lists['reset_membership_id'];?>
						</td>
						<td>
							<?php echo JText::_('OSM_RESET_MEMBERSHIP_ID_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_MEMBERSHIP_ID_START_NUMBER'); ?>
						</td>
						<td width="40%">
							<input type="text" name="membership_id_start_number" class="inputbox" value="<?php echo $this->config->membership_id_start_number ? $this->config->membership_id_start_number : 1000; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_MEMBERSHIP_ID_START_NUMBER_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key">
							<?php echo JText::_('OSM_SHOW_LOGIN_BOX'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_login_box_on_subscribe_page']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SHOW_LOGIN_BOX_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key">
							<?php echo JText::_('OSM_ENABLE_CAPTCHA'); ?>
						</td>
						<td>
							<?php echo $this->lists['enable_captcha']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ENABLE_COUPON'); ?>
						</td>
						<td>
							<?php echo $this->lists['enable_coupon']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SHOW_PRICE_INCLUDING_TAX'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_price_including_tax']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_HIDE_DETAILS_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_price_including_tax']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_HIDE_DETAILS_BUTTON_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_EU_VAT_NUMBER_FIELD'); ?>
						</td>
						<td>
							<?php echo $this->lists['eu_vat_number_field']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_EU_VAT_NUMBER_FIELD_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ALLOWED_FILE_TYPES') ; ?>
						</td>
						<td>
							<input type="text" name="allowed_file_types" class="inputbox" value="<?php echo $this->config->allowed_file_types; ?>" size="50" />
						</td>
						<td>
							<?php echo JText::_('OSM_ALLOWED_FILE_TYPES_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ACTIVATE_HTTPS'); ?>
						</td>
						<td>
							<?php echo $this->lists['use_https']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_TERMS_AND_CONDITIONS_ARTICLE') ; ?>
						</td>
						<td>
							<?php echo $this->lists['article_id']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_FIX_TERMS_AND_CONDITIONS') ?>
						</td>
						<td>
							<?php
							echo $this->lists['fix_terms_and_conditions_popup'];
							?>
						</td>
						<td>
							<?php echo JText::_('OSM_FIX_TERMS_AND_CONDITIONS_EXPLAIN') ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DATE_FORMAT') ; ?>					
						</td>
						<td>
							<input type="text" name="date_format" class="inputbox" value="<?php echo $this->config->date_format; ?>" size="10" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DATE_FIELD_FORMAT') ; ?>
						</td>
						<td>
							<?php echo $this->lists['date_field_format']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_DATE_FIELD_FORMAT_EXPLAIN') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_CURRENCY'); ?>
						</td>
						<td>
							<?php echo $this->lists['currency_code']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_CURRENCY_SYMBOL'); ?>
						</td>
						<td>
							<input type="text" name="currency_symbol" class="inputbox" value="<?php echo $this->config->currency_symbol; ?>" size="10" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DECIMALS'); ?>
						</td>
						<td>
							<input type="text" name="decimals" class="inputbox" value="<?php echo isset($this->config->decimals) ? $this->config->decimals : 2; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_DECIMALS_EXPLAIN'); ?>
						</td>
					</tr>
					
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DECIMAL_POINT'); ?>
						</td>
						<td>
							<input type="text" name="dec_point" class="inputbox" value="<?php echo isset($this->config->dec_point) ? $this->config->dec_point : '.'; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_DECIMAL_POINT_EXPLAIN'); ?>
						</td>
					</tr>
					
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_THOUNSANDS_SEP'); ?>
						</td>
						<td>
							<input type="text" name="thousands_sep" class="inputbox" value="<?php echo isset($this->config->thousands_sep) ? $this->config->thousands_sep : ','; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_THOUNSANDS_SEP_EXPLAIN'); ?>
						</td>
					</tr>
					
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_CURRENCY_POSITION'); ?>
						</td>
						<td>
							<?php echo $this->lists['currency_position']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT'); ?>
						</td>
						<td>
							<input type="text" name="number_columns" class="inputbox" value="<?php echo $this->config->number_columns ? $this->config->number_columns : 3 ; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT_EXPLAIN'); ?>
						</td>
					</tr>								
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_SEND_ATTACHMENTS_TO_ADMIN'); ?>
						</td>
						<td>
							<?php echo $this->lists['send_attachments_to_admin']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SEND_ATTACHMENTS_TO_ADMIN_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DEFAULT_COUNTRY'); ?>
						</td>
						<td>
							<?php echo $this->lists['country_list']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>					
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_ALLOW_RENEWAL'); ?>
						</td>										
						<td>
							<input type="text" name="number_days_before_renewal" class="input-mini" value="<?php echo (int)$this->config->number_days_before_renewal; ?>" size="10" />							
							<?php echo JText::_('OSM_DAYS_BEFORE_SUBSCRIPTION_EXPIRED'); ?>
						</td>										
						<td>
							<?php echo JText::_('OSM_ALLOW_RENEWAL_EXPLAIN'); ?>
						</td>	
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('OSM_CONVERSION_TRACKING_CODE'); ?>
						</td>
						<td>
							<textarea name="conversion_tracking_code" class="input-xlarge" rows="10"><?php echo $this->config->conversion_tracking_code;?></textarea>
						</td>
						<td>
							<?php echo JText::_('OSM_CONVERSION_TRACKING_CODE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_DEBUG'); ?>
						</td>
						<td>
							<?php echo $this->lists['debug']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_DEBUG_EXPLAIN');?>
						</td>
					</tr>
				</table>
			</div>			
			<div class="tab-pane" id="invoice-page">
				<table class="admintable adminform" style="width:100%;">
					<tr>
						<td  class="key" width="10%">
							<?php echo JText::_('OSM_ACTIVATE_INVOICE_FEATURE'); ?>
						</td>
						<td width="60%">
							<?php echo $this->lists['activate_invoice_feature']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_ACTIVATE_INVOICE_FEATURE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" width="10%">
							<?php echo JText::_('OSM_SEND_INVOICE_TO_SUBSCRIBERS'); ?>
						</td>
						<td width="60%">
							<?php echo $this->lists['send_invoice_to_customer']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SEND_INVOICE_TO_SUBSCRIBERS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" width="10%">
							<?php echo JText::_('OSM_SEND_COPY_OF_INVOICE_TO_ADMIN'); ?>
						</td>
						<td width="60%">
							<?php echo $this->lists['send_invoice_to_admin']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_SEND_COPY_OF_INVOICE_TO_ADMIN_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key">
							<?php echo JText::_('OSM_INVOICE_START_NUMBER'); ?>
						</td>
						<td>
							<input type="text" name="invoice_start_number" class="inputbox" value="<?php echo $this->config->invoice_start_number ? $this->config->invoice_start_number : 1; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_INVOICE_START_NUMBER_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" width="10%">
							<?php echo JText::_('OSM_RESET_INVOICE_NUMBER_EVERY_YEAR'); ?>
						</td>
						<td width="60%">
							<?php echo $this->lists['reset_invoice_number']; ?>
						</td>
						<td>
							<?php echo JText::_('OSM_RESET_INVOICE_NUMBER_EVERY_YEAR_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_INVOICE_PREFIX'); ?>
						</td>
						<td>
							<input type="text" name="invoice_prefix" class="inputbox" value="<?php echo isset($this->config->invoice_prefix) ? $this->config->invoice_prefix : 'IV'; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_INVOICE_PREFIX_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td  class="key" style="width:25%">
							<?php echo JText::_('OSM_INVOICE_NUMBER_LENGTH'); ?>
						</td>
						<td>
							<input type="text" name="invoice_number_length" class="inputbox" value="<?php echo $this->config->invoice_number_length ? $this->config->invoice_number_length : 5; ?>" size="10" />
						</td>
						<td>
							<?php echo JText::_('OSM_INVOICE_NUMBER_LENGTH_EXPLAIN'); ?>
						</td>
					</tr>																						
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_INVOICE_FORMAT'); ?>
						</td>
						<td>
							<?php echo $editor->display( 'invoice_format',  $this->config->invoice_format , '100%', '550', '75', '8' ) ;?>					
						</td>
						<td>
							&nbsp;
						</td>				
					</tr>
				</table>	
			</div>
		</div>	
	</div>
	<input type="hidden" name="task" value="" />
	<div class="clearfix"></div>	
</form>