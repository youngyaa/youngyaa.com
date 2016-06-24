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
$db = JFactory::getDbo();
$query = $db->getQuery(true);
OSMembershipHelperJquery::validateForm();
$selectedState = '';
if ($this->config->use_https)
{
	$ssl = 1;
}
else
{
	$ssl = 0;
}
$bootstrapHelper = $this->bootstrapHelper;

// Get mapping classes, make them ready for using
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-group');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$fieldSuffix = OSMembershipHelper::getFieldSuffix();
?>
<script type="text/javascript">
	var siteUrl = '<?php echo OSMembershipHelper::getSiteUrl();  ?>';
</script>
<div id="osm-profile-page" class="row-fluid osm-container">
<h1 class="osm_title"><?php echo JText::_('OSM_USER_PROFILE'); ?></h1>
<form action="index.php" method="post" name="osm_form" id="osm_form" autocomplete="off" enctype="multipart/form-data" class="form form-horizontal">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#profile-page" data-toggle="tab"><?php echo JText::_('OSM_EDIT_PROFILE');?></a></li>
		<li><a href="#my-subscriptions-page" data-toggle="tab"><?php echo JText::_('OSM_MY_SUBSCRIPTIONS');?></a></li>
		<li><a href="#subscription-history-page" data-toggle="tab"><?php echo JText::_('OSM_SUBSCRIPTION_HISTORY');?></a></li>
		<?php
			if (count($this->plugins))
			{
				$count = 0 ;
				foreach ($this->plugins as $plugin)
				{
					$title  = $plugin['title'] ;
					$count++ ;
				?>
					<li><a href="#<?php echo 'tab_'.$count;  ?>" data-toggle="tab"><?php echo $title;?></a></li>
				<?php
				}
			}
		?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="profile-page">
			<h2 class="osm-form-heading"><?php echo JText::_('OSM_PROFILE_DATA'); ?></h2>
			<?php
				if ($this->item->user_id)
				{
					$params = JComponentHelper::getParams('com_users');
					$validationRules = array();
					$minimumLength = $params->get('minimum_length', 4);
					if ($minimumLength)
					{
						$validationRules[] = "minSize[$minimumLength]";
					}
					if(version_compare(JVERSION, '3.1.2', 'ge'))
					{
						$validationRules[] = 'ajax[ajaxValidatePassword]';
					}
					if (count($validationRules))
					{
						$class = ' class="validate['.implode(',', $validationRules).']"';
					}
					else
					{
						$class = '';
					}
				?>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>">
							<?php echo JText::_('OSM_USERNAME'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<?php echo $this->item->username; ?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" for="password">
							<?php echo JText::_('OSM_PASSWORD'); ?>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="password" id="password" name="password" size="20" value=""<?php echo $class; ?> />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" for="password2">
							<?php echo  JText::_('OSM_RETYPE_PASSWORD') ?>
							<span class="required">*</span>
						</label>
						<div class="<?php echo $controlsClass; ?>">
							<input value="" class="validate[equals[password]]" type="password" name="password2" id="password2" />
						</div>
					</div>
				<?php
				}
				if ($this->item->membership_id)
				{
				?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('OSM_MEMBERSHIP_ID'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo OSMembershipHelper::formatMembershipId($this->item, $this->config); ?>
					</div>
				</div>
				<?php
				}
				$fields = $this->form->getFields();
				if (isset($fields['state']))
				{
					$selectedState = $fields['state']->value;
				}
				foreach ($fields as $field)
				{
					if ($field->fee_field)
					{
						echo $field->getOutput(true, $bootstrapHelper);
					}
					else
					{
						echo $field->getControlGroup($bootstrapHelper);
					}
				}
			?>
			<div class="form-actions">
				<input type="submit" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('OSM_UPDATE'); ?>"/>
			</div>
		</div>
		<div class="tab-pane" id="my-subscriptions-page">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>
							<?php echo JText::_('OSM_PLAN') ?>
						</th>
						<th width="25%" class="center">
							<?php echo JText::_('OSM_ACTIVATE_TIME') ; ?>
						</th>
						<th width="10%" class="center">
							<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$plans = $this->plans;
						foreach($plans as $plan)
						{
						?>
						<tr>
							<td>
								<?php echo $plan->title; ?>
							</td>
							<td class="center">
								<strong><?php echo JHtml::_('date', $plan->subscription_from_date, $this->config->date_format, null); ?></strong> <?php echo JText::_('OSM_TO'); ?>
								<strong>
									<?php
										if ($plan->lifetime_membership || $plan->subscription_to_date == '2099-12-31 23:59:59')
										{
											echo JText::_('OSM_LIFETIME');
										}
										else
										{
											echo JHtml::_('date', $plan->subscription_to_date, $this->config->date_format);
										}
									?>
								</strong>
							</td>
							<td class="center">
								<?php
									switch ($plan->subscription_status)
									{
										 case 0 :
											echo JText::_('OSM_PENDING');
											break ;
										case 1 :
											echo JText::_('OSM_ACTIVE');
											break ;
										case 2 :
											echo JText::_('OSM_EXPIRED');
											break ;
										default:
											echo JText::_('OSM_CANCELLED');
											break;

									}

									if ($plan->subscription_status == 1 && $plan->subscription_id)
									{
									?>
										<input type="button" class="btn btn-danger" value="<?php echo JText::_('OSM_CANCEL_SUBSCRIPTION'); ?>" onclick="cancelSubscription('<?php echo $plan->subscription_id;  ?>');" />
									<?php
									}

									if ($plan->recurring_cancelled)
									{
										echo '<br /><span class="text-error">' . JText::_('OSM_RECURRING_CANCELLED').'</span>';
									}

								?>
							</td>
						</tr>
						<?php
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="subscription-history-page">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>
							<?php echo JText::_('OSM_PLAN') ?>
						</th>
						<th width="20%">
							<?php echo JText::_('OSM_SUBSCRIPTION_DATE') ; ?>
						</th>
						<th width="20%">
							<?php echo JText::_('OSM_ACTIVATE_TIME') ; ?>
						</th>
						<th width="14%" class="right">
							<?php echo JText::_('OSM_GROSS_AMOUNT') ; ?>
						</th>
						<th>
							<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
						</th>
						<?php
							if ($this->config->activate_invoice_feature)
							{
							?>
								<th style="text-align: center;">
									<?php echo JText::_('OSM_INVOICE_NUMBER') ; ?>
								</th>
							<?php
							}
						?>
					</tr>
				</thead>
				<tbody>
				<?php
					for ($i = 0 , $n = count($this->items) ; $i < $n ; $i++)
					{
						$row = $this->items[$i] ;
						$link = JRoute::_('index.php?option=com_osmembership&view=subscription&id='.$row->id.'&Itemid='.$this->Itemid);
						$symbol = $row->currency_symbol ? $row->currency_symbol : $row->currency;
					?>
						<tr>
							<td>
								<a href="<?php echo $link; ?>"><?php echo $row->plan_title; ?></a>
							</td>
							<td class="center">
								<?php echo JHtml::_('date', $row->created_date, $this->config->date_format); ?>
							</td>
							<td align="center">
								<strong><?php echo JHtml::_('date', $row->from_date, $this->config->date_format); ?></strong> <?php echo JText::_('OSM_TO'); ?>
								<strong>
									<?php
									if ($row->lifetime_membership || $row->to_date == '2099-12-31 23:59:59')
									{
										echo JText::_('OSM_LIFETIME');
									}
									else
									{
										echo JHtml::_('date', $row->to_date, $this->config->date_format);
									}
									?>
								</strong>
							</td>
							<td class="right">
								<?php echo OSmembershipHelper::formatCurrency($row->gross_amount, $this->config, $symbol)?>
							</td>
							<td>
								<?php
									switch ($row->published)
									{
										case 0 :
											echo JText::_('OSM_PENDING');
											break ;
										case 1 :
											echo JText::_('OSM_ACTIVE');
											break ;
										case 2 :
											echo JText::_('OSM_EXPIRED');
											break ;
										case 3 :
											echo JText::_('OSM_CANCELLED_PENDING');
											break ;
										case 4 :
											echo JText::_('OSM_CANCELLED_REFUNDED');
											break ;
									}
								?>
							</td>
							<?php
								if ($this->config->activate_invoice_feature)
								{
								?>
									<td class="center">
										<?php
											if (OSMembershipHelper::needToCreateInvoice($row))
											{
											?>
												<a href="<?php echo JRoute::_('index.php?option=com_osmembership&task=download_invoice&id='.$row->id); ?>" title="<?php echo JText::_('OSM_DOWNLOAD'); ?>"><?php echo OSMembershipHelper::formatInvoiceNumber($row, $this->config) ; ?></a>
											<?php
											}
										?>
									</td>
								<?php
								}
							?>
						</tr>
					<?php
					}
					?>
					</tbody>
					<?php
				?>
			</table>
		</div>
		<?php
			if (count($this->plugins))
			{
				$count = 0 ;
				foreach ($this->plugins as $plugin)
				{
					$form = $plugin['form'] ;
					$count++ ;
				?>
					<div class="tab-pane" id="tab_<?php echo $count; ?>">
						<?php
							echo $form ;
						?>
					</div>
				<?php
				}
			}
		?>

</div>
<div class="clearfix"></div>
	<input type="hidden" name="option" value="com_osmembership" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="profile.update" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<?php
if (count($this->planIds))
{
?>
<form action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_renew_membership&Itemid='.$this->Itemid, false, $ssl); ?>" method="post" name="osm_form_renew" id="osm_form_renew" autocomplete="off" class="form form-horizontal">
	<h2 class="osm-form-heading"><?php echo JText::_('OSM_RENEW_MEMBERSHIP'); ?></h2>
	<ul class="osm-renew-options">
		<?php
			$renewOptionCount = 0;
			foreach ($this->planIds as $planId)
			{
				$query->clear();
				$query->select('*')
					->from('#__osmembership_plans')
					->where('id = '. $planId);

				if ($fieldSuffix)
				{
					OSMembershipHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
				}

				$db->setQuery($query);
				$plan = $db->loadObject();

				$symbol = $plan->currency_symbol ? $plan->currency_symbol : $plan->currency;

				$query->clear();
				$query->select('*')
					->from('#__osmembership_renewrates')
					->where('plan_id = '. $planId)
					->order('number_days');
				$db->setQuery($query);
				$renewOptions = $db->loadObjectList();
				if (count($renewOptions))
				{
					foreach ($renewOptions as $renewOption)
					{
						$renewOptionCount++;
						list($renewOptionFrequency, $renewOptionLength) = OSMembershipHelper::getRecurringSettingOfPlan($renewOption->number_days);
						switch ($renewOptionFrequency)
						{
							case 'D':
								$text = $renewOptionLength > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
								break ;
							case 'W' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
								break ;
							case 'M' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
								break ;
							case 'Y' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
								break ;
						}
					?>
						<li class="osm-renew-option">
							<input type="radio" class="validate[required] inputbox" id="renew_option_id_<?php echo $renewOptionCount; ?>" name="renew_option_id" value="<?php echo $planId.'|'.$renewOption->id; ?>" />
							<label for="renew_option_id_<?php echo $renewOptionCount; ?>"><?php JText::printf('OSM_RENEW_OPTION_TEXT', $plan->title, $renewOptionLength.' '. $text, OSMembershipHelper::formatCurrency($renewOption->price, $this->config, $symbol)); ?></label>
						</li>
					<?php
					}
				}
				else
				{
					$renewOptionCount++;
					$length = $plan->subscription_length;
					switch ($plan->subscription_length_unit) {
						case 'D':
							$text = $length > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
							break ;
						case 'W' :
							$text = $length > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
							break ;
						case 'M' :
							$text = $length > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
							break ;
						case 'Y' :
							$text = $length > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
							break ;
					}
				?>
					<li class="osm-renew-option">
						<input type="radio" class="validate[required] inputbox" id="renew_option_id_<?php echo $renewOptionCount; ?>" name="renew_option_id" value="<?php echo $planId;?>" />
						<label for="renew_option_id_<?php echo $renewOptionCount; ?>"><?php JText::printf('OSM_RENEW_OPTION_TEXT', $plan->title, $length.' '.$text, OSMembershipHelper::formatCurrency($plan->price, $this->config, $symbol)); ?></label>
					</li>
				<?php
				}
			}
		?>
	</ul>
	<div class="form-actions">
		<input type="submit" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('OSM_PROCESS_RENEW'); ?>"/>
	</div>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
</form>
<?php
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_upgrade_membership&Itemid='.$this->Itemid, false, $ssl); ?>" method="post" name="osm_form_update_membership" id="osm_form_update_membership" autocomplete="off" class="form form-horizontal">
	<?php
		$query->clear();
		$query->select('id')
			->from('#__osmembership_plans')
			->where('price = 0')
			->where('published = 1');
		$db->setQuery($query);
		$trialPlanIds = $db->loadColumn();
		if (!count($trialPlanIds))
		{
			$trialPlanIds = array(0);
		}

		//We should only allow upgrading from active membership and free trial memberships
		$query->clear();
		$query->select('DISTINCT plan_id')
			->from('#__osmembership_subscribers')
			->where('profile_id = ' . $this->item->id)
			->where('(published = 1 OR (published < 3 AND plan_id IN ('.implode(',', $trialPlanIds).')))');
		$db->setQuery($query);
		$planIds = $db->loadColumn();

		if (!count($planIds))
		{
			$planIds = array(0);
		}
		$sql = 'SELECT * FROM #__osmembership_upgraderules WHERE from_plan_id IN ('.implode(',', $planIds).') ORDER BY from_plan_id';
		$db->setQuery($sql);
		$upgradeRules = $db->loadObjectList();
		if (count($upgradeRules))
		{
			$query->clear();
			$query->select('*')
				->from('#__osmembership_plans')
				->where('published = 1');

			if ($fieldSuffix)
			{
				OSMembershipHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
			}

			$db->setQuery($query);
			$plans = $db->loadObjectList('id');
		?>
		<h2 class="osm-form-heading"><?php echo JText::_('OSM_UPGRADE_MEMBERSHIP'); ?></h2>
			<ul class="osm-upgrade-options">
				<?php
					$upgradeOptionCount = 0;
					foreach ($upgradeRules as $rule)
					{
						$upgradeOptionCount++;
						$upgradeToPlan = $plans[$rule->to_plan_id];
						$symbol = $upgradeToPlan->currency_symbol ? $upgradeToPlan->currency_symbol : $upgradeToPlan->currency;
					?>
						<li class="osm-upgrade-option">
							<input type="radio" class="validate[required]" id="upgrade_option_id_<?php echo $upgradeOptionCount; ?>" name="upgrade_option_id" value="<?php echo $rule->id; ?>" />
							<label for="upgrade_option_id_<?php echo $upgradeOptionCount; ?>"><?php JText::printf('OSM_UPGRADE_OPTION_TEXT', $plans[$rule->from_plan_id]->title, $upgradeToPlan->title, OSMembershipHelper::formatCurrency($rule->price, $this->config, $symbol)); ?></label>
						</li>
					<?php
					}
				?>
			</ul>
			<div class="form-actions">
				<input type="submit" class="<?php echo $btnClass; ?> btn-primary" value="<?php echo JText::_('OSM_PROCESS_UPGRADE'); ?>"/>
			</div>
		<?php
		}
	?>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
</form>

<form action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_cancel_subscription&Itemid='.$this->Itemid, false, $ssl); ?>" method="post" name="osm_form_cancel_subscription" id="osm_form_cancel_subscription" autocomplete="off" class="form form-horizontal">
	<input type="hidden" name="subscription_id" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>

<script type="text/javascript">
	OSM.jQuery(function($){
		$(document).ready(function(){
			OSMVALIDATEFORM("#osm_form");
			OSMVALIDATEFORM("#osm_form_renew");
			OSMVALIDATEFORM("#osm_form_update_membership");
			buildStateField('state', 'country', '<?php echo $selectedState; ?>');
		})
	});


	function cancelSubscription(subscriptionId)
	{
		if (confirm("<?php echo JText::_('OSM_CANCEL_SUBSCRIPTION_CONFIRM'); ?>"))
		{
			var form = document.osm_form_cancel_subscription;
			form.subscription_id.value = subscriptionId;
			form.submit();
		}
	}
</script>
</div>