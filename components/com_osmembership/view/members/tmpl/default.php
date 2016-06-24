<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
$fields = $this->fields;
$cols = count($fields);
$showPlan = $this->params->get('show_plan', 1);
$showSubscriptionDate = $this->params->get('show_subscription_date', 1);
?>
<div id="osm-subscription-history" class="osm-container row-fluid">
<form method="post" name=os_form id="os_form" action="<?php echo JRoute::_('index.php?option=com_osmembership&view=members&Itemid='.$this->Itemid); ?>">
<h1 class="osm-page-title"><?php echo JText::_('OSM_MEMBERS_LIST') ; ?></h1>
	<table width="100%">
		<tr>
			<td align="left">
				<?php echo JText::_( 'OSM_FILTER' ); ?>:
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="input-medium" onchange="this.form.submit();" />
				<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'OSM_GO' ); ?></button>
				<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'OSM_RESET' ); ?></button>
			</td >
		</tr>
	</table>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<?php
					if ($showPlan)
					{
						$cols++;
					?>
						<th>
							<?php echo JText::_('OSM_PLAN') ?>
						</th>
					<?php
					}
					foreach($fields as $field)
					{
					?>
						<th><?php echo $field->title; ?></th>
					<?php
					}
					if ($showSubscriptionDate)
					{
						$cols++;
					?>
						<th class="center">
							<?php echo JText::_('OSM_SUBSCRIPTION_DATE') ; ?>
						</th>
					<?php
					}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			$fieldsData = $this->fieldsData;
			for ($i = 0 , $n = count($this->items) ; $i < $n ; $i++)
			{
				$row = $this->items[$i] ;
			?>
				<tr>
					<?php
					if ($showPlan)
					{
					?>
						<td>
							<?php echo $row->plan_title; ?>
						</td>
					<?php
					}
					foreach ($fields as $field)
					{
						if ($field->is_core)
						{
							$fieldValue = $row->{$field->name};
						}
						elseif (isset($fieldsData[$row->id][$field->id]))
						{
							$fieldValue = $fieldsData[$row->id][$field->id];
						}
						else
						{
							$fieldValue = '';
						}
						?>
							<td>
								<?php echo $fieldValue; ?>
							</td>
						<?php
					}
					if ($showSubscriptionDate)
					{
					?>
						<td class="center">
							<?php echo JHtml::_('date', $row->created_date, $this->config->date_format); ?>
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
			if ($this->pagination->total > $this->pagination->limit)
			{
			?>
			<tfoot>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>
			<?php
			}
		?>
	</table>
</form>
</div>