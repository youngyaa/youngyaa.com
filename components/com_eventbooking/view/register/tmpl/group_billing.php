<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die;
if ($this->config->use_https)
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_group_registration&Itemid='.$this->Itemid, false, 1);
}
else
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_group_registration&Itemid='.$this->Itemid, false);
}
$selectedState = '';

$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
if (!$this->userId && $this->config->user_registration)
{
	$validateLoginForm = true;
?>
<h3 class="eb-heading"><?php echo JText::_('EB_EXISTING_USER_LOGIN'); ?></h3>
<form method="post" action="index.php" name="eb-form-login" id="eb-form-login" autocomplete="off" class="form form-horizontal">
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="username">
			<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="username" id="username" class="input-large validate[required]" value=""/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password">
			<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="password" id="password" name="password" class="input-large validate[required]" value="" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlsClass; ?>">
			<input type="submit" value="<?php echo JText::_('EB_LOGIN'); ?>" class="button btn btn-primary" name="btn-login" id="btn-login" />
		</div>
	</div>
	<?php
		if (JPluginHelper::isEnabled('system', 'remember'))
		{
		?>
			<input type="hidden" name="remember" value="1" />
		<?php
		}
	?>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" id="return_url" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<h3 class="eb-heading"><?php echo JText::_('EB_NEW_USER_REGISTER'); ?></h3>
<?php
}
else
{
	$validateLoginForm = false;
}
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>" autocomplete="off" class="form form-horizontal" enctype="multipart/form-data">
<?php
	if (!$this->userId && $this->config->user_registration)
	{
		$params = JComponentHelper::getParams('com_users');
		$minimumLength = $params->get('minimum_length', 4);
		($minimumLength) ? $minSize = "minSize[4]" : $minSize = "";
	?>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="username1">
			<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="username" id="username1" class="input-large validate[required,ajax[ajaxUserCall],<?php echo $minSize;?>]" value="<?php echo JRequest::getVar('username'); ?>"/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password1">
			<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="password" name="password1" id="password1" class="input-large input-large validate[required,<?php echo $minSize;?>]" value=""/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password2">
			<?php echo  JText::_('EB_RETYPE_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="password" name="password2" id="password2" class="input-large validate[required,equals[password1]]" value=""/>
		</div>
	</div>
	<?php
	}
	$fields = $this->form->getFields();
	if (isset($fields['state']))
	{
		$selectedState = $fields['state']->value;
	}
	$dateFields = array();
	foreach ($fields as $field)
	{
		echo $field->getControlGroup($bootstrapHelper);
		if ($field->type == "Date")
		{
			$dateFields[] = $field->name;
		}
	}
	if (($this->totalAmount > 0) || $this->form->containFeeFields())
	{
	?>
	<h3 class="eb-heading"><?php echo JText::_('EB_PAYMENT_INFORMATION'); ?></h3>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_AMOUNT'); ?>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php
				if ($this->config->currency_position == 0)
				{
				?>
					<div class="<?php echo $inputPrependClass; ?> inline-display">
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
						<input id="total_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->totalAmount, $this->config); ?>" />
					</div>
				<?php
				}
				else
				{
				?>
					<div class="<?php echo $inputAppendClass; ?> inline-display">
						<input id="total_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->totalAmount, $this->config); ?>" />
						<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
					</div>
				<?php
				}
			?>
		</div>
	</div>
	<?php
		if ($this->enableCoupon || $this->discountAmount > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
							<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->discountAmount, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">
							<input id="discount_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->discountAmount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
				?>
			</div>
		</div>
		<?php
		}
		if($this->lateFee > 0)
		{
		?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('EB_LATE_FEE'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if ($this->config->currency_position == 0)
					{
						?>
						<div class="<?php echo $inputPrependClass;  ?> inline-display">
							<span class="<?php echo $addOnClass;?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
							<input id="late_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->lateFee, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
						?>
						<div class="<?php echo $inputAppendClass;  ?> inline-display">
							<input id="late_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->lateFee, $this->config); ?>" />
							<span class="<?php echo $addOnClass;?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		<?php
		}
		if($this->event->tax_rate > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_TAX_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
							<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->taxAmount, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">
							<input id="tax_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->taxAmount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
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
			<div class="<?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('EB_PAYMENT_FEE'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?>">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
							<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->paymentProcessingFee, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?>">
							<input id="payment_processing_fee" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->paymentProcessingFee, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		<?php
		}
		if ($this->enableCoupon || $this->discountAmount > 0 || $this->event->tax_rate > 0 || $this->showPaymentFee)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_GROSS_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
							<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->amount, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">
							<input id="amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->amount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
				?>
			</div>
		</div>
		<?php
		}
		if ($this->depositPayment)
		{
			if ($this->paymentType == 1)
			{
				$style = '';
			}
			else
			{
				$style = 'style = "display:none"';
			}
			?>
			<div id="deposit_amount_container" class="<?php echo $controlGroupClass; ?>"<?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="payment_type">
					<?php echo JText::_('EB_DEPOSIT_AMOUNT') ;?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if ($this->config->currency_position == 0)
					{
					?>
						<div class="<?php echo $inputPrependClass; ?> inline-display">
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
							<input id="deposit_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->depositAmount, $this->config); ?>" />
						</div>
					<?php
					}
					else
					{
					?>
						<div class="<?php echo $inputAppendClass; ?> inline-display">
							<input id="deposit_amount" type="text" readonly="readonly" class="input-small" value="<?php echo EventbookingHelper::formatAmount($this->depositAmount, $this->config); ?>" />
							<span class="<?php echo $addOnClass; ?>"><?php echo $this->event->currency_symbol ? $this->event->currency_symbol : $this->config->currency_symbol;?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="payment_type">
					<?php echo JText::_('EB_PAYMENT_TYPE') ;?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php echo $this->lists['payment_type'] ;?>
				</div>
			</div>
			<?php
		}
		if ($this->enableCoupon)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>" for="coupon_code"><?php echo  JText::_('EB_COUPON') ?></label>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" class="input-medium" name="coupon_code" id="coupon_code" value="<?php echo JRequest::getVar('coupon_code'); ?>" onchange="calculateGroupRegistrationFee();" />
				<span class="invalid" id="coupon_validate_msg" style="display: none;"><?php echo JText::_('EB_INVALID_COUPON'); ?></span>
			</div>
		</div>
		<?php
		}
		if (!$this->waitingList)
		{
			if (count($this->methods) > 1)
			{
				?>
				<div class="<?php echo $controlGroupClass; ?> payment_information" id="payment_method_container">
					<label class="<?php echo $controlLabelClass; ?>" for="payment_method">
						<?php echo JText::_('EB_PAYMENT_OPTION'); ?>
						<span class="required">*</span>
					</label>

					<div class="<?php echo $controlsClass; ?>">
						<?php
						$method = null;
						for ($i = 0, $n = count($this->methods); $i < $n; $i++)
						{
							$paymentMethod = $this->methods[$i];
							if ($paymentMethod->getName() == $this->paymentMethod)
							{
								$checked = ' checked="checked" ';
								$method  = $paymentMethod;
							}
							else
							{
								$checked = '';
							}
							?>
							<label class="checkbox">
								<input onclick="changePaymentMethod('group');" class="validate[required] radio"
									   type="radio" name="payment_method"
									   value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> /><?php echo JText::_($paymentMethod->getTitle()); ?>
							</label>
						<?php
						}
						?>
					</div>
				</div>
			<?php
			}
			else
			{
				$method = $this->methods[0];
				?>
				<div class="<?php echo $controlGroupClass; ?> payment_information" id="payment_method_container">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('EB_PAYMENT_OPTION'); ?>
					</label>

					<div class="<?php echo $controlsClass; ?>">
						<?php echo JText::_($method->getTitle()); ?>
					</div>
				</div>
			<?php
			}
			if ($method->getCreditCard())
			{
				$style = '';
			}
			else
			{
				$style = 'style = "display:none"';
			}
			?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_number" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="x_card_num">
					<?php echo JText::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="x_card_num" name="x_card_num"
						   class="input-large validate[required,creditCard]"
						   value="<?php echo JRequest::getVar('x_card_num'); ?>"/>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_exp_date" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<?php echo $this->lists['exp_month'] . '  /  ' . $this->lists['exp_year']; ?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_cvv_code" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="x_card_code">
					<?php echo JText::_('AUTH_CVV_CODE'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="x_card_code" name="x_card_code"
						   class="input-large validate[required,custom[number]]"
						   value="<?php echo JRequest::getVar('x_card_code'); ?>"/>
				</div>
			</div>
			<?php
			if ($method->getCardType())
			{
				$style = '';
			}
			else
			{
				$style = ' style = "display:none;" ';
			}
			?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_type" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="card_type">
					<?php echo JText::_('EB_CARD_TYPE'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<?php echo $this->lists['card_type']; ?>
				</div>
			</div>
			<?php
			if ($method->getCardHolderName())
			{
				$style = '';
			}
			else
			{
				$style = ' style = "display:none;" ';
			}
			?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_holder_name" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="card_holder_name">
					<?php echo JText::_('EB_CARD_HOLDER_NAME'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="card_holder_name" name="card_holder_name"
						   class="input-large validate[required]"
						   value="<?php echo JRequest::getVar('card_holder_name'); ?>"/>
				</div>
			</div>
			<?php
			if ($method->getName() == 'os_ideal')
			{
				$style = '';
			}
			else
			{
				$style = ' style = "display:none;" ';
			}
			?>
			<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_bank_list" <?php echo $style; ?>>
				<label class="<?php echo $controlLabelClass; ?>" for="bank_id">
					<?php echo JText::_('EB_BANK_LIST'); ?><span class="required">*</span>
				</label>

				<div class="<?php echo $controlsClass; ?>">
					<?php echo isset($this->lists['bank_id']) ? $this->lists['bank_id'] : ''; ?>
				</div>
			</div>
		<?php
		}
	}
	if ($this->showCaptcha)
	{
	?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_CAPTCHA'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->captcha; ?>
			</div>
		</div>
	<?php
	}
	$articleId  = $this->event->article_id ? $this->event->article_id : $this->config->article_id ;
	if ($this->config->accept_term ==1 && $articleId)
	{
		if (version_compare(JVERSION, '3.1', 'ge') && JLanguageMultilang::isEnabled())
		{
			$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			$langCode     = JFactory::getLanguage()->getTag();
			if (isset($associations[$langCode]))
			{
				$article = $associations[$langCode];
			}
		}

		if (!isset($article))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, catid')
				->from('#__content')
				->where('id = ' . (int) $articleId);
			$db->setQuery($query);
			$article = $db->loadObject();
		}

		require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
		EventbookingHelperJquery::colorbox('eb-colorbox-term');
		$termLink = ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html';
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="checkbox">
				<input type="checkbox" name="accept_term" value="1" class="validate[required]" data-errormessage="<?php echo JText::_('EB_ACCEPT_TERMS');?>" />
				<?php echo JText::_('EB_ACCEPT'); ?>&nbsp;
				<?php
					echo "<a class=\"eb-colorbox-term\" href=\"".JRoute::_($termLink)."\">"."<strong>".JText::_('EB_TERM_AND_CONDITION')."</strong>"."</a>\n";
				?>
			</label>
		</div>
	<?php
	}
	if ($this->waitingList)
	{
		$buttonText = JText::_('EB_PROCESS');
	}
	else
	{
		$buttonText = JText::_('EB_PROCESS_REGISTRATION');
	}
	?>
	<div class="form-actions">
		<input type="button" class="btn btn-primary" name="btn-group-billing-back" id="btn-group-billing-back" value="<?php echo  JText::_('EB_BACK') ;?>">
		<input type="submit" class="btn btn-primary" name="btn-process-group-billing" id="btn-process-group-billing" value="<?php echo $buttonText;?>">
		<img id="ajax-loading-animation" src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" style="display: none;"/>
	</div>
	<?php
		if (count($this->methods) == 1)
		{
		?>
			<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
		<?php
		}
	?>
	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int)$this->showPaymentFee ; ?>" />
	<script type="text/javascript">
		var eb_current_page = 'group_billing';
		<?php echo os_payments::writeJavascriptObjects();?>
		var siteUrl = "<?php echo EventbookingHelper::getSiteUrl(); ?>";
			Eb.jQuery(document).ready(function($){
				<?php
					if (count($dateFields))
					{
						echo EventbookingHelperHtml::getCalendarSetupJs($dateFields);
					}
					if ($this->amount == 0)
					{
					?>
						$('.payment_information').css('display', 'none');
					<?php
					}
				?>
				$("#adminForm").validationEngine('attach', {
					onValidationComplete: function(form, status){
						if (status == true) {
							form.on('submit', function(e) {
								e.preventDefault();
							});
							return true;
						}
						return false;
					}
				});
				<?php
					if ($validateLoginForm)
					{
					?>
						$("#eb-login-form").validationEngine();
					<?php
					}

				?>
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
				<?php
					if ($this->showCaptcha && $this->captchaPlugin == 'recaptcha')
					{
						$captchaPlugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
						$params = $captchaPlugin->params;
						$version    = $params->get('version', '1.0');
						$pubkey = $params->get('public_key', '');
						if ($version == '1.0')
						{
							$theme  = $params->get('theme', 'clean');
						?>
							Recaptcha.create("<?php echo $pubkey; ?>", "dynamic_recaptcha_1", {theme: "<?php echo $theme; ?>"});
						<?php
						}
						else
						{
							$theme = $params->get('theme2', 'light');
							$langTag = JFactory::getLanguage()->getTag();
							if (JFactory::getApplication()->isSSLConnection())
							{
								$file = 'https://www.google.com/recaptcha/api.js?hl=' . $langTag . '&onload=onloadCallback&render=explicit';
							}
							else
							{
								$file = 'http://www.google.com/recaptcha/api.js?hl=' . $langTag . '&onload=onloadCallback&render=explicit';
							}
							JHtml::_('script', $file, true, true);
							?>
								grecaptcha.render("dynamic_recaptcha_1", {sitekey: "' . <?php echo $pubkey;?> . '", theme: "' . <?php echo $theme; ?> . '"});
							<?php
						}
					}
				?>
				$('#btn-group-billing-back').click(function(){
					$.ajax({
						url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
						type: 'post',
						dataType: 'html',
						beforeSend: function() {
							$('#btn-group-billing-back').attr('disabled', true);
						},
						complete: function() {
							$('#btn-group-billing-back').attr('disabled', false);
						},
						success: function(html) {
							$('#eb-group-members-information .eb-form-content').html(html);
							$('#eb-group-billing .eb-form-content').slideUp('slow');
							<?php ($this->config->collect_member_information) ? $idAjax = 'eb-group-members-information' : $idAjax = 'eb-number-group-members';?>
							$('#<?php echo $idAjax; ?> .eb-form-content').slideDown('slow');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				});
				//term colorbox term
				 $(".eb-colorbox-term").colorbox({
					 href: $(this).attr('href'),
					 innerHeight: '80%',
					 innerWidth: '80%',
					 overlayClose: true,
					 iframe: true,
					 opacity: 0.3
				});
			})
	</script>
</form>