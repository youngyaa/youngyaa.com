<?php
/**
* @package    jticketing
* @author     Techjoomla
* @copyright  Copyright 2012 - Techjoomla
* @license    http://www.gnu.org/licenses/gpl-3.0.html
**/
jimport( 'joomla.application.module.helper');
$document=JFactory::getDocument();
//no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document->addStyleSheet(JUri::root().'components/com_jticketing/assets/css/jticketing.css');

require_once(JPATH_ROOT.DS."components".DS."com_jticketing".DS."helpers".DS."frontendhelper.php");
$frontendhelper=new jticketingfrontendhelper();
JLoader::import('main', JPATH_SITE.DS.'components'.DS.'com_jticketing'.DS.'helpers');
$MainHelper=new jticketingmainhelper();
$singleEventItemid=$MainHelper->getItemId('index.php?option=com_jticketing&view=events&layout=all',1);
$buyTicketItemId = $MainHelper->getItemId('index.php?option=com_jticketing&view=buy&layout=default',1);
//@model helper object
$modJTicketingHelper=new modJTicketingHelper();
$featured_camp=$params->get('featured_camp');
$data=$modJTicketingHelper->getData();
$moduleParams = json_decode($module->params);
$show_ticket_types=$moduleParams->ticket_type;

$modparams = new JRegistry($module->params);

$tjClass = '';

if (JVERSION < '3.0')
{
	$tjClass = 'jticketing ';
}
?>

<?php if($module->showtitle || $modparams->get('module-intro') || $count > 3): ?>
<div class="section-header text-left">
  <?php if($module->showtitle): ?>
  <h3 class="section-title ">  <span><?php echo $module->title ?></span> </h3>
  <?php endif; ?>

  <?php if($modparams->get('module-intro')): ?>
  <div class="module-intro">
    <?php echo $modparams->get('module-intro') ?>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div id="mod-<?php echo $module->id ?>" class="jticketing-events <?php echo $tjClass.$params->get('moduleclass_sfx'); ?>">

	<?php
	$arraycnt=count($data); 
  $no_of_event_show = $params->get('no_of_event_show');
  $numberItem = $no_of_event_show < $arraycnt ? $no_of_event_show : $arraycnt;

	for($i=0;$i<$arraycnt;$i++) { ?>
		<div class="item"><div class="inner">
			<?php
			 if(!empty($moduleParams->image)) {
			?>
			<div class="jticketing-event-image">
				<!-- Image-->
					<a href="<?php echo JUri::root().substr(JRoute::_('index.php?option=com_jticketing&view=event&id='.$data[$i]['event']->id.'&Itemid='.$singleEventItemid),strlen(JUri::base(true))+1);?>">
						<?php
						$imagePath = 'media/com_jticketing/images/';
						$imagePath = JRoute::_(JUri::base() . $imagePath . $data[$i]['event']->image, false);
						?>
						<img alt="200x150"  class="jticketing_thmb_style" src="<?php echo $imagePath; ?>">
					</a>
      </div>
			<?php
			 }
			?>

			<div class="jticketing-event-desc">
        <h4 class="jticketing-event-title">
					<a href="<?php echo JRoute::_('index.php?option=com_jticketing&view=event&id='.$data[$i]['event']->id.'&Itemid='.$singleEventItemid,false);?>">
						<?php echo $data[$i]['event']->title; ?>
					</a>
        </h4>
        <?php 
          //Event Description
          if(!empty($moduleParams->show_description)) {
            if(strlen($data[$i]['event']->short_description)>=50)
            echo '<p>'.substr($data[$i]['event']->short_description,0,50).'...</p>';
            else
            echo '<p>'.$data[$i]['event']->short_description.'</p>';
          }
        ?>
				
        <?php if($show_ticket_types == 1) { ?>
          <div class="ticket-table">

          <?php
            if($data[$i]['ticket_types'])
            {
              ?>
              <table class="table table-bordered">
                <tr>
                  <th>
                  <?php echo JText::_('MOD_JTICKETING_EVENT_TYPE');?>
                  </th>
                  <th>
                  <?php echo JText::_('MOD_JTICKETING_EVENT_PRICE');?>
                  </th>
                  <th>
                  <?php echo JText::_('MOD_JTICKETING_EVENT_NO_SEATS');?>
                  </th>
                </tr>
              <?php
                $ticket_type = $data[$i]['ticket_types'];
                for($j=0;$j<count($ticket_type);$j++)
                {
                ?>
                <tr>
                  <td>
                    <?php

                    if($ticket_type[$j]->title)
                    {
                      echo $ticket_type[$j]->title;
                    }
                    else
                    {
                      echo "-";
                    }

                    ?>
                  </td>
                  <td>
                    <?php

                      if($ticket_type[$j]->price)
                      {
                        echo $frontendhelper->getFromattedPrice($ticket_type[$j]->price);
                      }
                      else
                      {
                        echo "-";
                      }

                    ?>
                  </td>
                  <td>
                  <?php
                    if($ticket_type[$j]->count)
                    {
                      echo $ticket_type[$j]->count;
                    }
                    else
                    {
                      echo "-";
                    }
                  ?>
                  </td>
                </tr>

                <?php
                }?>
              </table>
              <?php
              }

          ?>
          </div>
        <?php  } ?>

        <dl>
          <?php if(!empty($moduleParams->show_event_start_end_date)) { ?>
          <dd>
            <strong><?php echo JText::_('MOD_JTICKETING_EVENT_EVENT_STARTDATE') . ': ';?></strong>
            <?php echo JFactory::getDate($data[$i]['event']->startdate)->Format(JText::_('MOD_JTICKETING_EVENT_DATE_FORMAT_SHOW_AMPM')); ?>
          </dd>

          <dd>
            <strong><?php echo JText::_('MOD_JTICKETING_EVENT_EVENT_ENDDATE') . ': ';?></strong>
            <?php echo JFactory::getDate($data[$i]['event']->enddate)->Format(JText::_('MOD_JTICKETING_EVENT_DATE_FORMAT_SHOW_AMPM')); ?>
          </dd>
          <?php } ?>

          <?php if(!empty($moduleParams->show_booking_start_end_date)) { ?>
          <dd>
            <strong><?php echo JText::_('MOD_JTICKETING_EVENT_EVENT_BOOKING_START_DATE') . ': ';?></strong>
            <?php echo JFactory::getDate($data[$i]['event']->booking_start_date)->Format(JText::_('MOD_JTICKETING_EVENT_DATE_FORMAT_SHOW_SHORT')); ?>
          </dd>

          <dd>
            <strong><?php echo JText::_('MOD_JTICKETING_EVENT_EVENT_BOOKING_END_DATE') . ': ';?></strong>
            <?php echo JFactory::getDate($data[$i]['event']->booking_end_date)->Format(JText::_('MOD_JTICKETING_EVENT_DATE_FORMAT_SHOW_SHORT')); ?>
          </dd>

          <?php } ?>

          <dd class="jticketing-event-location">
            <?php if (JVERSION>=3.0){?>
            <i class="icon icon-location"></i>
            <?php } else { ?>
            <i class="icon-map-marker"></i>
            <?php }
            //Event Location
             echo $data[$i]['event']->location; ?>
          </dd>
          
        </dl>

        <?php
          $jticketingmainhelper = new jticketingmainhelper;
          $showbuybutton        = $jticketingmainhelper->showbuybutton($data[$i]['event']->id);

          if($showbuybutton)
          {
            ?>
            <a
            href="<?php echo JRoute::_('index.php?option=com_jticketing&view=buy&layout=default&eventid='.$data[$i]['event']->id.'&Itemid='.$buyTicketItemId, false);?>"
            class="btn btn-primary">
              <?php echo JText::_('TPL_MOD_JTICKETING_EVENT_BUY_BUTTON'); ?>
            </a>
          <?php
          }
        ?>
			</div>
			<?php $k = count($data) -1; ?>
    </div></div>
	<?php } ?>
</div>

<script>
    (function ($) {
      $(document).ready(function(){ 
        $(".t3-section .jticketing-events#mod-<?php echo $module->id ?>").owlCarousel({
          navigation : true,
          pagination: false,
          items: <?php echo $numberItem; ?>,
          loop: false,
          scrollPerPage : true,
          autoHeight: false,
          navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
          itemsDesktop : [1199, 3],
          itemsDesktopSmall : [979, 2],
          itemsTablet : [768, 2],
          itemsTabletSmall : [600, 2],
          itemsMobile : [479, 1]
        });
      });
    })(jQuery);
    </script>