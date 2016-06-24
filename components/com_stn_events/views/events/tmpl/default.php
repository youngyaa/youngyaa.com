<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::script(JURI::base() . 'components/com_stn_events/views/assets/moment.js');
JHtml::script(JURI::base() . 'components/com_stn_events/views/assets/jquery.flexisel.js');
//moment.js
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_stn_events');
$canEdit    = $user->authorise('core.edit', 'com_stn_events');
$canCheckin = $user->authorise('core.manage', 'com_stn_events');
$canChange  = $user->authorise('core.edit.state', 'com_stn_events');
$canDelete  = $user->authorise('core.delete', 'com_stn_events');
$user = JFactory::getUser();
$hide = 1;
date_default_timezone_set('Australia/Sydney')
?>
<link rel="stylesheet" href="<?php echo JURI::base(); ?>components/com_stn_events/views/assets/style.css" type="text/css" />
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("#flexiselDemo3").flexisel({
        visibleItems: 7,
        animationSpeed: 1000,
        autoPlay: true,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 2
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 3
            },
            tablet: { 
                changePoint:768,
                visibleItems: 6
            }
        }
    }); 
});
</script>

<div class="container2 t3-mainbody2">
	<div class="event_title">
		<h2><?php echo $this->eventDetails->title; ?></h2>
		<div class="timer"><span class="date">Date:</span>
			<p><span class="days"><?php echo date('dS',strtotime($this->eventDetails->startdate)); ?></span>
			<span class="days"><?php echo date('F',strtotime($this->eventDetails->startdate)); ?></span>
			<span class="year"><?php echo date('Y',strtotime($this->eventDetails->startdate)); ?></span></p>
			<span class="date_to">to</span>
			<p><span class="days"><?php echo date('dS',strtotime($this->eventDetails->enddate)); ?></span>
			<span class="days"><?php echo date('F',strtotime($this->eventDetails->enddate)); ?></span>
			<span class="year"><?php echo date('Y',strtotime($this->eventDetails->enddate)); ?></span></p>
		</div>
	</div>
	<?php if (!$user->guest ) { if(count($this->items) > 0){ ?>
	<div class="eventwreperfull">
	<div class="slider">
		<ul id="flexiselDemo3">
            <?php foreach ($this->items as $i => $item) : ?>
			<li data-startday="<?php echo $item->title; ?>" data-curday="<?php echo date('Y-m-d H:i:s'); ?>">
				<?php $currentday = ''; if(strtotime($item->title) == strtotime(date('Y-m-d'))){$currentday = 'activeEventDay';}?>
				<div class="slide_timer <?php echo $currentday; ?>" data-start="<?php echo date('H:i:s',strtotime($item->starttime)); ?>" data-eventid="<?php echo $item->id; ?>" data-endtim="<?php echo date('H:i:s',strtotime($item->endtime)); ?>" data-prize="<?php echo $item->prize; ?>">
					<span class="time_to"><?php echo date('dS F Y',strtotime($item->title)); ?></span>
                    <span><?php echo date('H:i:s',strtotime($item->starttime)); ?></span>
                    <span class="time_to">to</span>
                    <span><?php echo date('H:i:s',strtotime($item->endtime)); ?></span>
					<span class="prizeNameinCarasol"><?php echo $item->prize; ?></span>
                </div>
			</li>
            <?php endforeach; ?>
		</ul>
	</div>
	<div class="banner">
		<h2 class="prizenamevalue"></h2>		
		<span><img src="<?php echo JURI::base(); ?>images/stnevents/placeholder.png" alt="" /></span>
		<h6>This award is prorided by xxxx PTY. LTD</h6>
	</div>
	<div class="row mainbody_content">
		<div class="col-xs-12 col-sm-12 minbody_content">
			<div class="timer">
				<!--<h4>Light when counting</h4>-->
				<span class="time">Time</span>
				<p class="ongoingstartTimer"><span class="hours"><span>hour :</span>00</span><span class="minutes"><span>min :</span>00</span><span class="second"><span>sec :</span>00</span>
				</p>
			</div>
			<span><a class="grab_it" style="cursor:pointer;">Grab It Now!</a></span></div>
		
		<div class="col-xs-12 col-sm-12 numbring" style="margin-top: 35px;">
			<div class="mainbody_numbering">
				<h5>Rule</h5>
				<?php echo $this->eventDetails->rules; ?>
			</div>
		</div>
	</div>
	</div>
	<p class="noeventsToday" style="display:none;text-align:center;">"Sorry, today’s seckilling has all ended. Please come back tomorrow!"</p>
	<?php } else { ?>
		<P style="text-align: center;">No Seckilling Event Found</P>
	<?php } } else { ?>
	<P style="text-align: center;">Restricted area !!! Only registered user are allowed. So Please regiter and login to access this area.</P>
	<?php } ?>
</div>
<div class="resultPopUps">
	<div class="successtoaward">
		<h2>Congratulation! You successfully grab <span class="giftname"></span> on <span class="successgreabtime"></span></h2>
		<p>
			You will receive an e-mail about this, and will be contacted by our staff in 1-2 business days about receiving the award.
			<br>
			Please also feel free to contact Young Yaa PTY.LTD for information about the award on any business time.
			<br><br>
			<b>Young Yaa PTY.LTD</b>
			<br>
			<b>Opening Hours : </b>09:30 - 12:00, 13:30 - 17:00 (AEST), Monday - Friday<br>
			<b>Address : </b>911/301 George St, Sydney, NSW, Australia<br>
			<b>Phone : </b>+61 (02) 8065 1192<br>
			<b>Mail : </b><a href="mailto:info@youngyaa.com">info@youngyaa.com</a><br>
			<b>Website : </b><a href="https://www.youngyaa.com.au/">www.youngyaa.com.au</a><br>
		</p>		
		<p class="confirmbutton"><span>Confirm</span></p>
	</div>
	<div class="failtoaward">
		<p class="losertext">Sorry, you are a little slow. This award has aldready been grabbed. Please come back later and try again.</p>
		<p class="confirmbutton"><span>Confirm</span></p>
	</div>
</div>
<style>
.activeEvent, .activeEventStart{background: #000!important;}
.resultPopUps{
	background: rgba(255, 255, 255, 0.2) none repeat scroll 0 0;
	height: 100%;
	left: 0;
	position: fixed;
	text-align: center;
	top: 0;
	width: 100%;
	z-index: 99999;
	display: none;
}
.successtoaward, .failtoaward{
	display: none;
	background: #fff;
	max-width:400px;
	padding: 20px;
	border-radius:5px;
	margin: 20px auto;
	text-align: left;
	max-height:600px;
}
.prizeNameinCarasol {
  font-size: 14px !important;
  color: #6d850a!important;
  min-height: 75px;
  margin-top: 15px;
}
</style>
<script type="text/javascript">
	jQuery(document).ready(function () {
		checkCounterVal();
		/*setTimeout(function(){
			checkCounterVal();
		},1000);*/
		setInterval(function(){
			checkCounterVal();
		},1000);
		var now = new Date();
		var utc = new Date(now.getTime() + (now.getTimezoneOffset() * 60000) + (660 * 60000));
		var formattedwithsec= utc.getHours()+":"+(utc.getMinutes()<10?'0':'') + utc.getMinutes()+":"+(utc.getSeconds()<10?'0':'') + utc.getSeconds();
		jQuery('.grab_it').click(function(){
			var activeEvent = jQuery('.activeEvent').attr('data-eventid');
			if (activeEvent != '' && activeEvent != null){
			jQuery.ajax({
				url: "<?php echo JURI::BASE(); ?>index.php?option=com_stn_events&task=grabit",
				data: {"eventId":activeEvent},
				type:'POST',
				success: function(result){
					if (result != '' && result != null) {
						jQuery('.resultPopUps').show();
						if (result != 'alradyplay') {
							//jQuery('.resultPopUps').show();
							if (result == 0) {
							   jQuery('.giftname').text(jQuery('.prizenamevalue').text());
							   jQuery('.successgreabtime').text(formattedwithsec);
							   jQuery('.successtoaward').show();
							   jQuery('.failtoaward').hide();
							   jQuery.ajax({
									url: "<?php echo JURI::BASE(); ?>index.php?option=com_stn_events&task=mailawardWinner",
									data: {"eventId":activeEvent},
									type:'POST'
								});
							} else {
							   //jQuery('.losertext').text('Sorry, you are a little slow. Please come back later and try again.');
							   jQuery('.successtoaward').hide();
							   jQuery('.failtoaward').show();
							}
                        } else {
							//alert('Already Grabed');
							//jQuery('.losertext').text('Already Grabed');
							jQuery('.successtoaward').hide();
							jQuery('.failtoaward').show();losertext
						}
                    }
					/*setTimeout(function(){
						jQuery('.resultPopUps').hide();
					},20000);*/
				}
			});
            }
		});
		jQuery('.confirmbutton span').click(function(){
			jQuery('.resultPopUps').hide();
		});
	});
	function checkCounterVal() {
		var eventCheckFirst = '';
		var endtimeclsFinal = '';	
		var now = new Date();
		//var formatted = now.getHours()+":"+(now.getMinutes()<10?'0':'') + now.getMinutes();
		//var formattedwithsec = now.getHours()+":"+(now.getMinutes()<10?'0':'') + now.getMinutes()+":"+(now.getSeconds()<10?'0':'') + now.getSeconds();
		var utc = new Date(now.getTime() + (now.getTimezoneOffset() * 60000) + (660 * 60000));
		//var formatted = (utc.getHours()<10?'0':'') + utc.getHours() +":"+(utc.getMinutes()<10?'0':'') + utc.getMinutes();
		var formatted = utc.getHours()+":"+(utc.getMinutes()<10?'0':'') + utc.getMinutes()+":"+(utc.getSeconds()<10?'0':'') + utc.getSeconds();
		var formattedwithsec = utc.getHours()+":"+(utc.getMinutes()<10?'0':'') + utc.getMinutes()+":"+(utc.getSeconds()<10?'0':'') + utc.getSeconds();
		jQuery('.activeEventDay').each(function(e) {
			var startdatetime = jQuery(this).attr('data-start');
			var endtimecls = jQuery(this).attr('data-endtim');
			var eventId = jQuery(this).attr('data-eventid');
			//console.log(startdatetime);
			//console.log(formatted);
			//console.log(endtimecls);
			if(startdatetime <= formatted && endtimecls >= formatted){
				jQuery(this).addClass('activeEvent');
				eventCheckFirst = eventId;
				endtimeclsFinal = endtimecls+':00';
			} else {
				jQuery(this).removeClass('activeEvent');
			}
		});
		if (eventCheckFirst != '') {
			var timediff = timedifferencecalc(endtimeclsFinal,formattedwithsec);
			var parts = timediff.split(':');
			var timertext = '<span class="hours"><span>hour :</span>'+parts[0]+'</span><span class="minutes"><span>min :</span>'+parts[1]+'</span><span class="second"><span>sec :</span>'+parts[2]+'</span> To On-going';
			jQuery('.timer p.ongoingstartTimer').html(timertext);
			jQuery.ajax({
				url: "<?php echo JURI::BASE(); ?>index.php?option=com_stn_events&view=events&task=eventdetailbyid",
				data: {"eventId":eventCheckFirst},
				type:'POST',
				dataType:'json',
				success: function(result){
					if (result != '' && result != null) {
						if (result.prizeimage == '') {
							var imgpath = '<?php echo JURI::base(); ?>images/stnevents/placeholder.png';
							jQuery('.banner img').attr('src',imgpath);
						} else {
							var imgpath = '<?php echo JURI::base(); ?>images/stnevents/'+result.prizeimage;
							jQuery('.banner img').attr('src',imgpath);
						}
						var prizeProvider = 'This award is prorided by '+result.prizeprovider;
						jQuery('.banner h6').text(prizeProvider);
						jQuery('.prizenamevalue').text(result.prize);
					}
				}
			});
			jQuery('.grab_it').css({'opacity':'1','background':'#f00'});
		} else {
			jQuery('.grab_it').css({'opacity':'0.3','background':'#595959'});
			var starttimecompare = '';
			jQuery('.activeEventDay').each(function(e) {
				var startdatetime = jQuery(this).attr('data-start');
				var endtimecls = jQuery(this).attr('data-endtim');
				var eventId = jQuery(this).attr('data-eventid');
				if(startdatetime > formatted && (starttimecompare == '' || starttimecompare > startdatetime)){
					starttimecompare = startdatetime;
					eventCheckFirst = eventId;
					endtimeclsFinal = startdatetime;
				} else {
					jQuery(this).removeClass('activeEventStart');
				}
			});
			if (eventCheckFirst != '') {
				jQuery('.activeEventDay[data-eventid="'+eventCheckFirst+'"]').addClass('activeEventStart');
				var timediff = timedifferencecalc(endtimeclsFinal,formattedwithsec);
				var parts = timediff.split(':');
				var timertext = '<span class="hours"><span>hour :</span>'+parts[0]+'</span><span class="minutes"><span>min :</span>'+parts[1]+'</span><span class="second"><span>sec :</span>'+parts[2]+'</span> To Start';
				jQuery('.timer p.ongoingstartTimer').html(timertext);
				jQuery.ajax({
					url: "<?php echo JURI::BASE(); ?>index.php?option=com_stn_events&view=events&task=eventdetailbyid",
					data: {"eventId":eventCheckFirst},
					type:'POST',
					dataType:'json',
					success: function(result){
						if (result != '' && result != null) {
							if (result.prizeimage == '') {
								var imgpath = '<?php echo JURI::base(); ?>images/stnevents/placeholder.png';
								jQuery('.banner img').attr('src',imgpath);
							} else {
								var imgpath = '<?php echo JURI::base(); ?>images/stnevents/'+result.prizeimage;
								jQuery('.banner img').attr('src',imgpath);
							}
							var prizeProvider = 'This award is prorided by '+result.prizeprovider;
							jQuery('.banner h6').text(prizeProvider);
							jQuery('.prizenamevalue').text(result.prize);
						}
					}
				});
			}
			if (eventCheckFirst == ''){
               jQuery('.eventwreperfull').hide();
			   jQuery('.noeventsToday').show();			   
            } else {
			   jQuery('.eventwreperfull').show();
			   jQuery('.noeventsToday').hide();
			}
			//alert(eventCheckFirst);
		}
	}
	function timedifferencecalc(a,b) {
		var difference = Math.abs(toSeconds(a) - toSeconds(b));
		// format time differnece
		var result = [
			Math.floor(difference / 3600), // an hour has 3600 seconds
			Math.floor((difference % 3600) / 60), // a minute has 60 seconds
			difference % 60
		];
		// 0 padding and concatation
		result = result.map(function(v) {
			return v < 10 ? '0' + v : v;
		}).join(':');
		return result;
    }
	function toSeconds(time_str) {
		// Extract hours, minutes and seconds
		var parts = time_str.split(':');
		// compute  and return total seconds
		return parts[0] * 3600 + // an hour has 3600 seconds
		parts[1] * 60 + // a minute has 60 seconds
		+
		parts[2]; // seconds
	}
</script>

		<?php #echo JRoute::_('index.php?option=com_stn_events&view=events&task=eventdetailbyid', false); ?>