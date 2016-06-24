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
if ($this->item->id)
{
    $coordinates = $this->item->lat.','.$this->item->long;
}
else
{
    $coordinates = '40.992954,29.042092';
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;				
		} else {
			//Should validate the information here
			if (form.name.value == "") {
				alert("<?php echo JText::_('EN_ENTER_LOCATION'); ?>");
				form.name.focus();
				return ;
			}					
			Joomla.submitform( pressbutton );
		}
	}
</script>
<script src="https://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
	var map;
	var geocoder;
	var marker;
	
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;				
		} else {
			//Should validate the information here
			if (form.name.value == "") {
				alert("<?php echo JText::_('EN_ENTER_LOCATION'); ?>");
				form.name.focus();
				return ;
			}					
			Joomla.submitform( pressbutton );
		}
	}
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapDiv = document.getElementById('map-canvas');
		// Create the map object
		map = new google.maps.Map(mapDiv, {
				center: new google.maps.LatLng(<?php  if(!empty($coordinates)){ echo $coordinates; } else { echo "40.992954,29.042092"; }?>),
				zoom: 10,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				streetViewControl: false
		});
		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php  if(!empty($coordinates)){ echo $coordinates; } else { echo "40.992954,29.042092"; }?>),
			draggable: true
		});
		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						document.getElementById('address').value = results[0].formatted_address;
						document.getElementById('coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}
	function getLocationFromAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				$('coordinates').value = results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7);
			} else {
				alert('We\'re sorry but your location was not found.');
			}
		});
	}
	// Initialize google map
	google.maps.event.addDomListener(window, 'load', initialize);
	// Search for addresses
	function getLocations(term) {
		var content = $('eventmaps_results');
		address = $('address').getSize();
		$('eventmaps_results').setStyle('width', address.x - 21);
		$('eventmaps_results').style.display = 'none';
		$$('#eventmaps_results li').each(function(el) {
			el.dispose();
		});
		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK') {
					results.each(function(item) {
						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});
						thea.addEvent('click', function() {
							$('address').value = item.formatted_address;
							$('coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
							var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(7), item.geometry.location.lng().toFixed(7));
							marker.setPosition(location);
							map.setCenter(location);
							$('eventmaps_results').style.display = 'none';
						});
						thea.inject(theli);
						theli.inject(content);
					});
					$('eventmaps_results').style.display = '';
				}
			});
		}
	}
	function clearLocations() {
		setTimeout( function () {
			$('eventmaps_results').style.display = 'none';
		},1000);
	}
</script>
<form action="index.php?option=com_eventbooking&view=location" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">	
	<div class="span5">
		<table class="admintable adminform">		
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_NAME'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" />
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_CREATED_BY'); ?>
				</td>
				<td>
					<?php echo EventbookingHelper::getUserInput($this->item->user_id) ; ?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>			
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_ADDRESS'); ?>
				</td>
				<td>
					<input class="input-xlarge" type="text" name="address" id="address" size="70" autocomplete="off" onkeyup="getLocations(this.value)" onblur="clearLocations();" maxlength="250" value="<?php echo $this->item->address;?>" />
					<ul id="eventmaps_results" style="display:none;"></ul>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>		
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_CITY'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="city" id="city" size="30" maxlength="250" value="<?php echo $this->item->city;?>" />
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_STATE'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="state" id="state" size="30" maxlength="250" value="<?php echo $this->item->state;?>" />
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_ZIP'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="zip" id="zip" size="20" maxlength="250" value="<?php echo $this->item->zip;?>" />
				</td>
				<td>
					&nbsp;
				</td>
			</tr>		
			<tr>
				<td class="key"> 
					<?php echo JText::_('EB_COUNTRY'); ?>
				</td>
				<td>
					<?php echo $this->lists['country'] ; ?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>	
			<tr>
                <td class="key">
                    <?php echo JText::_('EB_COORDINATES'); ?>
                </td>
                <td>
                    <input class="text_area" type="text" name="coordinates" id="coordinates" size="30" maxlength="250" value="<?php echo $this->item->lat.','.$this->item->long;?>" />
                </td>
            </tr>
			<?php
				if (JLanguageMultilang::isEnabled())
				{
				?>
					<tr>
						<td class="key">
							<?php echo JText::_('EB_LANGUAGE'); ?>
						</td>
						<td>
							<?php echo $this->lists['language'] ; ?>
						</td>
					</tr>
				<?php
				}
			?>
			<tr>
				<td class="key">
					<?php echo JText::_('EB_PUBLISHED') ; ?>
				</td>
				<td>
					<?php echo $this->lists['published']; ?>
				</td>	
			</tr>
		</table>
	</div>
	<div class="span7">
		<table class="admintable adminform" style="width:100%;">
			<tr>
				<td>
					<input type="button" onclick="getLocationFromAddress();" value="<?php echo JText::_('EB_PINPOINT'); ?> &raquo;" />
			        <br/><br/>
					<div id="map-canvas" style="width: 95%; height: 400px"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="clearfix"></div>
	</div>			
</div>		
<div class="clearfix"></div>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<style>
	#map-canvas img{
		max-width:none !important;
	}
</style>