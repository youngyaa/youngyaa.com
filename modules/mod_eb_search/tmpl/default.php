<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
$output = '<input name="search" id="search_eb_box" maxlength="50"  class="inputbox" type="text" size="20" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';		
?>					
<form method="post" name="eb_search_form" id="eb_search_form" action="<?php echo JRoute::_('index.php?option=com_eventbooking&task=search&&Itemid='.$itemId);  ?>">
    <table width="100%" class="search_table">
    	<tr>
    		<td>
    			<?php echo $output ; ?>	
    		</td>
    	</tr>
    	<?php
    	    if ($showCategory)
	        {
    	    ?>
    	    	<tr>
    	    		<td>
    	    			<?php echo $lists['category_id'] ; ?>
    	    		</td>	    		
    	    	</tr>
    	    <?php    
    	    }
    	    if ($showLocation)
	        {
    	    ?>
    	    	<tr>
    	    		<td>
    	    			<?php echo $lists['location_id'] ; ?>
    	    		</td>	    		
    	    	</tr>
    	    <?php    
    	    }
    	?>	
    	<tr>
    		<td>
    			<input type="button" class="btn btn-primary button search_button" value="<?php echo JText::_('EB_SEARCH'); ?>" onclick="searchData();" /> 
    		</td>
    	</tr>
    </table>
    <script language="javascript">
    	function searchData()
	    {
        	var form = document.eb_search_form ;
        	if (form.search.value == '<?php echo $text ?>')
	        {
            	form.search.value = '' ;
        	}
        	form.submit();
    	}
    </script>

	<input type="hidden" name="layout" value="<?php echo $layout; ?>" />
</form>
<style type="text/css">
#eb_search_form td {
	border: none;
	padding: 5px;
}
</style>
