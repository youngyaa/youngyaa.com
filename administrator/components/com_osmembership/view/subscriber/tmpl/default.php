<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
JToolBarHelper::title(   JText::_( 'OSM_EDIT_SUBSCRIBER' ), 'generic.png' );
JToolBarHelper::save('save');
JToolBarHelper::cancel('cancel');
if (JFactory::getUser()->authorise('core.admin', 'com_osmembership'))
{
	JToolBarHelper::preferences('com_osmembership');
}
$db = JFactory::getDbo();
$selectedState = '';
?>
<div class="row-fluid">
<form action="index.php?option=com_osmembership&view=subscriber" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#profile-page" data-toggle="tab"><?php echo JText::_('OSM_PROFILE_INFORMATION');?></a></li>
		<li><a href="#subscription-history-page" data-toggle="tab"><?php echo JText::_('OSM_SUBSCRIPTION_HISTORY');?></a></li>
		<?php
			if (count($this->plugins)) {
				$count = 0 ;
				foreach ($this->plugins as $plugin) {
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
			<table class="admintable adminform">
				<?php
					if ($this->item->user_id)
					{
					?>
						<tr>
							<td class="key">
								<?php echo JText::_('OSM_USERNAME'); ?>
							</td>
							<td>
								<?php echo $this->item->username; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('OSM_PASSWORD'); ?>
							</td>
							<td>
								<input type="password" name="password" size="20" value="" />
							</td>
						</tr>
					<?php
					}
					if ($this->item->membership_id) {
					?>
					<tr>
						<td class="key">
							<?php echo JText::_('OSM_MEMBERSHIP_ID'); ?>
						</td>
						<td>
							<?php echo OSMembershipHelper::formatMembershipId($this->item, $this->config);?>
						</td>
					</tr>
					<?php
					}
					$fields = $this->form->getFields();
					if ($fields['state']->type == 'State')
					{
						$stateType = 1;
					}
					else
					{
						$stateType = 0;
					}
					if (isset($fields['state']))
					{
						$selectedState = $fields['state']->value;
					}

					if (isset($fields['email']))
					{
						$fields['email']->setAttribute('class', 'validate[required,custom[email]]');
					}

					foreach ($fields as $field)
					{
						switch (strtolower($field->type))
						{
							case 'heading' :
								?>
								<tr><td colspan="2"><h3 class="osm-heading"><?php echo JText::_($field->title) ; ?></h3></td></tr>
								<?php
								break ;
							case 'message' :
								?>
								<tr>
									<td colspan="2">
										<p class="osm-message">
											<?php echo $field->description ; ?>
										</p>
									</td>
								</tr>
								<?php
								break ;
							default:
								?>
									<tr id="field_<?php echo $field->name; ?>">
										<td class="key">
											<?php echo JText::_($field->title); ?>
										</td>
										<td class="controls">
											<?php echo $field->input; ?>
										</td>
									</tr>
								<?php
								break;
						}
					}
				?>
			</table>
		</div>
		<div class="tab-pane" id="subscription-history-page">
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th>
							<?php echo JText::_('OSM_PLAN') ?>
						</th>
						<th class="title center">
							<?php echo JText::_('OSM_ACTIVATE_TIME') ; ?>
						</th>
						<th style="text-align: right;">
							<?php echo JText::_('OSM_GROSS_AMOUNT') ; ?>
						</th>
						<th class="title center">
							<?php echo JText::_('OSM_SUBSCRIPTION_STATUS'); ?>
						</th>
						<th class="title center">
							<?php echo JText::_('OSM_TRANSACTION_ID') ; ?>
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
					for ($i = 0 , $n = count($this->items) ; $i < $n ; $i++) {
						$row = $this->items[$i] ;
						$link = JRoute::_('index.php?option=com_osmembership&view=subscriber&id='.$row->id);
					?>
						<tr>
							<td>
								<a href="<?php echo $link; ?>"><?php echo $row->plan_title; ?></a>
							</td>
							<td class="center">
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
							<td style="text-align: right;">
								<?php echo $this->config->currency_symbol.number_format($row->gross_amount, 2) ; ?>
							</td>
							<td class="center">
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
							<td class="center">
								<?php echo $row->transaction_id ; ?>
							</td>
							<?php
								if ($this->config->activate_invoice_feature)
								{
								?>
									<td class="center">
										<a href="<?php echo JRoute::_('index.php?option=com_osmembership&task=download_invoice&id='.$row->id); ?>" title="<?php echo JText::_('OSM_DOWNLOAD'); ?>"><?php echo OSMembershipHelper::formatInvoiceNumber($row, $this->config) ; ?></a>
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
			if (count($this->plugins)) {
				$count = 0 ;
				foreach ($this->plugins as $plugin) {
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
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<?php
	if ($stateType)
	{
	?>
	<script type="text/javascript">
		var siteUrl = "<?php echo JUri::root(); ?>";
		function buildStateField(stateFieldId, countryFieldId, defaultState)
		{
			(function($) {
				if($('#' + stateFieldId).length)
				{
					//set state
					if ($('#' + countryFieldId).length)
					{
						var countryName = $('#' + countryFieldId).val();
					}
					else
					{
						var countryName = '';
					}
					$.ajax({
						type: 'POST',
						url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
						success: function(data) {
							$('#field_' + stateFieldId + ' .controls').html(data);
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(textStatus);
						}
					});
					//Bind onchange event to the country
					if ($('#' + countryFieldId).length)
					{
						$('#' + countryFieldId).change(function(){
							$.ajax({
								type: 'POST',
								url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
								success: function(data) {
									$('#field_' + stateFieldId + ' .controls').html(data);
								},
								error: function(jqXHR, textStatus, errorThrown) {
									alert(textStatus);
								}
							});

						});
					}
				}//end check exits state

			})(jQuery);
		}
		(function($){
			$(document).ready(function(){
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
			})
		})(jQuery);
	</script>
	<?php
	}
	?>
</form>
</div>