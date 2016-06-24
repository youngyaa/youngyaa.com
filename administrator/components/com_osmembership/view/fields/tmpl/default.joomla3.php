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
	$saveOrderingUrl = 'index.php?option=com_osmembership&task=field.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'fieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
?>
<form action="index.php?option=com_osmembership&view=fields" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('OSM_FILTER_SEARCH_FIELDS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('OSM_SEARCH_FIELDS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo $this->lists['show_core_field'];
					echo $this->lists['plan_id'];
					echo $this->lists['filter_state'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"> </div>
	<table class="adminlist table table-striped" id="fieldList">
	<thead>
		<tr>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="20">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('searchtools.sort',  'OSM_NAME', 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('searchtools.sort',  'OSM_TITLE', 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('searchtools.sort',  'OSM_FIELD_TYPE', 'tbl.field_type', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>			
			<th class="title center">
				<?php echo JHtml::_('searchtools.sort',  'OSM_CORE_FIELD', 'tbl.is_core', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th class="title center">
				<?php echo JHtml::_('searchtools.sort',  'OSM_PUBLISHED', 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>			
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('searchtools.sort',  'ID', 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	$ordering = ($this->state->filter_order == 'tbl.ordering');
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_osmembership&task=field.edit&cid[]='. $row->id );
		$checked 	= JHtml::_('grid.id',   $i, $row->id );		
		$published = JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'field.' );					
		$img 	= $row->required ? 'tick.png' : 'publish_x.png';
		$task 	= $row->required ? 'un_required' : 'required';
		$alt 	= $row->required ? JText::_( 'Required' ) : JText::_( 'Not required' );
		$action = $row->required ? JText::_( 'Not Require' ) : JText::_( 'Require' );
        $img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true) ;
        $href = '
    		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">'.
            $img .'</a>'
        ;
        $img 	= $row->is_core ? 'tick.png' : 'publish_x.png';
        $img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true);
        $canChange	= $user->authorise('core.edit.state',	'com_osmembership.category.'.$row->id);	
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
				<a href="<?php echo $link; ?>">
					<?php echo $row->name; ?>
				</a>
			</td>	
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->title; ?>
				</a>
			</td>
			<td>
				<?php					
					echo $row->fieldtype;								
			 	?>
			</td>									
			<td class="center">
				<?php echo $img ; ?>
			</td>
			<td class="center">
				<?php echo $published ; ?>
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