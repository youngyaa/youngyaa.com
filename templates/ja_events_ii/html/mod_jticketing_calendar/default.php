<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$root_url = JUri::base();
//$document->addStyleSheet($root_url . 'components/com_jticketing/assets/calendars/components/bootstrap2/css/bootstrap-theme.min.css');
//$document->addStyleSheet($root_url . 'components/com_jticketing/assets/calendars/css/calendar.css');
$document->addStyleSheet($root_url . 'modules/mod_jticketing_calendar/assets/css/calendar.css');
$document->addStyleSheet($root_url . 'components/com_jticketing/assets/font-awesome-4.1.0/css/font-awesome.min.css');
?>
<div class="jtcalendarForm">
<form method="post" name="jtcalendarForm" id="jtcalendarForm">


<!--
<div class="row">
<div class="pull-right form-inline">
<?php
if($state)
$filter = $state->get('filter.filter_evntCategory');
else
$filter = '';
$class = 'class="form-control input-medium" size="1" onchange="document.jtcalendarForm.submit();" name="filter_evntCategory"';
	//echo JHtml::_('select.genericlist', $cat_options, "filter_evntCategory", $class, "value", "text", $filter);
?>
</div>
</div>
-->

<div class="row-fluid">
<div class="span12">
	<div class="">
		<div class="form-inline"></div>
		<div class="btn-group">
			<span class="input-group-btn pull-left">
			<button class="btn btn-info  btn-small-jt" id="pre_year_button" data-calendar-nav="prev-year"><i class="fa fa-backward"></i></button>
			<button class="btn btn-info btn-small-jt" id="pre_button" data-calendar-nav="prev">
			<i class="fa fa-chevron-left"></i></button>
			<div class="btn btn-info" id="month_button" data-calendar-nav=""><span id="month_text"></span></div>
			<button class="btn btn-info btn-small-jt" id="nex_button" data-calendar-nav="next">
			<i class="fa fa-chevron-right"></i></button>
			<button class="btn btn-info btn-small-jt" id="nex_year_button" data-calendar-nav="next-year">
			<i class="fa fa-forward"></i></button>
			</div>
			</span>
	</div>
</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div id="calendar"></div>
	</div>
</div>


<?php
$app    = JFactory::getApplication();
$path   = JURI::base(true).'/templates/'.$app->getTemplate().'/';
$document->addScript($root_url . 'components/com_jticketing/assets/calendars/components/underscore/underscore-min.js');
$document->addScript($root_url . 'components/com_jticketing/assets/calendars/components/jstimezonedetect/jstz.min.js');
$document->addScript($root_url . 'components/com_jticketing/assets/calendars/js/calendar.js');
$document->addScript($path . 'js/modules/mod_jticketing_calendar/assets/js/app.js');
?>
<input type="hidden" name="template_path_calendar" id="template_path_calendar" value="<?php echo $root_url;?>modules/mod_jticketing_calendar/assets/tmpls/">
<script type="text/javascript">
jQuery("#jtcalendarForm").submit(function(e)
{
	e.preventDefault();
});
</script>
</form>
</div>