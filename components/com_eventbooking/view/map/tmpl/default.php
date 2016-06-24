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
$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr='.$this->location->lat.','.$this->location->long.'('.addslashes($this->location->address.', '.$this->location->city.', '.$this->location->state.', '.$this->location->zip.', '.$this->location->country).')' ;
$height = (int) $this->config->map_height ;
if (!$height) {
	$height = 600 ;
}
$height += 20 ;
$zoomLevel = (int) $this->config->zoom_level ;
if (!$zoomLevel) {
	$zoomLevel = 8 ;
}
$doc = JFactory::getDocument();
$protocol = JUri::getInstance()->getScheme();
$doc->addScript($protocol . '://maps.google.com/maps/api/js?sensor=true');
$doc->addScriptDeclaration('
	var geocoder, map;
	function initialize() {
		var latlng = new google.maps.LatLng("'.$this->location->lat.'", "'.$this->location->long.'");
		var options = {
			zoom: '.$zoomLevel.',
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("inline_map"), options);

		var marker = new google.maps.Marker({
			map: map,
			position: latlng,
		});
		var windowContent = "<h4>'.addslashes($this->location->name).'</h4>" +
			"<ul>" +
				"<li>'.$this->location->address . "  " . $this->location->city. "  " . $this->location->state."  " . $this->location->zip."  " . $this->location->country.'</li>" +
				"<li class=\'address getdirection\'><a href=\"'.$getDirectionLink.'\" target=\"_blank\">'.JText::_('EB_GET_DIRECTION').'</li>" +
			"</ul>";

		var infowindow = new google.maps.InfoWindow({
			content: windowContent,
			maxWidth: 250
		});

		google.maps.event.addListener(marker, "click", function() {
			infowindow.open(map,marker);
		});
		 infowindow.open(map,marker);
	}
	jQuery(document).ready(function () {
			initialize();
	});
');
?>
<div id="inline_map" style="height:<?php echo $height; ?>px; width:100%;"></div>