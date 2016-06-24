<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="span3"><div class="jticketing-sidebar">
	<?php
	//$module = JModuleHelper::getModule('mod_jticketing_calendar');
	//$html = JModuleHelper::renderModule($module);
	//echo $html;
	?>

	<form action="" method="post" name="adminForm3" id="adminForm3">
		<input type="hidden" name="option" value="com_jticketing" />
		<input type="hidden" name="view" value="events" />
		<input type="hidden" name="layout" value="default" />

			<?php

			if ($this->params->get('show_filters') && $this->params->get('show_search_filter')) {
			} ?>

		<?php
			if($this->params->get('show_sorting_options') || $this->params->get('show_filters'))
			{
		?>
		<div class="jticketing-sidebar-block">
			<?php	if($this->params->get('show_sorting_options')) { ?>
			<h3><?php echo JText::_('COM_JTICKETING_ORDERING_OPTIONS');?></h3>

			<?php
				echo JHtml::_('select.genericlist', $this->ordering_options, "filter_order", ' size="1"
				onchange="this.form.submit();"
				class="input-medium jticketing_filter_width" name="filter_order"',"value", "text", $this->lists['filter_order']);
			?>
			<?php
				echo JHtml::_('select.genericlist', $this->ordering_direction_options, "filter_order_Dir", ' size="1"
				onchange="this.form.submit();" class="input-medium jticketing_filter_width" name="filter_order_Dir"',"value", "text", $this->lists['filter_order_Dir']);
			?>

			<?php } ?>
		</div>
		
		<?php 
				if($this->params->get('show_filters') and ($this->params->get('show_creator_filter') or $this->params->get('show_location_filter')))
				{
					?>
					<div class="jticketing-sidebar-block">
						<h3><?php echo JText::_('COM_JTICKETING_FILTER_EVENTS');?></h3>

						<?php
						$creator_filter_on=0;
						if($this->params->get('show_creator_filter'))
						{
							$creator_filter_on=1;
							echo JHtml::_('select.genericlist', $this->creator, "filter_creator", ' size="1"
							onchange="this.form.submit();" class="input-medium jticketing_filter_width" name="filter_creator"',"value", "text", $this->lists['filter_creator']);
						}
						else
						{
							$input=JFactory::getApplication()->input;
							$filter_creator=$input->get('filter_creator','','INT');
							if(!empty($filter_user))
							{
								$creator_filter_on=1;
								echo JHtml::_('select.genericlist', $this->creator, "filter_creator", ' size="1"
								onchange="this.form.submit();" class="input-medium jticketing_filter_width" name="filter_creator"',"value", "text", $this->lists['filter_creator']);
							}
						}
						?>
						<?php
						//organization_individual_type
						if($this->params->get('show_location_filter'))
						{
							 echo JHtml::_('select.genericlist', $this->location, "filter_location", 'class="input-medium jticketing_filter_width" size="1"
							onchange="this.form.submit();" name="filter_location"',"value", "text",$this->lists['filter_location']);


						}
						?>
						<!-- -Quick Search Filter-->
					</div>

					<div class="jticketing-sidebar-block">
						<h3><?php echo JText::_('COM_JTICKETING_EVENTS_TO_SHOW'); ?></h3>
						<ul class="com_jticketing_list_style_none">
							<?php
								
								$cat_url='index.php?option=com_jticketing&view=events&layout=default&events_to_show=&Itemid='.$this->singleEventItemid;
								$cat_url=JUri::root().substr(JRoute::_($cat_url),strlen(JUri::base(true))+1);

								if ($this->lists['events_to_show']=='')
								{
									echo ' <li><b><a href="'.$cat_url.'">'.JText::_('COM_JTICKETING_RESET_FILTER_TO_ALL').'</a></b></li>';
								}
								else
								{
									echo ' <li><a href="'.$cat_url.'">'.JText::_('COM_JTICKETING_RESET_FILTER_TO_ALL').'</a></li>';
								}


								for($i=1;$i<count($this->events_to_show);$i++)
								{
									$cat_url='index.php?option=com_jticketing&view=events&layout=default&events_to_show='.$this->events_to_show[$i]->value.'&Itemid='.$this->singleEventItemid;
									$cat_url=JUri::root().substr(JRoute::_($cat_url),strlen(JUri::base(true))+1);

									if($this->lists['events_to_show']==$this->events_to_show[$i]->value)
									{
										echo ' <li><b><a href="'.$cat_url.'">'. $this->events_to_show[$i]->text.'</a></b></li>';
									}
									else
									{
										echo ' <li><a href="'.$cat_url.'">'. $this->events_to_show[$i]->text.'</a></li>';
									}
								}
							?>
						</ul>
					<!-- -Quick Search Filter-->
					</div>
					<?php
					}
					?>		
	<?php	} ?>

			<!-- -Events Category Filter-->
			<?php
			//category
			if($this->params->get('show_category_filter'))
			{ ?>
			<div class="jticketing-sidebar-block">
				<h3><?php echo JText::_('COM_JTICKETING_FILTER_EVNT_CAT'); ?></h3>
				<ul class="com_jticketing_list_style_none">
					<?php
					
					$cat_url='index.php?option=com_jticketing&view=events&filter_events_cat=&Itemid='.$this->singleEventItemid;
					$cat_url=JUri::root().substr(JRoute::_($cat_url),strlen(JUri::base(true))+1);

					if($this->lists['filter_events_cat']=='')
						echo ' <li><b><a href="'.$cat_url.'">'.JText::_('COM_JTICKETING_RESET_FILTER_TO_ALL').'</a></b></li>';
					else
						echo ' <li><a href="'.$cat_url.'">'.JText::_('COM_JTICKETING_RESET_FILTER_TO_ALL').'</a></li>';

					for($i=1;$i<count($this->cat_options);$i++)
					{
						$cat_url='index.php?option=com_jticketing&view=events&filter_events_cat='.$this->cat_options[$i]->value.'&Itemid='.$this->singleEventItemid;
						$cat_url=JUri::root().substr(JRoute::_($cat_url),strlen(JUri::base(true))+1);

						if($this->lists['filter_events_cat']==$this->cat_options[$i]->value)
							echo ' <li><b><a href="'.$cat_url.'">'. $this->cat_options[$i]->text.'</a></b></li>';
						else
							echo ' <li><a href="'.$cat_url.'">'. $this->cat_options[$i]->text.'</a></li>';
					}
					?>
				</ul>
			</div>
			<?php }?>
			<!-- -events Category Filter-->

	</form>
</div></div>