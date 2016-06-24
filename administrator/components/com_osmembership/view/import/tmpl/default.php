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
defined('_JEXEC') or die;

JToolBarHelper::title(JText::_('OSM_IMPORT_SUBSCRIBERS_TITLE'));
JToolBarHelper::save('subscription.import');
JToolBarHelper::cancel('subscription.cancel');
?>
<form action="index.php?option=com_osmembership&view=import" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<table class="admintable adminform">
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_CSV_FILE'); ?>											
			</td>
			<td>
				<input type="file" name="csv_subscribers" size="50">	
			</td>
			<td>
				<?php echo JText::_('OSM_CSV_FILE_EXPLAIN'); ?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>			
</form>