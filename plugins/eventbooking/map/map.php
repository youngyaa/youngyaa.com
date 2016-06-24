<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class plgEventBookingMap extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_map', JPATH_ADMINISTRATOR);
	}

	/**
	 * Display event location in a map
	 *
	 * @param $row
	 *
	 * @return array|string
	 */
	public function onEventDisplay($row)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from('#__eb_locations AS a')
			->innerJoin('#__eb_events AS b ON a.id = b.location_id')
			->where('b.id = ' . (int) $row->id);;
		$db->setQuery($query);
		$location = $db->loadObject();

		ob_start();
		$this->drawMap($location);
		$form = ob_get_clean();
		if (empty($location->lat) && empty($location->long))
		{
			return '';
		}
		else
		{
			return array('title' => JText::_('PLG_EB_MAP'),
			             'form'  => $form
			);
		}
	}

	/**
	 * Display event location in a map
	 *
	 * @param $location
	 */
	private function drawMap($location)
	{
		$uri = JUri::getInstance();
		if ($uri->getScheme() == 'https')
		{
			$https = true;
		}
		else
		{
			$https = false;
		}
		$config     = EventbookingHelper::getConfig();
		$zoomLevel  = $config->zoom_level ? (int) $config->zoom_level : 10;
		$mapWidth   = $this->params->def('map_width', 700);
		$mapHeight  = $this->params->def('map_height', 500);
		$bubbleText = "<ul class=\"bubble\">";
		$bubbleText .= "<li class=\"location_name\"><h4>";
		$bubbleText .= addslashes($location->name);
		$bubbleText .= "</h4></li>";
		$bubbleText .= "<li class=\"address\">" . addslashes($location->address . ', ' . $location->city . ', ' . $location->state . ', ' . $location->zip . ', ' . $location->country) . "</li>";
		$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr=' . $location->lat . ',' . $location->long . '(' . addslashes($location->address . ', ' . $location->city . ', ' . $location->state . ', ' . $location->zip . ', ' . $location->country) . ')';
		$bubbleText .= "<li class=\"address getdirection\"><a href=\"" . $getDirectionLink . "\" target=\"_blank\">" . JText::_('EB_GET_DIRECTION') . "</li>";
		$bubbleText .= "</ul>";
		?>
		<script type="text/javascript"
		        src="<?php echo ($https) ? 'https' : 'http'?>://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			(function ($) {
				$(document).ready(function () {
					function initialize() {
						var latlng = new google.maps.LatLng(<?php echo $location->lat ?>, <?php echo $location->long; ?>);
						var myOptions = {
							zoom: <?php echo $zoomLevel; ?>,
							center: latlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


						var marker = new google.maps.Marker({
							position: latlng,
							map: map,
							title: "<?php echo $location->name ; ?>"
						});

						var contentString = '<?php echo $bubbleText ; ?>';
						var infowindow = new google.maps.InfoWindow({
							content: contentString,
							//maxWidth: 20
						});
						google.maps.event.addListener(marker, 'click', function () {
							infowindow.open(map, marker);
						});
						infowindow.open(map, marker);
					}

					initialize();
				});
			})(jQuery);
		</script>
		<div id="mapform">
			<div id="map_canvas" style="width: <?php echo $mapWidth; ?>px; height: <?php echo $mapHeight; ?>px"></div>
		</div>
	<?php
	}
}	