<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;
JHtml::_('behavior.modal', 'a.osm-modal');
$selectedState = '';
?>
<script type="text/javascript">
	var siteUrl = '<?php echo OSMembershipHelper::getSiteUrl();  ?>';
</script>
<?php
OSMembershipHelperJquery::validateForm();
switch($this->action)
{
	case 'upgrade' :
		$headerText = JText::_('OSM_SUBSCRIION_UPGRADE_FORM_HEADING');
		break ;
	case 'renew' :
		$headerText = JText::_('OSM_SUBSCRIION_RENEW_FORM_HEADING');
		break ;
	default :
		$headerText = JText::_('OSM_SUBSCRIPTION_FORM_HEADING') ;
		break ;
}
$headerText        = str_replace('[PLAN_TITLE]', $this->plan->title, $headerText);

$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');

$fields = $this->form->getFields();
if (isset($fields['state']))
{
	$selectedState = $fields['state']->value;
}
?>
<div id="osm-singup-page" class="osm-container">
<h1 class="osm-page-title"><?php echo $headerText; ?></h1>
<?php
if (strlen($this->message))
{
?>
	<div class="osm-message clearfix"><?php echo $this->message; ?></div>
<?php
}
if (!$this->userId && $this->config->show_login_box_on_subscribe_page)
{
	$actionUrl = JRoute::_('index.php?option=com_users&task=user.login');
	$returnUrl = JUri::getInstance()->toString();
?>
<form method="post" action="<?php echo $actionUrl ; ?>" name="osm_login_form" id="osm_login_form" autocomplete="off" class="form form-horizontal">
	<h2 class="osm-heading"><?php echo JText::_('OSM_EXISTING_USER_LOGIN'); ?></h2>
	<div class="<?php echo $controlGroupClass ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="username">
			<?php echo  empty($this->config->use_email_as_username) ? JText::_('OSM_USERNAME') : $fields['email']->title; ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="username" id="username" required class="input-large validate[required]" value=""/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password">
			<?php echo  JText::_('OSM_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="password" id="password" name="password" required class="input-large validate[required" value="" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass ?>">
		<div class="<?php echo $controlsClass; ?>">
			<input type="submit" value="<?php echo JText::_('OSM_LOGIN'); ?>" class="button btn btn-primary" />
		</div>
	</div>
	<?php
		if ($this->config->registration_integration)
		{
		?>
			<h2 class="eb-heading"><?php echo JText::_('OSM_NEW_USER_REGISTER'); ?></h2>
			<h3 class="osm-heading"><?php echo JText::_('OSM_ACCOUNT_INFORMATION');?></h3>
		<?php
		}
	?>
	<input type="hidden" name="remember" value="1" />
	<input type="hidden" name="return" value="<?php echo base64_encode($returnUrl) ; ?>" />
	<input type="hidden" name="registration_integration" value="<?php echo $this->config->registration_integration; ?>">
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<?php
}
?>
<form method="post" name="os_form" id="os_form" action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_subscription&Itemid='.$this->Itemid, false, $this->config->use_https ? 1 : 0); ?>" enctype="multipart/form-data" autocomplete="off" class="form form-horizontal">
	<?php
	if (!$this->userId && $this->config->registration_integration)
	{
		$params = JComponentHelper::getParams('com_users');
		$minimumLength = $params->get('minimum_length', 4);
		($minimumLength) ? $minSize = ",minSize[$minimumLength]" : $minSize = "";
		if(version_compare(JVERSION, '3.1.2', 'ge'))
		{
			$passwordValidation = ',ajax[ajaxValidatePassword]';
		}
		else
		{
			$passwordValidation = '';
		}

		if (empty($this->config->use_email_as_username))
		{
		?>
			<div class="<?php echo $controlGroupClass ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="username1">
					<?php echo  JText::_('OSM_USERNAME') ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="username" id="username1" class="validate[required,minSize[2],ajax[ajaxUserCall]]" value="<?php echo JRequest::getVar('username', null,'post'); ?>" size="15" autocomplete="off"/>
				</div>
			</div>
		<?php
		}
		else
		{
			echo $fields['email']->getControlGroup($bootstrapHelper);
		}
	?>
	<div class="<?php echo $controlGroupClass ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password1">
			<?php echo  JText::_('OSM_PASSWORD') ?>
			<span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input value="" class="validate[required<?php echo $minSize.$passwordValidation;?>] text-input osm_inputbox inputbox" type="password" name="password1" id="password1" autocomplete="off"/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password2">
			<?php echo  JText::_('OSM_RETYPE_PASSWORD') ?>
			<span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input value="" class="validate[required,equals[password1]] text-input osm_inputbox inputbox" type="password" name="password2" id="password2" />
		</div>
	</div>
	<?php
	}

	if (!empty($this->config->use_email_as_username) && !$this->userId)
	{
		unset($fields['email']);
	}

	foreach ($fields as $field)
	{
		echo $field->getControlGroup($bootstrapHelper);
	}

	if ($this->plan->trial_amount > 0 && $this->plan->trial_duration > 0)
	{
		$onlyDiscountForTrial = true;
	}
	else
	{
		$onlyDiscountForTrial = false;
	}
	if ($this->fees['amount'] > 0 || $this->form->containFeeFields() || $this->plan->recurring_subscription)
	{
	?>
		<h3 class="osm-heading"><?php echo JText::_('OSM_PAYMENT_INFORMATION');?></h3>
		<?php
			if ($this->plan->recurring_subscription)
			{
				if ($this->plan->trial_duration > 0)
				{
				?>
					<div class="<?php echo $controlGroupClass ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_TRIAL_DURATION_PRICE'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="trial_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="trial_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
							<div class="explain-text inline-display"><?php echo $this->trialPeriorText; ?></div>
						</div>
					</div>
				<?php
					if ($this->config->enable_coupon)
					{
						if ($this->fees['show_trial_discount_amount'])
						{
							$style = '' ;
						}
						else
						{
							$style = ' style = "display:none;" ' ;
						}
					?>
					<div class="<?php echo $controlGroupClass ?>" id="trial_discount_amount_container"<?php echo $style; ?>>
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_TRIAL_DURATION_DISCOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="trial_discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_discount_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="trial_discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_discount_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
						</div>
					</div>
					<?php
					}
					if ($this->taxRate > 0)
					{
						if ($this->fees['show_trial_tax_amount'])
						{
							$style = '';
						}
						else
						{
							$style = ' style = "display:none;" ' ;
						}
						$trialTaxAmount = $this->fees['trial_tax_amount'];
					?>
					<div class="<?php echo $controlGroupClass ?>" id="trial_tax_amount_container"<?php echo $style; ?>>
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_TRIAL_TAX_AMOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="trial_tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($trialTaxAmount, $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="trial_tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($trialTaxAmount, $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
						</div>
					</div>
					<?php
					}
					else
					{
						$trialTaxAmount = 0;
					}
					if ($this->showPaymentFee)
					{
						if ($this->fees['show_trial_payment_processing_fee'])
						{
							$style = '';
						}
						else
						{
							$style = ' style = "display:none;" ' ;
						}
					?>
					<div class="<?php echo $controlGroupClass ?>" id="trial_payment_processing_fee_container"<?php echo $style; ?>>
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_TRIAL_PAYMENT_FEE'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="trial_payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_payment_processing_fee'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="trial_payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_payment_processing_fee'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
							?>
						</div>
					</div>
					<?php
					}
					if ($this->config->enable_coupon || $trialTaxAmount > 0 || $this->showPaymentFee)
					{
						if ($this->fees['show_trial_gross_amount'])
						{
							$style = '' ;
						}
						else
						{
							$style = ' style = "display:none;" ' ;
						}
					?>
					<div class="<?php echo $controlGroupClass ?>" id="trial_gross_amount_container"<?php echo $style; ?>>
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_GROSS_TRIAL_AMOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="trial_gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_gross_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="trial_gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['trial_gross_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
							<div class="explain-text inline-display"><?php echo $this->trialPeriorText; ?></div>
						</div>
					</div>
					<?php
					}
				}
				?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_REGULAR_PRICE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="regular_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_amount'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="regular_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_amount'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
						?>
						<div class="explain-text inline-display"><?php echo $this->regularPeriorText; ?></div>
					</div>
				</div>
			<?php
				if ($this->config->enable_coupon && !$onlyDiscountForTrial)
				{
				?>
					<div class="<?php echo $controlGroupClass ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_REGULAR_DISCOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="regular_discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_discount_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="regular_discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_discount_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
						</div>
					</div>
				<?php
				}
				if ($this->taxRate > 0)
				{
				?>
					<div class="<?php echo $controlGroupClass ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_REGULAR_TAX'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="regular_tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_tax_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="regular_tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_tax_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
						</div>
					</div>
				<?php
				}
				if ($this->showPaymentFee)
				{
				?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_PAYMENT_FEE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						if ($this->config->currency_position == 0)
						{
						?>
							<div class="<?php echo $inputPrependClass; ?> inline-display">
								<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								<input id="regular_payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_payment_processing_fee'], $this->config); ?>" />
							</div>
						<?php
						}
						else
						{
						?>
							<div class="<?php echo $inputAppendClass; ?> inline-display">
								<input id="regular_payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_payment_processing_fee'], $this->config); ?>" />
								<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
							</div>
						<?php
						}
						?>
					</div>
				</div>
				<?php
				}
				if (($this->config->enable_coupon && !$onlyDiscountForTrial) || $this->taxRate > 0 || $this->showPaymentFee)
				{
				?>
					<div class="<?php echo $controlGroupClass ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_REGULAR_GROSS_AMOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
								if ($this->config->currency_position == 0)
								{
								?>
									<div class="<?php echo $inputPrependClass; ?> inline-display">
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
										<input id="regular_gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_gross_amount'], $this->config); ?>" />
									</div>
								<?php
								}
								else
								{
								?>
									<div class="<?php echo $inputAppendClass; ?> inline-display">
										<input id="regular_gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['regular_gross_amount'], $this->config); ?>" />
										<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									</div>
								<?php
								}
							?>
							<div class="explain-text inline-display"><?php echo $this->regularPeriorText; ?></div>
						</div>
					</div>
				<?php
				}
			}
			else
			{
			?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_PRICE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['amount'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['amount'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
						?>
					</div>
				</div>
			<?php
				if ($this->config->enable_coupon)
				{
				?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_DISCOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['discount_amount'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['discount_amount'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
						?>
					</div>
				</div>
				<?php
				}
				if ($this->taxRate > 0)
				{
				?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_TAX'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['tax_amount'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['tax_amount'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
						?>
					</div>
				</div>
				<?php
				}
				if ($this->showPaymentFee)
				{
				?>
					<div class="<?php echo $controlGroupClass ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_PAYMENT_FEE'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['payment_processing_fee'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['payment_processing_fee'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				<?php
				}
				if ($this->config->enable_coupon || $this->taxRate > 0 || $this->showPaymentFee)
				{
				?>
				<div class="<?php echo $controlGroupClass ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_GROSS_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php
							if ($this->config->currency_position == 0)
							{
							?>
								<div class="<?php echo $inputPrependClass; ?> inline-display">
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
									<input id="gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['gross_amount'], $this->config); ?>" />
								</div>
							<?php
							}
							else
							{
							?>
								<div class="<?php echo $inputAppendClass; ?> inline-display">
									<input id="gross_amount" type="text" readonly="readonly" class="input-small" value="<?php echo OSMembershipHelper::formatAmount($this->fees['gross_amount'], $this->config); ?>" />
									<span class="<?php echo $addOnClass; ?>"><?php echo $this->currencySymbol?></span>
								</div>
							<?php
							}
						?>
					</div>
				</div>
				<?php
				}
			}
			if ($this->config->enable_coupon)
			{
			?>
			<div class="<?php echo $controlGroupClass ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('OSM_COUPON'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="input-medium" name="coupon_code" id="coupon_code" value="<?php echo JRequest::getVar('coupon_code');?>" onchange="calculateSubscriptionFee();" />
					<span class="invalid" id="coupon_validate_msg" style="display: none;"><?php echo JText::_('OSM_INVALID_COUPON'); ?></span>
				</div>
			</div>
			<?php
			}
			if (count($this->methods) > 1)
			{
			?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="payment_method_container">
				<label class="<?php echo $controlLabelClass; ?>" for="payment_method">
					<?php echo JText::_('OSM_PAYMENT_OPTION'); ?>
					<span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<ul id="osm-payment-method-list" class="nav clearfix">
					<?php
						$method = null ;
						for ($i = 0 , $n = count($this->methods); $i < $n; $i++)
						{
							$paymentMethod = $this->methods[$i];
							if ($paymentMethod->getName() == $this->paymentMethod)
							{
								$checked = ' checked="checked" ';
								$method = $paymentMethod ;
							}
							else
							{
								$checked = '';
							}
						?>
						<li class="osm-payment-method-item radio">
							<input onclick="changePaymentMethod();" id="osm-payment-method-item-<?php echo $i; ?>" type="radio" name="payment_method" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> />
							<label for="osm-payment-method-item-<?php echo $i; ?>"><?php echo JText::_($paymentMethod->title) ; ?></label>
						</li>
						<?php
						}
					?>
					</ul>
				</div>
			</div>
		<?php
		}
		else
		{
			$method = $this->methods[0] ;
		?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="payment_method_container">
				<label class="<?php echo $controlLabelClass; ?>" for="payment_method">
					<?php echo JText::_('OSM_PAYMENT_OPTION'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php echo JText::_($method->title); ?>
				</div>
			</div>
		<?php
		}
		if ($method->getCreditCard())
		{
			$style = '' ;
		}
		else
		{
			$style = 'style = "display:none"';
		}
		?>
		<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_number" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>"><?php echo  JText::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="x_card_num" class="validate[required,creditCard] osm_inputbox inputbox" value="<?php echo JRequest::getVar('x_card_num', null,'post'); ?>" size="20" />
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_exp_date" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['exp_month'] .'  /  '.$this->lists['exp_year'] ; ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_cvv_code" <?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('AUTH_CVV_CODE'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="x_card_code" class="validate[required,custom[number]] osm_inputbox input-small" value="<?php echo JRequest::getVar('x_card_code', null,'post'); ?>" size="20" />
			</div>
		</div>
		<?php
			if ($method->getCardHolderName())
			{
				$style = '' ;
			}
			else
			{
				$style = ' style = "display:none;" ' ;
			}
		?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_holder_name" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('OSM_CARD_HOLDER_NAME'); ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="card_holder_name" class="validate[required] osm_inputbox inputbox"  value="<?php echo JRequest::getVar('card_holder_name', null,'post'); ?>" size="40" />
				</div>
			</div>
		<?php
	}
	$articleId =  $this->plan->terms_and_conditions_article_id > 0 ? $this->plan->terms_and_conditions_article_id : $this->config->article_id;
	if ($articleId > 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__content')
			->where('id = '. (int) $articleId);
		$db->setQuery($query);
		$rowArticle = $db->loadObject() ;
		$catId = $rowArticle->catid ;
		require_once JPATH_ROOT.'/components/com_content/helpers/route.php' ;
		if ($this->config->fix_terms_and_conditions_popup)
		{
			$termLink = ContentHelperRoute::getArticleRoute($articleId, $catId).'&format=html' ;
			$extra = ' target="_blank" ';
		}
		else
		{
			$termLink = ContentHelperRoute::getArticleRoute($articleId, $catId).'&tmpl=component&format=html' ;
			$extra = ' class="osm-modal" ' ;
		}
	?>
	<div class="<?php echo $controlGroupClass ?>">
		<input type="checkbox" name="accept_term" value="1" class="validate[required] osm_inputbox inputbox" />
		<strong><?php echo JText::_('OSM_ACCEPT'); ?>&nbsp;<a href="<?php echo JRoute::_($termLink); ?>" <?php echo $extra ; ?> rel="{handler: 'iframe', size: {x: 700, y: 500}}"><?php echo JText::_('OSM_TERM_AND_CONDITION'); ?></a></strong>
	</div>
	<?php
	}
	if ($this->config->enable_captcha) {
	?>
		<div class="<?php echo $controlGroupClass ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('OSM_CAPTCHA'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->captcha;?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="form-actions">
		<input type="submit" class="<?php echo $bootstrapHelper->getClassMapping('btn'); ?> btn-primary" name="btnSubmit" id="btn-submit" value="<?php echo  JText::_('OSM_PROCESS_SUBSCRIPTION') ;?>">
		<img id="ajax-loading-animation" src="<?php echo JUri::base();?>media/com_osmembership/ajax-loadding-animation.gif" style="display: none;"/>
	</div>
<?php
	if (count($this->methods) == 1)
	{
	?>
		<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
	<?php
	}
?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="plan_id" value="<?php echo $this->plan->id ; ?>" />
	<input type="hidden" name="option" value="com_osmembership" />
	<input type="hidden" name="act" value="<?php echo $this->action ; ?>" />
	<input type="hidden" name="renew_option_id" value="<?php echo $this->renewOptionId ; ?>" />
	<input type="hidden" name="upgrade_option_id" value="<?php echo $this->upgradeOptionId ; ?>" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int)$this->showPaymentFee ; ?>" />
	<input type="hidden" name="vat_number_field" value="<?php echo $this->config->eu_vat_number_field ; ?>" />
	<input type="hidden" name="country_base_tax" value="<?php echo $this->countryBaseTax; ?>" />	
	<input type="hidden" name="default_country" id="default_country" value="<?php echo $this->config->default_country; ?>" />
	<script type="text/javascript">
		var taxStateCountries = "<?php echo $this->taxStateCountries;?>";
		taxStateCountries = taxStateCountries.split(',');
		OSM.jQuery(function($){
			$(document).ready(function(){
				if($('input[name^=registration_integration]').length)
				{
					OSMVALIDATEFORM("#osm_login_form");
				}

				$("#os_form").validationEngine('attach', {
					onValidationComplete: function(form, status){
						if (status == true) {
							form.on('submit', function(e) {
								e.preventDefault();
							});

							form.find('#btn-submit').prop('disabled', true);
							<?php
								if ($this->plan->price > 0)
								{
								?>
									if (typeof stripePublicKey !== 'undefined')
									{
										if($('input:radio[name^=payment_method]').length)
										{
											var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
										}
										else
										{
											var paymentMethod = $('input[name^=payment_method]').val();
										}

										if (paymentMethod == 'os_stripe')
										{
											Stripe.card.createToken({
												number: $('input[name^=x_card_num]').val(),
												cvc: $('input[name^=x_card_code]').val(),
												exp_month: $('select[name^=exp_month]').val(),
												exp_year: $('select[name^=exp_year]').val(),
												name: $('input[name^=card_holder_name]').val()
											}, stripeResponseHandler);

											return false;
										}
									}
								<?php
								}
							?>
							return true;
						}
						return false;
					}
				});

				<?php
					if ($this->fees['amount'] == 0 && !$this->plan->recurring_subscription)
					{
					?>
						$('.payment_information').css('display', 'none');
					<?php
					}
					if ($this->config->eu_vat_number_field)
					{
					?>
						// Add css class for vat number field
						$('input[name^=<?php echo $this->config->eu_vat_number_field   ?>]').addClass('taxable');
						$('input[name^=<?php echo $this->config->eu_vat_number_field   ?>]').before('<div class="<?php echo $inputPrependClass; ?>"><span class="<?php echo $addOnClass; ?>" id="vat_country_code"><?php echo $this->countryCode; ?></span>');
						$('input[name^=<?php echo $this->config->eu_vat_number_field   ?>]').after('<span class="invalid" id="vatnumber_validate_msg" style="display: none;"><?php echo ' '.JText::_('OSM_INVALID_VATNUMBER'); ?></span></div>');
						$('input[name^=<?php echo $this->config->eu_vat_number_field   ?>]').change(function(){
							calculateSubscriptionFee();
						});
						<?php
						}
					?>
					buildStateField('state', 'country', '<?php echo $selectedState; ?>');
			})
		});
		<?php
			os_payments::writeJavascriptObjects();
		?>
	</script>
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>