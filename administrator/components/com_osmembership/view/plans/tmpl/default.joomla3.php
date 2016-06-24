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
$ordering = ($this->state->filter_order == 'tbl.ordering');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
$saveOrder	= $listOrder == 'tbl.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_osmembership&task=plan.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'planList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
JHtml::_('behavior.modal', 'a.modal');
$config = OSMembershipHelper::getConfig();
$cols = 11;
if ($this->showThumbnail)
{
	$cols++;
}
if ($this->showCategory)
{
	$cols++;
}
?>
<form action="index.php?option=com_osmembership&view=plans" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('OSM_FILTER_SEARCH_PLANS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('OSM_SEARCH_PLANS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					if (isset($this->lists['filter_category_id']))
					{
						echo $this->lists['filter_category_id'];
					}
					echo $this->lists['filter_state'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="adminlist table table-striped" id="planList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="20">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<?php
						if ($this->showCategory)
						{
						?>
							<th class="title">
								<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_CATEGORY'), 'b.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
							</th>
						<?php
						}
						if ($this->showThumbnail)
						{
						?>
							<th class="title" width="10%">
								<?php echo JText::_('OSM_THUMB'); ?>
							</th>
						<?php
						}
					?>
					<th class="title" width="8%">
						<?php echo JText::_('OSM_LENGTH'); ?>
					</th>
					<th class="center" width="8%">
						<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_RECURRING'), 'tbl.recurring_subscription', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="title" width="8%">
						<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_PRICE'), 'tbl.price', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="title center" width="12%">
						<?php echo JText::_('OSM_TOTAL_SUBSCRIBERS'); ?>
					</th>
					<th class="title center" width="12%">
						<?php echo JText::_('OSM_ACTIVE_SUBSCRIBERS'); ?>
					</th>
					<th width="5%">
						<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th width="2%">
						<?php echo JHtml::_('searchtools.sort',  JText::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_osmembership&task=plan.edit&cid[]='. $row->id);
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'plan.' );
				$canChange	= $user->authorise('core.edit.state',	'com_osmembership.category.'.$row->id);
				$img 	= $row->recurring_subscription ? 'tick.png' : 'publish_x.png';
				$alt 	= $row->recurring_subscription ? JText::_( 'Recurring' ) : JText::_( 'Onetime' );
				$img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true);

				$symbol = $row->currency_symbol ? $row->currency_symbol : $row->currency;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip"';
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
						<i class="icon-menu"></i>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering ?>" class="width-20 text-area-order "/>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>"><?php echo $row->title ; ?></a>
					</td>
					<?php
						if ($this->showCategory)
						{
						?>
							<td><?php echo $row->category_title; ?></td>
						<?php
						}
						if ($this->showThumbnail)
						{
						?>
							<td class="center">
								<?php
								if ($row->thumb)
								{
									?>
									<a href="<?php echo JUri::root().'media/com_osmembership/'.$row->thumb ; ?>" class="modal"><img src="<?php echo JUri::root().'/media/com_osmembership/'.$row->thumb ; ?>" /></a>
								<?php
								}
								?>
							</td>
						<?php
						}
					?>
					<td>
						<?php
						if ($row->lifetime_membership)
						{
							echo JText::_('OSM_LIFETIME');
						}
						else
						{
							$length = $row->subscription_length;
							switch ($row->subscription_length_unit) {
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
							echo $row->subscription_length.' '.$text;
						}
						?>
					</td>
					<td class="center">
						<?php echo $img ; ?>
					</td>
					<td>
						<?php
						if ($row->price > 0)
						{
							echo OSMembershipHelper::formatCurrency($row->price, $config, $symbol);
						}
						else
						{
							echo JText::_('OSM_FREE');
						}
						?>
					</td>
					<td class="center">
						<?php echo OSMembershipHelper::countSubscribers($row->id); ?>
					</td>
					<td class="center">
						<?php echo OSMembershipHelper::countSubscribers($row->id, 1); ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
					</td>
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />	
	<?php echo JHtml::_( 'form.token' ); ?>
	</div>
</form>