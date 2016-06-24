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

class modEventBookingGoogleMapHelper
{
	protected $_module;
	protected $_itemId;
	protected $_params;
	protected $_rows;
	protected $_db;
	protected $_query;
	protected $_config;
	protected $_location;

	/**
	 * initialization class
	 *
	 */
	function __construct($module, $params)
	{
		$this->loadLanguage();
		$this->_module = $module;
		$this->_params = $params;
		$this->_itemId = EventbookingHelper::getItemid();
		if (empty($this->_db)) $this->_db = JFactory::getDbo();
		if (empty($this->_query)) $this->_query = $this->_db->getQuery(true);
		if (empty($this->_config)) $this->_config = EventbookingHelper::getConfig();
		if (empty($this->_rows)) $this->_rows = $this->loadAllEventBooking();
		$this->loadMapInListing();
	}

	/**
	 * Enter description here...
	 *
	 */
	protected function loadLanguage()
	{
		JFactory::getLanguage()->load('com_eventbooking', JPATH_SITE, JFactory::getLanguage()->getTag(), true);
	}

	/**
	 * get all event published
	 *
	 */
	protected function loadAllEventBooking($locationId = 0)
	{
		$hidePastEvents = $this->_params->get('hide_past_events', 0);
		$categoryIds    = $this->_params->get('category_ids');
		$user           = JFactory::getUser();
		$this->_query
			->clear()
			->select('a.id,a.title,a.location_id')
			->from('#__eb_events AS a')
			->select('b.name, b.lat, b.long, b.address, b.city, b.state, b.zip, b.country')
			->innerJoin('#__eb_locations AS b ON a.location_id = b.id')
			->select('c.category_id AS catid')
			->innerJoin('#__eb_event_categories AS c ON a.id = c.event_id')
			->where('a.published=1')
			->where('a.location_id !=0')
			->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order('a.event_date ASC');
		if ($hidePastEvents)
		{
			$currentDate = JHtml::_('date', 'Now', 'Y-m-d');
			$this->_query->where('DATE(a.event_date) >= "' . $currentDate . '"');
		}
		if (count($categoryIds))
		{
			$this->_query->where('c.main_category = 1 AND c.category_id IN (' . implode(',', $categoryIds) . ')');
		}
		if (JLanguageMultilang::isEnabled())
		{
			$this->_query->where('a.language IN (' . $this->_db->Quote(JFactory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')');
		}
		if ($locationId)
		{
			$this->_query->where('a.location_id=' . (int) $locationId);
		}
		else
		{
			$this->_query->group('a.location_id');
		}
		$this->_query->group('a.id');
		$this->_db->setQuery($this->_query);
		$this->_rows = $this->_db->loadObjectList();

		return $this->_rows;
	}

	/**
	 * general google map for event
	 *
	 */
	protected function loadMapInListing()
	{
		$locationGroups = $this->loadAllEventBooking();
		if (!count($locationGroups))
		{
			echo JText::_('EB_NO_EVENTS');

			return;
		}
		$zoomLevel = $this->_params->get('zoom_level', 10);
		$doc       = JFactory::getDocument();
		$doc->addScript($this->_config->use_https ? 'https' : 'http' . '://maps.googleapis.com/maps/api/js?sensor=false');
		?>
		<script type="text/javascript">
			Eb.jQuery(document).ready(function ($) {
				var markers = [];
				var markerIndex = 0;
				var markerArray = [];
				var infowindow;
				var cityCircle;
				var myHome = new google.maps.LatLng(<?php echo $this->_rows[0]->lat; ?>, <?php echo $this->_rows[0]->long; ?>);
				<?php
					  for($i=0; $i<count($locationGroups); $i++)
					  {
						   $row = $locationGroups[$i];
						   if(($row->lat != "") and ($row->long != ""))
						   {
						   ?>
				var eventListing<?php echo $row->id?> = new google.maps.LatLng(<?php echo $row->lat; ?>, <?php echo $row->long; ?>);
				<?php
				   }
			  }
		  ?>
				var mapOptions = {
					zoom: <?php echo $zoomLevel; ?>,
					streetViewControl: true,
					mapTypeControl: true,
					panControl: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: myHome,
				};
				var map = new google.maps.Map(document.getElementById("map<?php echo $this->_module->id; ?>"), mapOptions);
				var infoWindow = new google.maps.InfoWindow();
				var markerBounds = new google.maps.LatLngBounds();


				function makeMarker(options) {
					var pushPin = new google.maps.Marker({map: map});
					pushPin.setOptions(options);
					google.maps.event.addListener(pushPin, 'click', function () {
						infoWindow.setOptions(options);
						infoWindow.open(map, pushPin);
					});
					markerArray.push(pushPin);
					return pushPin;
				}

				google.maps.event.addListener(map, 'click', function () {
					infoWindow.close();
				});
				<?php
						  for($i=0;$i<count($locationGroups);$i++)
						{
							  $row = $locationGroups[$i];
							  $samelocations = $this->loadAllEventBooking($row->location_id);
							if(($row->lat != "") and ($row->long != ""))
							{
							?>
				makeMarker({
					position: eventListing<?php echo $row->id?>,
					title: "<?php echo addslashes($row->title);?>",
					content: '<div class="row-fluid"><ul><?php foreach ($samelocations as $samelocation){ echo '<li><h4>'. JHtml::link(EventbookingHelperRoute::getEventRoute($samelocation->id,$samelocation->catid,$this->_itemId), addslashes($samelocation->title)).'</h4></li>'; }?></ul></div>',
					icon: new google.maps.MarkerImage('<?php echo JURI::root()?>modules/mod_eb_googlemap/asset/marker/marker.png')
				});
				<?php
				}
			  }
		  ?>
			});
		</script>
	<?php
	}

}

?>
