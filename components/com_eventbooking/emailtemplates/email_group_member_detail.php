<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
?>
<table class="os_table" width="100%" cellspacing="3" cellpadding="3">
    <tr>
        <td class="title_cell">
            <?php echo  JText::_('EB_EVENT_TITLE') ?>
        </td>
        <td class="field_cell">
            <?php echo $rowEvent->title ; ?>
        </td>
    </tr>
    <?php
    if ($config->show_event_date) {
    ?>
        <tr>
            <td class="title_cell">
                <?php echo  JText::_('EB_EVENT_DATE') ?>
            </td>
            <td class="field_cell">
                <?php
                if ($rowEvent->event_date == EB_TBC_DATE)
                {
                    echo JText::_('EB_TBC');
                }
                else
                {
                    echo JHTML::_('date', $rowEvent->event_date, $config->event_date_format, $param) ;
                }
                ?>
            </td>
        </tr>
    <?php
    }
    if ($config->show_event_location_in_email && $rowLocation) 
	{
        $location = $rowLocation ;
        $locationInformation = array();
        if ($location->address)
        {
        	$locationInformation[] = $location->address;
        }
        if ($location->city)
        {
        	$locationInformation[] = $location->city;
        }
        if ($location->state)
        {
        	$locationInformation[] = $location->state;
        }
        if ($location->zip)
        {
        	$locationInformation[] = $location->zip;
        }
        if ($location->country)
        {
        	$locationInformation[] = $location->country;
        }
        ?>
        <tr>
            <td class="title_cell">
                <?php echo  JText::_('EB_LOCATION') ?>
            </td>
            <td class="field_cell">
                <?php echo $location->name.' ('.implode(', ', $locationInformation).')' ; ?>
            </td>
        </tr>
    <?php
    }
    $fields = $memberForm->getFields();
    foreach ($fields as $field)
    {
	    if ($field->hideOnDisplay)
	    {
		    continue;
	    }
    	echo $field->getOutput(false);
    }
    ?>    																																									
</table>
