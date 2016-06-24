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
$ordering = ($this->state->filter_order == 'tbl.ordering');
if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}
?>
<form action="index.php?option=com_osmembership&view=plugins" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
<table width="100%">
<tr>
	<td align="left">
		<?php echo JText::_( 'OSM_FILTER' ); ?>:
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search ;?>" class="text_area search-query" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'OSM_GO' ); ?></button>
		<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'OSM_RESET' ); ?></button>
	</td>	
	<td style="float:right;">
		<?php echo $this->lists['filter_state']; ?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  JText::_('OSM_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th class="title" width="20%">
				<?php echo JHtml::_('grid.sort', JText::_('OSM_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>			
			<th class="title">
				<?php echo JHtml::_('grid.sort', JText::_('OSM_AUTHOR') , 'tbl.author', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>			
			<th class="title center">
				<?php echo JHtml::_('grid.sort', JText::_('OSM_AUTHOR_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>	
			<th class="title center">
				<?php echo JHtml::_('grid.sort', JText::_('OSM_RECURRING_SUPPORT') , 'tbl.support_recurring_subscription', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>		
			<th class="title center">
				<?php echo JHtml::_('grid.sort', JText::_('OSM_PUBLISHED') , 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  'OSM_ORDER', 'tbl.ordering', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				<?php echo JHtml::_('grid.order',  $this->items , 'filesave.png', 'save_plugin_order' ); ?>
			</th>												
			<th>
				<?php echo JHtml::_('grid.sort', JText::_('OSM_ID') , 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
		</tr>		
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_osmembership&task=plugin.edit&cid[]='. $row->id );
		$checked 	= JHtml::_('grid.id',   $i, $row->id );				
		$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'plugin.' );
        $img 	= $row->support_recurring_subscription ? 'tick.png' : 'publish_x.png';
        $img = JHtml::_('image','admin/'.$img, '', array('border' => 0), true);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $checked; ?>
			</td>	
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->name; ?>
				</a>
			</td>
			<td>
				<?php echo $row->title; ?>
			</td>												
			<td>
				<?php echo $row->author; ?>
			</td>
			<td class="center">
				<?php echo $row->author_email;?>
			</td>
			<td class="center">
				<?php echo $img ; ?>
			</td>
			<td class="center">
				<?php echo $published ; ?>
			</td>			
			<td class="order">
				<span><?php echo $this->pagination->orderUpIcon( $i, true,'plugin.orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'plugin.orderdown', 'Move Down', $ordering ); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="input-mini" style="text-align: center" />
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
	<table class="adminform" style="margin-top: 50px;">
		<tr>
			<td>
				<fieldset class="adminform">
					<legend><?php echo JText::_('OSM_INSTALL_NEW_PLUGIN'); ?></legend>
					<table>
						<tr>
							<td>
								<input type="file" name="plugin_package" id="plugin_package" size="50" class="inputbox" /> <input type="button" class="button" value="<?php echo JText::_('OSM_INSTALL'); ?>" onclick="installPlugin();" />
							</td>
						</tr>
					</table>					
				</fieldset>
			</td>
		</tr>		
	</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>				 
	<script type="text/javascript">
		function installPlugin() {
			var form = document.adminForm ;
			if (form.plugin_package.value =="") {
				alert("<?php echo JText::_('OSM_CHOOSE_PLUGIN'); ?>");
				return ;	
			}
			
			form.task.value = 'plugin.install' ;
			form.submit();
		}
	</script>
</form>