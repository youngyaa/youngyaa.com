<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class EventbookingViewDashboardHtml extends RADViewHtml
{
	/**
	 * This view doesn't have a model associated to it
	 * @var bool
	 */
	public $hasModel = false;

	/**
	 * Display dashboard view
	 */
	public function display()
	{
		$this->latestRegistrants = RADModel::getInstance('Registrants', 'EventbookingModel', array('table_prefix' => '#__eb_', 'ignore_request' => true, 'remember_states' => false))
			->setState('limitstart', 0)
			->setState('limit', 5)
			->setState('filter_order', 'tbl.id')
			->setState('filter_order_Dir', 'DESC')
			->getData();

		$this->upcomingEvents = RADModel::getInstance('Events', 'EventbookingModel', array('table_prefix' => '#__eb_', 'ignore_request' => true, 'remember_states' => false))
			->setState('limitstart', 0)
			->setState('limit', 5)
			->setState('filter_order', 'tbl.event_date')
			->setState('filter_order_Dir', 'ASC')
			->setState('filter_upcoming_events', 1)
			->getData();

		$this->config = EventbookingHelper::getConfig();
		parent::display();
	}

	/**
	 *
	 * Function to create the buttons view.
	 *
	 * @param string $link  target url
	 * @param string $image path to image
	 * @param string $text  image description
	 */
	function quickIconButton($link, $image, $text, $id = null)
	{
		$language = JFactory::getLanguage();
		?>
			<div
				style="float:<?php echo ($language->isRTL()) ? 'right' : 'left'; ?>;" <?php if ($id) echo 'id="' . $id . '"'; ?>>
				<div class="icon">
					<a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
						<?php echo JHtml::_('image', 'administrator/components/com_eventbooking/assets/icons/' . $image, $text); ?>
						<span><?php echo $text; ?></span>
					</a>
				</div>
			</div>
		<?php
	}
}