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
defined( '_JEXEC' ) or die;
JToolBarHelper::title(JText::_('EB_DASHBOARD'), 'generic.png');
?>
<table>
	<tr>
		<td>
			<div id="cpanel">
				<?php
					$this->quickiconButton('index.php?option=com_eventbooking&view=configuration', 'icon-48-eventbooking-config.png', JText::_('EB_CONFIGURATION'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=categories', 'icon-48-eventbooking-categories.png', JText::_('EB_CATEGORIES'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=events', 'icon-48-eventbooking-events.png', JText::_('EB_EVENTS'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=registrants', 'icon-48-eventbooking-registrants.png', JText::_('EB_REGISTRANTS'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=fields', 'icon-48-eventbooking-fields.png', JText::_('EB_CUSTOM_FIELDS'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=locations', 'icon-48-eventbooking-locations.png', JText::_('EB_LOCATIONS'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=coupons', 'icon-48-eventbooking-coupons.png', JText::_('EB_COUPONS'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=plugins', 'icon-48-eventbooking-payments.png', JText::_('EB_PAYMENT_PLUGINS'));					
					$this->quickiconButton('index.php?option=com_eventbooking&view=language', 'icon-48-eventbooking-language.png', JText::_('EB_TRANSLATION'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=message', 'icon-48-mail.png', JText::_('EB_EMAIL_MESSAGES'));
					$this->quickiconButton('index.php?option=com_eventbooking&task=csv_export', 'icon-48-eventbooking-export.png', JText::_('EB_EXPORT_REGISTRANTS'));					
					$this->quickiconButton('index.php?option=com_eventbooking&view=massmail', 'icon-48-eventbooking-massmail.png', JText::_('EB_MASS_MAIL'));
                    $this->quickiconButton('index.php?option=com_eventbooking&view=countries', 'icon-48-countries.png', JText::_('EB_COUNTRIES'));
					$this->quickiconButton('index.php?option=com_eventbooking&view=states', 'icon-48-states.png', JText::_('EB_STATES'));
					$this->quickiconButton('index.php?option=com_eventbooking', 'icon-48-download.png', JText::_('EB_UPDATE_CHECKING'), 'update-check');
				?>
			</div>
		</td>
		<td valign="top" width="55%">
			<?php
				echo JHtml::_('sliders.start', 'statistics_pane');
				echo JHtml::_('sliders.panel', JText::_('EB_UPCOMING_EVENTS'), 'upcoming_events');
				echo $this->loadTemplate('upcoming_events');
				echo JHtml::_('sliders.panel', JText::_('EB_LASTEST_REGISTRANGS'), 'registrants');
				echo $this->loadTemplate('registrants');
				echo JHtml::_('sliders.panel', JText::_('EB_USEFUL_LINKS'), 'links_panel');
				echo $this->loadTemplate('useful_links');
                echo JHtml::_('sliders.panel', JText::_('EB_CREDITS'), 'links_panel');
                echo $this->loadTemplate('credits');
				echo JHtml::_('sliders.end');
			?>
		</td>
	</tr>
</table>
<style>
	#statistics_pane
    {
		margin:0px !important
	}
</style>
<script type="text/javascript">
    var upToDateImg = '<?php echo JUri::base(true).'/components/com_eventbooking/assets/icons/icon-48-jupdate-uptodate.png' ?>';
    var updateFoundImg = '<?php echo JUri::base(true).'/components/com_eventbooking/assets/icons/icon-48-jupdate-updatefound.png';?>';
    var errorFoundImg = '<?php echo JUri::base(true).'/components/com_eventbooking/assets/icons/icon-48-deny.png';?>';
    jQuery(document).ready(function() {
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_eventbooking&task=check_update',
            dataType: 'json',
            success: function(msg, textStatus, xhr)
            {
                if (msg.status == 1)
                {
                    jQuery('#update-check').find('img').attr('src', upToDateImg).attr('title', msg.message);
                    jQuery('#update-check').find('span').text(msg.message);
                }
                else if (msg.status == 2)
                {
                    jQuery('#update-check').find('img').attr('src', updateFoundImg).attr('title', msg.message);
                    jQuery('#update-check').find('a').attr('href', 'http://joomdonation.com/my-downloads.html');
                    jQuery('#update-check').find('span').text(msg.message);
                }
                else
                {
                    jQuery('#update-check').find('img').attr('src', errorFoundImg);
                    jQuery('#update-check').find('span').text('<?php echo JText::_('EB_UPDATE_CHECKING_ERROR'); ?>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                jQuery('#update-check').find('img').attr('src', errorFoundImg);
                jQuery('#update-check').find('span').text('<?php echo JText::_('EB_UPDATE_CHECKING_ERROR'); ?>');
            }
        });
    });
</script>