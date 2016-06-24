<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
?>
<div id="osm-subscription-detail-page" class="row-fluid osm-container">
<h1 class="osm-page-title"><?php echo JText::_('OSM_SUBSCRIPTION_DETAIL'); ?></h1>
<form method="post" name="osm_form" id="osm_form" class="form form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('OSM_PLAN'); ?>
		</label>
		<div class="controls">
			<?php echo $this->item->plan_title ; ?>
		</div>
	</div>
	<?php
	if ($this->item->membership_id)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('OSM_MEMBERSHIP_ID'); ?>
			</label>
			<div class="controls">
				<?php echo OSMembershipHelper::formatMembershipId($this->item, $this->config); ?>
			</div>
		</div>
	<?php
	}
	$fields = $this->form->getFields();
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
				<h3 class="osm-heading"><?php echo JText::_($field->title) ; ?></h3>
				<?php
				break ;
			case 'message' :
				?>
					<div class="control-group osm-message">
						<?php echo $field->description ; ?>
					</div>
					<?php
				break ;
			default:
				?>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_($field->title); ?>
					</label>
					<div class="controls">
						<?php
							$fieldValue = $field->value;
							if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
							{
								$fieldValue = implode(', ', json_decode($fieldValue));
							}
							echo $fieldValue;
						?>
					</div>
				</div>
				<?php
				break;
		}
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_CREATED_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('date', $this->item->created_date, $this->config->date_format) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_SUBSCRIPTION_START_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('date', $this->item->from_date, $this->config->date_format) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_SUBSCRIPTION_END_DATE'); ?>
		</label>
		<div class="controls">
			<?php
				if ($this->item->lifetime_membership || $this->item->to_date == '2099-12-31 23:59:59')
				{
					echo JText::_('OSM_LIFETIME');
				}
				else
				{
					echo JHtml::_('date', $this->item->to_date, $this->config->date_format);
				}
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_NET_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol.($this->item->amount > 0 ? number_format($this->item->amount, 2) : "0.00"); ?>
		</div>
	</div>
	<?php
	if ($this->item->discount_amount > 0)
	{
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_DISCOUNT_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol ;  ?><?php echo $this->item->discount_amount > 0 ? number_format($this->item->discount_amount, 2) : "0.00"; ?>
		</div>
	</div>
	<?php
	}
	if ($this->item->tax_amount > 0)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('OSM_TAX_AMOUNT'); ?>
			</label>
			<div class="controls">
				<?php echo $this->config->currency_symbol ;  ?><?php echo number_format($this->item->tax_amount, 2); ?>
			</div>
		</div>
	<?php
	}
	?>

	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('OSM_GROSS_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol ;  ?><?php echo $this->item->gross_amount > 0 ? number_format($this->item->gross_amount, 2) : "0.00"; ?>
		</div>
	</div>
	<?php
		if ($this->item->gross_amount > 0)
		{
		?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('OSM_PAYMENT_METHOD'); ?>
			</label>
			<div class="controls">
				<?php
					$method = os_payments::loadPaymentMethod($this->item->payment_method) ;
					if ($method)
					{
						echo JText::_($method->title);
					}
				?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('OSM_TRANSACTION_ID'); ?>
			</label>
			<div class="controls">
				<?php echo $this->item->transaction_id ; ?>
			</div>
		</div>
		<?php
		}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
		</label>
		<div class="controls">
			<?php
			switch ($this->item->published)
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
		</div>
	</div>
	<div class="form-actions">
		<input type="button" class="btn btn-primary" onclick="window.history.back();" value="<?php echo JText::_('OSM_BACK'); ?>" />
	</div>
</form>
</div>