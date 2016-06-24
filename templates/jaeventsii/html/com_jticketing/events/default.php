<?php
/**
* @version		1.5.1 JTicketing $
* @package		JTicketing
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
$app    = JFactory::getApplication();
$filter_start_date     = $app->input->get('filter_start_date', '', 'string');
$filter_end_date       = $app->input->get('filter_end_date', '', 'string');
if($this->integration != 2 and $this->integration != 4)//NATIVE EVENT MANAGER
{
	?>
		<div class="alert alert-info alert-help-inline">
			<?php	echo JText::_('COMJTICKETING_INTEGRATION_NATIVE_NOTICE');	?>
		</div>
	<?php

	return false;
}

echo '<div id="fb-root"></div>';
$fblike_tweet = JUri::root().'components/com_jticketing/assets/js/fblike.js';
echo "<script type='text/javascript' src='".$fblike_tweet."'></script>";

if(JVERSION>=3.0)
{
	JHtml::_('bootstrap.tooltip');
}

$setdata=JRequest::get('get');

if(JVERSION >= '1.6.0'){
       $core_js = JUri::root().'media/system/js/core.js';
       $flg=0;
       $document=JFactory::getDocument();
       foreach($document->_scripts as $name=>$ar)
       {
               if($name == $core_js )
                       $flg=1;
       }
       if($flg==0)
				echo "<script type='text/javascript' src='".$core_js."'></script>";
}

$params=JComponentHelper::getParams('com_jticketing');
$show_field=0;
$max_donation_cnf=0;
$show_selected_fields=$params->get('show_selected_fields');
if($show_selected_fields)
{
	$creatorfield=$params->get('creatorfield');
	if(isset($creatorfield))
	foreach($creatorfield as $tmp)
	{
		switch($tmp)
		{
			case 'max_donation':
				$max_donation_cnf=1;
			break;

			case 'long_desc':
				$long_desc_cnf=1;
			break;

		}
	}
}
else
{
	$show_field=1;
}

?>
<?php if(JVERSION<3.0): ?>
<div class="techjoomla-bootstrap">
<?php endif;?>
	<form action="" method="post" name="adminForm" id="adminForm">
	<div id="all" class="row-fluid">
		<div class="span12">

			<!--page header-->
			<h2 class="componentheading">
				<?php echo JText::_('JT_ALL_CAMP');?>
			</h2>

			<div class="jticketing-filter">
					<input type="text" placeholder="<?php echo JText::_('COM_JTICKETING_ENTER_EVENTS_NAME'); ?>" name="search" id="search" value="<?php echo $srch = ($this->lists['search'])?$this->lists['search']:''; ?>" class="input-medium pull-left" onchange="document.adminForm.submit();" />
					<div class="btn-group jt-offset">
						<button type="button" onclick="this.form.submit();" class="btn btn-primary tip hasTooltip" data-original-title="Search"><i class="icon-search"></i></button>
						<button onclick="document.id('search').value='';this.form.submit();" type="button" class="btn btn-success tip hasTooltip" data-original-title="Clear"><i class="icon-remove"></i></button>
					</div>
				<?php if(JVERSION >= 3.0 ) { ?>

				<div class="btn-group pull-right">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<?php } ?>
			</div>
			
			<div class="row-fluid">
				<?php
				$eventsContainerClass = 'span12';

				if ($this->params->get('show_sorting_options') || $this->params->get('show_search_filter') || $this->params->get('show_filters') || $this->params->get('show_creator_filter') || $this->params->get('show_category_filter') || $this->params->get('show_location_filter'))
				{
					$eventsContainerClass = 'span9';
				}

				?>
				<?php

				$filterspath = $this->jticketingmainhelper->getViewpath('events', 'filters');

				//Get the filter html
				$html='';
				ob_start();
				include $filterspath;
				$html1=ob_get_contents();
				ob_end_clean();

				$filter_alignment=$params->get('filter_alignment');

				if (empty($filter_alignment))
				{
					$filter_alignment='right';

				}

				//Render filters html left side of pings
				if($filter_alignment=='left')
				{
					echo $html1;
				}
				 ?>
			<?php $random_container = 'jticket_pc_es_app_my_products';?>

				<div class="<?php echo $eventsContainerClass; ?>">
					<div id="jticket_pc_es_app_my_products" class="equal-height equal-height-child">
							<?php
								if($this->items)
								{
									$count =0;
									foreach($this->items as $eventdata)
									{
										$eventpinpath = $this->jticketingmainhelper->getViewpath('events', 'eventpin');

										$html='';
										ob_start();
										include $eventpinpath;
										$html=ob_get_contents();
										ob_end_clean();
										echo $html;
									?>

									<?php

									}//foreach

								}//if
								else
								{
								 ?>
										 <div class="alert alert-danger bs-alert-old-docs center">
											<?php echo JText::_('COM_JT_NOT_FOUND_EVENT');?>
										 </div>
										 <?php
								}
								?>
					</div>
					<!--jt_container -->
				</div>
				<!--span9 -->
				<?php
				//Render filters html right side of pings
				if($filter_alignment=='right')
				{
					echo $html1;
				}
				?>
			</div>
			<!--row-fluid-->

				<div class="row-fluid">
					<div class="span12">
						<?php if (JVERSION >= '3.0'): ?>
							<?php echo $this->pagination->getListFooter(); ?>
						<?php else: ?>
							<div class="pager">
								<?php echo $this->pagination->getListFooter(); ?>
							</div>
						<?php endif; ?>
					</div><!--span12-->
				</div><!--row-fluid-->

				<input type="hidden" name="option" value="com_jticketing" />
				<input type="hidden" name="view" value="events" />
				<input type="hidden" name="layout" value="default" />
				<input type="hidden" name="defaltevent" value="<?php echo $this->lists['filter_events_cat'];?>" />
			</form>
		</div>
		<!--span12-->
	</div>
	<!--row-fluid-->
<?php
if (JVERSION < '3.0'): ?>
	</div>
	<!--bootstrap-->
<?php endif; ?>
<?php

$pinsetuppath = $this->jticketingmainhelper->getViewpath('events', 'pinsetup');
ob_start();
include $pinsetuppath;
$html=ob_get_contents();
ob_end_clean();
echo $html;
?>
