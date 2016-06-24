<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

if (count($rows))
{
?>
	<ul class="menu">
		<?php				
			foreach ($rows  as $row)
			{
			?>
				<li>
					<a href="<?php echo JRoute::_(EventbookingHelperRoute::getCategoryRoute($row->id, $itemId)); ?>"><?php echo $row->name; ?></a>
				</li>
			<?php	
			}
		?>			
	</ul>
<?php
}
?>					

