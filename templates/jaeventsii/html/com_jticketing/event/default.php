<?php
/**
 * @version     1.5
 * @package     com_jticketing
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <extensions@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$params = JComponentHelper::getParams( 'com_jticketing' );
$order_currency =$params->get('currency');
$jticketingmainhelper = new jticketingmainhelper();
$app  = JFactory::getApplication();
//Find Langitude Latitude of Location
$address = str_replace(" ", "+", $this->item->location);
$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=true";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);
$response_a = json_decode($response);
$lat = $response_a->results[0]->geometry->location->lat;
$long = $response_a->results[0]->geometry->location->lng;

$st_datetime = new DateTime($this->item->startdate);
$ISO_startdate = $st_datetime->format(DateTime::ISO8601);

$imagePath = 'media/com_jticketing/images/';
$imagePath = JRoute::_(JUri::base() . $imagePath . $this->item->image, false);
$event_url='index.php?option=com_jticketing&view=event&id='.$this->item->id;
$event_url=JUri::root().substr(JRoute::_($event_url),strlen(JUri::base(true))+1);

$this->jt_params     = $app->getParams('com_jticketing');
$this->enable_self_enrollment     =  $this->jt_params->get('enable_self_enrollment', '', 'INT');
$this->supress_buy_button     =  $this->jt_params->get('supress_buy_button', '', 'INT');
$this->accesslevels_for_enrollment     =  $this->jt_params->get('accesslevels_for_enrollment');
$user   = JFactory::getUser();
$groups = $user->getAuthorisedViewLevels();
$guest  = $user->get('guest');
$allow_access_level_enrollment = 0;

// Check access levels for enrollment
foreach ($groups as $group)
{
	if (in_array($group, $this->accesslevels_for_enrollment))
	{
		$allow_access_level_enrollment = 1;
		break;
	}
}

if($this->integration != 2)//NATIVE EVENT MANAGER
{
	?>
		<div class="alert alert-info alert-help-inline">
			<?php	echo JText::_('COMJTICKETING_INTEGRATION_NATIVE_NOTICE');	?>
		</div>
	<?php

	return false;
}

$this->item->short_description=trim($this->item->short_description);

?>

<?php if(JVERSION < '3.0'): ?>
<div class="techjoomla-bootstrap">
<?php endif; ?>
	<div class="jticketing-event-detail" itemscope itemtype="http://schema.org/Event">
	<div class="page-header" itemprop="name">
		<h1><?php echo $this->escape($this->item->title); ?></h1>
	</div>

<?php

	//Integration with Jlike
	if(file_exists(JPATH_SITE.'/'.'components/com_jlike/helper.php'))
	{
		$show_comments = -1;
		$show_like_buttons = 1;

		$JTicketingIntegrationsHelper =new JTicketingIntegrationsHelper();
		$jlikehtml=$JTicketingIntegrationsHelper->DisplayjlikeButton($event_url, $this->item->id, $this->escape($this->item->title), $show_comments, $show_like_buttons);
		if($jlikehtml)
		echo $jlikehtml;
	}
	//Integration with Jlike

?>

		<strong><?php echo JText::_('COM_JTICKETING_EVENT_CATEGORY') . ' : ';?></strong>
		<i><?php
		if(isset($this->item->category_id_title))
		{
			echo $this->item->category_id_title;
		}
		else
		{
			echo JText::_("COM_JTICKETING_UNDEFINED");
		}
		?></i>



	<div class="row">
		<div class="">

					<?php
					echo '<div id="fb-root"></div>';
					$fblike_tweet = JUri::root().'components/com_jticketing/assets/js/fblike.js';
					echo "<script type='text/javascript' src='".$fblike_tweet."'></script>";

					// set metadata
					$config=JFactory::getConfig();
					if(JVERSION>=3.0)
						$site_name=$config->get( 'sitename' );
					else
						$site_name=$config->getvalue( 'config.sitename' );

					$document->addCustomTag( '<meta property="og:title" content="'.$this->escape($this->item->title).'" />' );
					$document->addCustomTag( '<meta property="og:image" content="'.$imagePath.'" />' );
					$document->addCustomTag( '<meta property="og:url" content="'.$event_url.'" />' );
					$document->addCustomTag( '<meta property="og:description" content="'.nl2br($this->escape($this->item->short_description)).'" />' );
					$document->addCustomTag( '<meta property="og:site_name" content="'.$site_name.'" />' );
					$document->addCustomTag( '<meta property="og:type" content="event" />' );
					$pid=$params->get('addthis_publishid','GET','STRING');
					if($params->get('social_sharing'))
					{
						if($params->get('social_shring_type')=='addthis')
						{
							$add_this_share='
							<!-- AddThis Button BEGIN -->
							<div class="addthis_toolbox addthis_default_style">

							<a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="'.$event_url.'"></a>
							<a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="'.$event_url.'"></a>
							<a class="addthis_button_tweet" class="addthis_button" addthis:url="'.$event_url.'"></a>
							<a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="'.$event_url.'"></a>
							<a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="'.$event_url.'"></a>
							</div>
							<script type="text/javascript">
								var addthis_config ={ pubid: "'.$pid.'"};
							</script>
							<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="'.$pid.'"></script>
							<!-- AddThis Button END -->' ;

							$add_this_js='http://s7.addthis.com/js/300/addthis_widget.js';
							$JTicketingIntegrationsHelper=new JTicketingIntegrationsHelper();
							$JTicketingIntegrationsHelper->loadScriptOnce($add_this_js);
							//output all social sharing buttons
							echo' <div id="rr" style="">
								<div class="social_share_container">
								<div class="social_share_container_inner">'.
									$add_this_share.
								'</div>
							</div>
							</div>
							';
						}
						else
						{
							echo '<div class="com_jticketing_horizontal_social_buttons">';
							echo '<div class="com_jticketing_float_left">
									<div class="fb-like" data-href="'.$event_url.'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true"></div>
								</div>';
							echo '<div class="com_jticketing_float_left">
									&nbsp; <div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.$event_url.'"></div>
								</div>';
							echo '<div class="com_jticketing_float_left">
									&nbsp; <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$event_url.'" data-counturl="'.$event_url.'"  data-lang="en">Tweet</a>
								</div>';
							echo '</div>
								<div class="com_jticketing_clear_both"></div>';
						}
					}
				?>
		</div>
	</div>

	<?php if (JVERSION >= '3.0') :?>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JTICKETING_EVENT_TAB_DETAILS', true));?>
	<?php else:?>

		<ul id="myTab" class="nav nav-tabs">
			<li class="active">
				<a href="#details" data-toggle="tab">
					<?php echo JText::_('COM_JTICKETING_EVENT_TAB_DETAILS');?>
				</a>
			</li>
			<li>
				<a href="#extrafields" data-toggle="tab">
					<?php echo JText::_('COM_JTICKETING_EVENT_TAB_EXTRA_FIELDS');?>
				</a>
			</li>
		</ul>

		<div id="myTabContent" class="tab-content">
			<div class="tab-pane fade in active" id="details">
	<?php endif;?>

			<div class="row">
				<div class="span8">
					<div class="jticketing-event-image" >
						<img itemprop="image" class="img-polaroid com_jticketing_image_w98pc" src="<?php echo $imagePath;?>">
					</div>

					<div class="jticketing-event-desc" >
						<span class="com_jticketing_hidden_span" itemprop="description">
						<?php echo $this->item->short_description; ?>
						</span>
						<?php echo $this->item->long_description; ?>
					</div>
				</div>

				<div class="span4">
					<?php
						//load location template
						echo $this->loadTemplate('location');
					 ?>


					<div class="well">
						<dl>
							<dd>
								<strong><?php echo JText::_('COM_JTICKETING_EVENT_STARTDATE') . ' : ';?></strong>
								<?php echo JFactory::getDate($this->item->startdate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM')); ?>
							</dd>

							<dd>
								<strong><?php echo JText::_('COM_JTICKETING_EVENT_ENDDATE') . ' : ';?></strong>
								<?php echo JFactory::getDate($this->item->enddate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_AMPM')); ?>
							</dd>

							<dd>
								<strong><?php echo JText::_('COM_JTICKETING_EVENT_BOOKING_START_DATE') . ' : ';?></strong>
								<?php echo JFactory::getDate($this->item->booking_start_date)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT')); ?>
							</dd>
							<dd>
								<strong><?php echo JText::_('COM_JTICKETING_EVENT_BOOKING_END_DATE') . ' : ';?></strong>
								<?php echo JFactory::getDate($this->item->booking_end_date)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT')); ?>
							</dd>
						</dl>
					</div>
					<!--added by aniket-->



					<?php if( (is_array($this->GetTicketTypes)) && (count($this->GetTicketTypes))): ?>
						<div class="well">

								<table class="table-responsive">
									<tr>
										<td><strong><?php echo JText::_('COM_JTICKETING_TICKET_TYPE_TITLE');	?></strong></td>
										<td><strong><?php echo JText::_('COM_JTICKETING_TICKET_TYPE_PRICE');	?></strong></td>
										<td><strong><?php echo JText::_('COM_JTICKETING_TICKET_TYPE_AVAILABLE');	?></strong></td>
									</tr>
									<?php

									foreach($this->GetTicketTypes as $tickettypes)
									{
										if ($tickettypes->hide_ticket_type or ($tickettypes->unlimited_seats!=1 and $tickettypes->count<=0))
										{
											continue;
										}

										?>

										<tr>
											<td>
												<?php echo $tickettypes->title; ?>
											</td>

											<td>
												<?php
												echo $jticketingmainhelper->getFromattedPrice( number_format(($tickettypes->price),2),$order_currency);
												?>
											</td>

											<td>
												<?php
												if($tickettypes->unlimited_seats)
												{
													echo JText::_('COM_JTICKETING_BUY_UNLIM_SEATS');
												}
												else
												{
													echo $tickettypes->count.'/'.$tickettypes->available;
												}
												?>
											</td>

										</tr>

									<?php
									}
									?>

								</table>
								<?php

								if($this->showbuybutton)
								{
									if ($this->enable_self_enrollment == 1 and $allow_access_level_enrollment == 1)
									{
										if(JFactory::getUser()->authorise('core.enroll','com_jticketing.event.'.$this->item->id))
										{
									?>
										<a
										href="<?php echo JRoute::_('index.php?option=com_jticketing&task=orders.bookTicket&eventid='.$this->item->id.'&Itemid='.$this->buyTicketItemId,false);?>"
										class="btn btn-success com_jticketing_button">
											<?php echo JText::_('COM_JTICKETING_ENROLL_BUTTON'); ?>
										</a>

									<?php
										}
									}

									if ($this->enable_self_enrollment == 0 or ($this->enable_self_enrollment == 1 and $this->supress_buy_button==0))
									{
									?>
										<a
										href="<?php echo JRoute::_('index.php?option=com_jticketing&view=buy&layout=default&eventid='.$this->item->id.'&Itemid='.$this->buyTicketItemId,false);?>"
										class="btn btn-success com_jticketing_button">
											<?php echo JText::_('COM_JTICKETING_BUY_BUTTON'); ?>
										</a>
									<?php
									}
									?>
								<?php
								}
							?>

						</div>
					<?php endif;?>

				</div>
			</div>

		<?php if (JVERSION >= '3.0') :?>
			<?php echo JHtml::_('bootstrap.endTab');?>
		<?php else:?>
			</div>
		<?php endif;?>

		<?php if ($this->extraData) :
			if (JVERSION >= '3.0') :
					echo JHtml::_('bootstrap.addTab', 'myTab', 'extrafields', JText::_('COM_JTICKETING_EVENT_TAB_EXTRA_FIELDS', true));
				else:?>
					<div class="tab-pane fade in" id="extrafields">
			<?php endif;?>
						<?php echo $this->loadTemplate('extrafields');?>

			<?php if (JVERSION >= '3.0') :?>
				<?php echo JHtml::_('bootstrap.endTab');?>
			<?php else:?>
				</div>
			<?php
			endif;
		endif;?>

	<?php if (JVERSION >= '3.0') :?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	<?php else:?>
		</div>
	<?php endif;?>
</div>
<?php if(JVERSION < '3.0'): ?>
</div>
<?php endif; ?>

<?php
	//Integration with Jlike
	if(file_exists(JPATH_SITE.'/'.'components/com_jlike/helper.php'))
	{
		$show_comments = 1;
		$show_like_buttons = 0;
		$JTicketingIntegrationsHelper =new JTicketingIntegrationsHelper();
		$jlikehtml=$JTicketingIntegrationsHelper->DisplayjlikeButton($event_url, $this->item->id, $this->escape($this->item->title), $show_comments, $show_like_buttons);
		if($jlikehtml)
		echo $jlikehtml;
	}
	//Integration with Jlike
?>
