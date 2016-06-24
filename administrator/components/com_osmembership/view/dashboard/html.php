<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for Membership Pro component
 *
 * @package        Joomla
 * @subpackage     Membership Pro 
 */
class OSMembershipViewDashboardHtml extends MPFViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->subscriptions = MPFModel::getInstance('Subscriptions', 'OSMembershipModel')
			->limitstart(0)
			->limit(10)
			->filter_order('tbl.created_date')
			->filter_order_Dir('DESC')
			->getData();
		$this->config = OSMembershipHelper::getConfig();
		$this->data = OSMembershipModelSubscriptions::getStatisticsData();
					
		parent::display();
	}
	
	/**
	 * 
	 * Function to create the buttons view.
	 * @param string $link targeturl
	 * @param string $image path to image
	 * @param string $text image description
	 */
	protected function quickiconButton($link, $image, $text, $id = null)
	{
		$language = JFactory::getLanguage();
		?>	
		<div style="float:<?php echo ($language->isRTL()) ? 'right' : 'left'; ?>;" <?php if ($id) echo 'id="'.$id.'"'; ?>>
			<div class="icon">
				<a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
					<?php echo JHtml::_('image', 'administrator/components/com_osmembership/assets/icons/' . $image, $text); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}