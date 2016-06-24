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
defined( '_JEXEC' ) or die ;	
?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'new_item') {			
		newLanguageItem();						
	} else {
		Joomla.submitform( pressbutton );
	}
}		
function newLanguageItem() {
	table = document.getElementById('lang_table');
	row = table.insertRow(1);		
	cell0  = row.insertCell(0);
	cell0.innerHTML = '<input type="text" name="extra_keys[]" class="inputbox" size="50" />';
	cell1 = row.insertCell(1);		
	cell2 = row.insertCell(2);
	cell2.innerHTML = '<input type="text" name="extra_values[]" class="inputbox" size="100" />';
}
</script>
<form action="index.php?option=com_eventbooking&view=language" method="post" name="adminForm" id="adminForm">
	<table width="100%">
		<tr>			
			<td style="text-align: left;">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
				<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td style="text-align: right;">
				<?php echo $this->lists['filter_item']; ?>
				<?php echo $this->lists['filter_language']; ?>							
			</td>			
		</tr>
	</table>			
	<table class="adminlist table table-striped" id="lang_table">
		<thead>
			<tr>
				<th class="key" style="width:20%; text-align: left;"><?php echo JText::_('EB_KEY'); ?></th>
				<th class="key" style="width:40%; text-align: left;"><?php echo JText::_('EB_ORIGINAL'); ?></th>
				<th class="key" style="width:40%; text-align: left;"><?php echo JText::_('EB_TRANSLATION'); ?></th>
			</tr>
		</thead>				
		<?php
			if (strpos($this->state->filter_item, 'admin') !== FALSE)
			{
				$languageItem = substr($this->state->filter_item, 6);
			}	
			else 
			{
				$languageItem = $this->state->filter_item;
			}							
			$original = $this->items['en-GB'][$languageItem] ;			
			$trans = $this->items[$this->state->filter_language][$languageItem] ;
			
			foreach ($original as  $key=>$value) 
			{
				$show = true ;
				if (isset($trans[$key])) 
				{
					$translatedValue = $trans[$key];
					$missing = false ; 	
				} 
				else 
				{
					$translatedValue = $value;
					$missing = true ;
				}						  								
				?>
					<tr>
					<td class="key" style="text-align: left;"><?php echo $key; ?></td>
					<td style="text-align: left;"><?php echo $value; ?></td>
					<td>						
						<input type="hidden" name="keys[]" value="<?php echo $key; ?>" />
						<input type="text" name="<?php echo $key; ?>" class="input-xxlarge" value="<?php echo htmlspecialchars($translatedValue);  ?>" />
						<?php
							if ($missing) 
							{
							?>
								<span style="color:red;">*</span>
							<?php	
							}							
						?>
					</td>					
				</tr>	
				<?php						
			}
		?>
		<tfoot>
		<tr>			
			<td colspan="3">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>							
		</tr>
	</tfoot>
	</table>
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="task" value="" />				
	<?php echo JHtml::_( 'form.token' ); ?>
</form>