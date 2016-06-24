<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;
$fields = $form->getFields();
?>
<table class="os_table" width="100%">
	<tr>
		<td class="title_cell" width="35%">
			<?php echo  JText::_('OSM_PLAN') ?>
		</td>
		<td class="field_cell">
			<?php echo $planTitle;?>
		</td>
	</tr>
	<?php
		if ($row->coupon_id)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('code'))
				->from('#__osmembership_coupons')
				->where('id = '. $row->coupon_id);
			$db->setQuery($query);
			$couponCode = $db->loadResult();
		?>
			<tr>
				<td class="title_cell" width="35%">
					<?php echo  JText::_('OSM_COUPON') ?>
				</td>
				<td class="field_cell">
					<?php echo $couponCode; ?>
				</td>
			</tr>
		<?php
		}
		if (isset($username))
		{
		?>
			<tr>
				<td class="title_cell" width="35%">
					<?php echo  empty($config->use_email_as_username) ? JText::_('OSM_USERNAME') : $fields['email']->title; ?>
				</td>
				<td class="field_cell">
					<?php echo $username; ?>
				</td>
			</tr>
		<?php
		}
		if (isset($password) && !$toAdmin)
		{
		?>
			<tr>
				<td class="title_cell" width="35%">
					<?php echo  JText::_('OSM_PASSWORD') ?>
				</td>
				<td class="field_cell">
					<?php echo $password; ?>
				</td>
			</tr>
		<?php
		}
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('OSM_SUBSCRIPTION_START_DATE'); ?>
		</td>
		<td class="field_cell">
			<?php echo JHtml::_('date', $row->from_date, $config->date_format); ?>
		</td>
	</tr>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('OSM_SUBSCRIPTION_END_DATE'); ?>
		</td>
		<td class="field_cell">
			<?php
				if ($lifetimeMembership || $row->to_date == '2099-12-31 23:59:59')
				{
					echo JText::_('OSM_LIFETIME');
				}
				else
				{
					echo JHtml::_('date', $row->to_date, $config->date_format);
				}
			?>
		</td>
	</tr>
	<?php

	if (!empty($config->use_email_as_username))
	{
		unset($fields['email']);
	}

	foreach ($fields as $field)
	{
		if (!$field->visible)
		{
			continue;
		}

		switch (strtolower($field->type))
		{
			case 'heading' :
				?>
					<tr>
						<td colspan="2"><h3 class="osm-heading"><?php echo JText::_($field->title) ; ?></h3></td>
					</tr>

					<?php
					break ;
				case 'message' :
					?>
						<tr>
							<td colspan="2">
								<p class="osm-message"><?php echo $field->description ; ?></p>
							</td>
						</tr>
						<?php
					break ;
				case 'date':
					?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_($field->title); ?>
						</td>
						<td class="field_cell">
							<?php
							$fieldValue = $field->value;
							if ($fieldValue)
							{
								try
								{
									$formattedValue = JHtml::_('date', $fieldValue, $config->date_format, null);
									echo $formattedValue;
								}
								catch (Exception $e)
								{
									echo $fieldValue;
								}
							}
							else
							{
								echo $fieldValue;
							}
							?>
						</td>
					</tr>
					<?php
					break;
				default:
					?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_($field->title); ?>
						</td>
						<td class="field_cell">
							<?php
							if ($field->name == 'state')
							{
								$fieldValue = OSMembershipHelper::getStateName($row->country, $field->value);
							}
							else
							{
								$fieldValue = $field->value;
								if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
								{
									$fieldValue = implode(', ', json_decode($fieldValue));
								}
							}
							echo $fieldValue;
							?>
						</td>
					</tr>
					<?php
					break;
			}
		}
		if ($row->gross_amount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('OSM_PRICE'); ?>
				</td>
				<td>
					<?php echo OSMembershipHelper::formatCurrency($row->amount, $config, $currencySymbol); ?>
				</td>
			</tr>
			<?php
				if ($row->discount_amount > 0)
				{
				?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_('OSM_DISCOUNT'); ?>
						</td>
						<td>
							<?php echo OSMembershipHelper::formatCurrency($row->discount_amount, $config, $currencySymbol); ?>
						</td>
					</tr>
				<?php
				}
				if ($row->tax_amount > 0)
				{
				?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_('OSM_TAX'); ?>
						</td>
						<td>
							<?php echo OSMembershipHelper::formatCurrency($row->tax_amount, $config, $currencySymbol); ?>
						</td>
					</tr>
				<?php
				}
				if ($row->payment_processing_fee > 0)
				{
				?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_('OSM_PAYMENT_FEE'); ?>
						</td>
						<td>
							<?php echo OSMembershipHelper::formatCurrency($row->payment_processing_fee, $config, $currencySymbol); ?>
						</td>
					</tr>
				<?php
				}
				if ($row->discount_amount > 0 || $row->tax_amount > 0 || $row->payment_processing_fee > 0)
				{
				?>
					<tr>
						<td class="title_cell">
							<?php echo JText::_('OSM_GROSS_AMOUNT'); ?>
						</td>
						<td>
							<?php echo OSMembershipHelper::formatCurrency($row->gross_amount, $config, $currencySymbol); ?>
						</td>
					</tr>
				<?php
				}
			?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('OSM_PAYMENT_OPTION'); ?>
				</td>
				<td class="field_cell">
					<?php
						$method = os_payments::loadPaymentMethod($row->payment_method) ;
						if ($method)
						{
							echo JText::_($method->title);
						}
					?>
				</td>
			</tr>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('OSM_TRANSACTION_ID'); ?>
				</td>
				<td class="field_cell">
					<?php echo $row->transaction_id ; ?>
				</td>
			</tr>
		<?php
			if ($toAdmin && ($row->payment_method == 'os_creditcard'))
			{
			?>
			<tr>
				<td class="title_cell">
					<?php echo JText::_('OSM_LAST_4DIGITS'); ?>
				</td>
				<td class="field_cell">
					<?php echo $last4Digits; ?>
				</td>
			</tr>
			<?php
			}
		}
	?>
</table>