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
$editor = JFactory::getEditor() ;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$format = 'Y-m-d';
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
?>
<style>
	.calendar {
		vertical-align: bottom;
	}
</style>
<div class="row-fuid">
<form action="index.php?option=com_eventbooking&view=event" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
<ul class="nav nav-tabs">
	<li class="active"><a href="#basic-information-page" data-toggle="tab"><?php echo JText::_('EB_BASIC_INFORMATION');?></a></li>
	<li><a href="#discount-page" data-toggle="tab"><?php echo JText::_('EB_DISCOUNT_SETTING');?></a></li>				
	<?php 
		if ($this->config->event_custom_field) {
		?>
			<li><a href="#extra-information-page" data-toggle="tab"><?php echo JText::_('EB_EXTRA_INFORMATION');?></a></li>
		<?php	
		}
	    if ($translatable)
	    {
		?>
		    <li><a href="#translation-page" data-toggle="tab"><?php echo JText::_('EB_TRANSLATION'); ?></a></li>
		<?php
	    }
	?>
	<li><a href="#advance-settings-page" data-toggle="tab"><?php echo JText::_('EB_ADVANCED_SETTINGS');?></a></li>
	<?php
		// Plugins
		if (count($this->plugins)) {
			$count = 0 ;
			foreach ($this->plugins as $plugin) {
				$title  = $plugin['title'] ;
				$count++ ;
			?>
				<li><a href="#<?php echo 'tab_'.$count;  ?>" data-toggle="tab"><?php echo $title;?></a></li>
			<?php							
			}
		}


	?>			
</ul>
<div class="tab-content">			
	<div class="tab-pane active" id="basic-information-page">
		<div class="row-fluid">
			<div class="span8">
				<fieldset class="adminform">
					<legend><?php echo JText::_('EB_EVENT_DETAIL');?></legend>
					<table class="admintable" width="100%">
						<tr>
							<td class="key"><?php echo JText::_('EB_TITLE') ; ?></td>
							<td>
								<input type="text" name="title" value="<?php echo $this->item->title; ?>" class="input-xlarge" size="70" />
							</td>
						</tr>	
						<tr>
							<td class="key"><?php echo JText::_('EB_ALIAS') ; ?></td>
							<td>
								<input type="text" name="alias" value="<?php echo $this->item->alias; ?>" class="input-xlarge" size="70" />
							</td>
						</tr>					
						<tr>
							<td class="key"><?php echo JText::_('EB_CREATED_BY') ; ?></td>
							<td>
								<?php echo $this->lists['created_by'] ; ?>
							</td>
						</tr>
						<tr>
							<td class="key" valign="top"><?php echo JText::_('EB_MAIN_EVENT_CATEGORY') ; ?></td>
							<td>
								<div style="float: left;"><?php echo $this->lists['main_category_id'] ; ?></div>								
							</td>
						</tr>
						<tr>
							<td class="key" valign="top"><?php echo JText::_('EB_ADDITIONAL_CATEGORIES') ; ?></td>
							<td>
								<div style="float: left;"><?php echo $this->lists['category_id'] ; ?></div>
								<div style="float: left; padding-top: 25px; padding-left: 10px;">Press <strong>Ctrl</strong> to select multiple categories</div>
							</td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EB_THUMB_IMAGE') ; ?></td>
							<td>
								<input type="file" class="inputbox" name="thumb_image" size="60" />
								<?php
									if ($this->item->thumb) 
									{
									?>
										<a href="<?php echo JURI::root().'media/com_eventbooking/images/'.$this->item->thumb; ?>" class="modal"><img src="<?php echo JURI::root().'media/com_eventbooking/images/thumbs/'.$this->item->thumb; ?>" class="img_preview" /></a>
										<input type="checkbox" name="del_thumb" value="1" /><?php echo JText::_('EB_DELETE_CURRENT_THUMB'); ?>
									<?php	
									}
								?>
							</td>
						</tr>	
						<tr>
							<td class="key"><?php echo JText::_('EB_LOCATION') ; ?></td>
							<td>
								<?php echo $this->lists['location_id'] ; ?>
							</td>
						</tr>					
						<tr>
							<td class="key">
								<?php echo JText::_('EB_EVENT_START_DATE'); ?>
							</td>				
							<td>					
								<?php echo JHtml::_('calendar', ($this->item->event_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_date, $format, null), 'event_date', 'event_date') ; ?>
								<?php echo $this->lists['event_date_hour'].' '.$this->lists['event_date_minute']; ?>					
							</td>
						</tr>		
						<tr>
							<td class="key">
								<?php echo JText::_('EB_EVENT_END_DATE'); ?>
							</td>				
							<td>					
								<?php echo JHtml::_('calendar', ($this->item->event_end_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_end_date, $format, null), 'event_end_date', 'event_end_date') ; ?>
								<?php echo $this->lists['event_end_date_hour'].' '.$this->lists['event_end_date_minute']; ?>					
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
							</td>
							<td>
								<?php echo JHtml::_('calendar', ($this->item->registration_start_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->registration_start_date, $format, null), 'registration_start_date', 'registration_start_date') ; ?>
								<?php echo $this->lists['registration_start_hour'].' '.$this->lists['registration_start_minute']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUT_OFF_DATE' );?>::<?php echo JText::_('EB_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_CUT_OFF_DATE') ; ?></span>
							</td>
							<td>
								<?php echo JHtml::_('calendar', ($this->item->cut_off_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->cut_off_date, $format, null), 'cut_off_date', 'cut_off_date') ; ?>
								<?php echo $this->lists['cut_off_hour'].' '.$this->lists['cut_off_minute']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_PRICE'); ?>
							</td>				
							<td>
								<input type="text" name="individual_price" id="individual_price" class="input-small" size="10" value="<?php echo $this->item->individual_price; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_TAX_RATE'); ?>
							</td>
							<td>
								<input type="text" name="tax_rate" id="tax_rate" class="input-small" size="10" value="<?php echo $this->item->tax_rate; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EVENT_CAPACITY' );?>::<?php echo JText::_('EB_CAPACITY_EXPLAIN'); ?>"><?php echo JText::_('EB_CAPACITY'); ?></span>
							</td>
							<td>
								<input type="text" name="event_capacity" id="event_capacity" class="input-small" size="10" value="<?php echo $this->item->event_capacity; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EB_REGISTRATION_TYPE'); ?></td>
							<td>
								<?php echo $this->lists['registration_type'] ; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUSTOM_REGISTRATION_HANDLE_URL' );?>::<?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL_EXPLAIN'); ?>"><?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL'); ?></span>
							</td>
							<td>
								<input type="text" name="registration_handle_url" id="registration_handle_url" class="input-xxlarge" size="10" value="<?php echo $this->item->registration_handle_url; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_ATTACHMENT' );?>::<?php echo JText::_('EB_ATTACHMENT_EXPLAIN'); ?>"><?php echo JText::_('EB_ATTACHMENT'); ?></span>
							</td>
							<td>
								<input type="file" name="attachment"/>
								<?php
								if ($this->item->attachment)
								{
								?>
									<?php echo JText::_('EB_CURRENT_ATTACHMENT'); ?>&nbsp;<a href="<?php echo JURI::root().'media/com_eventbooking/'.$this->item->attachment; ?>" target="_blank"><?php echo $this->item->attachment; ?></a>
									<input type="checkbox" name="del_attachment" value="1" /><?php echo JText::_('EB_DELETE_CURRENT_ATTACHMENT'); ?>
								<?php
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('EB_SHORT_DESCRIPTION'); ?>
							</td>
							<td>
								<?php echo $editor->display( 'short_description',  $this->item->short_description , '100%', '180', '90', '6' ) ; ?>					
							</td>
						</tr>					
						<tr>
							<td class="key">
								<?php echo  JText::_('EB_DESCRIPTION'); ?>
							</td>
							<td>
								<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '90', '10' ) ; ?>					
							</td>
						</tr>
					</table>			
				</fieldset>			
			</div>
			<div class="span4">
				<fieldset class="adminform">
					<legend class="adminform"><?php echo JText::_('EB_GROUP_REGISTRATION_RATES'); ?></legend>
					<table class="adminlist" id="price_list" width="100%">
						<tr>
							<th width="50%" class="eb-left-align">
								<?php echo JText::_('EB_REGISTRANT_NUMBER'); ?>
							</th>				
							<th class="eb-left-align">
								<?php echo JText::_('EB_RATE'); ?>
							</th>
						</tr>
						<?php
							$n = max(count($this->prices), 3);
							for ($i = 0 ; $i < $n ; $i++)
							{
									if (isset($this->prices[$i]))
									{
										$price = $this->prices[$i] ;
										$registrantNumber = $price->registrant_number ;
										$price = $price->price ;
									}
									else
									{
										$registrantNumber =  null ;
										$price =  null ;
									}
							?>
								<tr>
									<td class="eb-left-align">
										<input type="text" class="input-mini" name="registrant_number[]" size="10" value="<?php echo $registrantNumber; ?>" />
									</td>						
									<td class="eb-left-align">
										<input type="text" class="input-mini" name="price[]" size="10" value="<?php echo $price; ?>" />
									</td>
								</tr>
							<?php				 									
							}
						?>
						<tr>
							<td colspan="3">
								<input type="button" class="button" value="<?php echo JText::_('EB_ADD'); ?>" onclick="addRow();" />
								&nbsp;
								<input type="button" class="button" value="<?php echo JText::_('EB_REMOVE'); ?>" onclick="removeRow();" />
							</td>
						</tr>
					</table>					
				</fieldset>
				<div class="clearfix"></div>
				<fieldset class="adminform">
					<legend class="adminform"><?php echo JText::_('EB_MISC'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EVENT_PASSWORD' );?>::<?php echo JText::_('EB_EVENT_PASSWORD_EXPLAIN'); ?>"><?php echo JText::_('EB_EVENT_PASSWORD'); ?></span>
							</td>
							<td>
								<input type="text" name="event_password" id="event_password" class="input-small" size="10" value="<?php echo $this->item->event_password; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_ACCESS' );?>::<?php echo JText::_('EB_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_ACCESS'); ?></span>
							</td>
							<td>
								<?php echo $this->lists['access']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_REGISTRATION_ACCESS' );?>::<?php echo JText::_('EB_REGISTRATION_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_REGISTRATION_ACCESS'); ?></span>
							</td>
							<td>
								<?php echo $this->lists['registration_access']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_PUBLISHED'); ?>
							</td>
							<td>
								<?php echo $this->lists['published']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MIN_NUMBER_REGISTRANTS' );?>::<?php echo JText::_('EB_MIN_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MIN_NUMBER_REGISTRANTS'); ?></span>
							</td>
							<td>
								<input type="text" name="min_group_number" id="min_group_number" class="input-small" size="10" value="<?php echo $this->item->min_group_number; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MAX_NUMBER_REGISTRANTS' );?>::<?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MAX_NUMBER_REGISTRANT_GROUP'); ?></span>
							</td>
							<td>
								<input type="text" name="max_group_number" id="max_group_number" class="input-small" size="10" value="<?php echo $this->item->max_group_number; ?>" />
							</td>
						</tr>
                        <tr>
                            <td class="key">
                                <?php echo JText::_('ENABLE_COUPON'); ?>
                            </td>
                            <td>
                                <?php echo $this->lists['enable_coupon']; ?>
                            </td>
                        </tr>
						<?php
						    if ($this->config->activate_deposit_feature)
						    {
						    ?>
						    	<tr>
			            			<td class="key" width="30%">
			            				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_DEPOSIT_AMOUNT' );?>::<?php echo JText::_('EB_DEPOSIT_AMOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?></span>
			            			</td>
			            			<td>
			            				<input type="text" name="deposit_amount" id="deposit_amount" class="input-mini" size="5" value="<?php echo $this->item->deposit_amount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['deposit_type'] ; ?>
			            			</td>
			            		</tr>
						    <?php    
						    }			    
						?>
						<tr>
							<td class="key" style="width: 160px;">
								<?php echo JText::_('EB_ENABLE_CANCEL'); ?>
							</td>
							<td>
								<?php echo $this->lists['enable_cancel_registration'] ; ?>
							</td>
						</tr>		
						<tr>
							<td class="key">
								<?php echo JText::_('EB_CANCEL_BEFORE_DATE'); ?>
							</td>
							<td>
								<?php echo JHtml::_('calendar', $this->item->cancel_before_date != $this->nullDate ? JHtml::_('date', $this->item->cancel_before_date, $format, null) : '', 'cancel_before_date', 'cancel_before_date'); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_AUTO_REMINDER'); ?>
							</td>
							<td>
								<?php echo $this->lists['enable_auto_reminder']; ?>
							</td>
						</tr>			
						<tr>
							<td class="key">
								<?php echo JText::_('EB_REMIND_BEFORE'); ?>
							</td>
							<td>
								<input type="text" name="remind_before_x_days" class="inputbox" size="5" value="<?php echo $this->item->remind_before_x_days; ?>" /> days
							</td>
						</tr>	
						<?php
							if ($this->config->term_condition_by_event)
							{
							?>
								<tr>
									<td class="key">
										<?php echo JText::_('EB_TERMS_CONDITIONS'); ?>
									</td>
									<td>
										<?php echo $this->lists['article_id'] ; ?>
									</td>	
								</tr>
							<?php	
							}
						?>
					</table>					
				</fieldset>
				<div class="clearfix"></div>
				<?php
					if ($this->config->activate_recurring_event && (!$this->item->id || $this->item->event_type == 1)) {
					?>						
						<fieldset class="adminform">
							<legend class="adminform"><?php echo JText::_('EB_RECURRING_SETTINGS'); ?></legend>
							<table class="admintable">
								<tr>
									<td width="30%" class="key" valign="top">
										<strong><?php echo JText::_('EB_REPEAT_TYPE'); ?></strong>
									</td>				
									<td>					
										<table width="100%">
											<tr>
												<td>
													<input type="radio" name="recurring_type" value="0" <?php if ($this->item->recurring_type == 0) echo ' checked="checked" ' ; ?> onclick="setDefaultDate();" /> <?php echo JText::_('EB_NO_REPEAT'); ?>
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="recurring_type" value="1" <?php if ($this->item->recurring_type == 1) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_days" size="5" class="input-mini clearfloat" value="<?php echo $this->item->number_days ; ?>" /> <?php echo JText::_('EB_DAYS'); ?>
												</td>
											</tr>
											<tr>
												<td>
			    										<input type="radio" name="recurring_type" value="2" <?php if ($this->item->recurring_type == 2) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_weeks" size="5" class="input-mini clearfloat" value="<?php echo $this->item->number_weeks ; ?>" /> <?php echo JText::_('EB_WEEKS'); ?>
			    										<br />
			    										<strong><?php echo JText::_('EB_ON'); ?></strong> 
			        									<?php
			        										$weekDays = explode(',', $this->item->weekdays) ;
			        										$daysOfWeek = array(0=> 'EB_SUN', 1 => 'EB_MON', 2=> 'EB_TUE', 3=>'EB_WED', 4 => 'EB_THUR', 5=>'EB_FRI', 6=> 'EB_SAT') ;
			        										foreach ($daysOfWeek as $key=>$value) {
			        										?>
			        											<input type="checkbox" class="inputbox clearfloat" value="<?php echo $key; ?>" name="weekdays[]" <?php if (in_array($key, $weekDays)) echo ' checked="checked"' ; ?> /> <?php echo JText::_($value); ?>&nbsp;&nbsp;
			        										<?php
			        											if ($key == 4)
			        												echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ;	
			        										}
			        									?>			
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="recurring_type" value="3" <?php if ($this->item->recurring_type == 3) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_months" size="5" class="input-mini clearfloat" value="<?php echo $this->item->number_months ; ?>" /> <?php echo JText::_('EB_MONTHS'); ?>
											        <?php echo JText::_('EB_ON'); ?> <input type="text" name="monthdays" class="input-mini clearfloat" size="10" value="<?php echo $this->item->monthdays; ?>" />
												</td>
											</tr>

											<tr>
												<td>
													<?php
														$params = new JRegistry($this->item->params);
														$options = array();
														$options[] = JHtml::_('select.option', 'first', JText::_('EB_FIRST'));
														$options[] = JHtml::_('select.option', 'second', JText::_('EB_SECOND'));
														$options[] = JHtml::_('select.option', 'third', JText::_('EB_THIRD'));
														$options[] = JHtml::_('select.option', 'fourth', JText::_('EB_FOURTH'));
														$options[] = JHtml::_('select.option', 'fifth', JText::_('EB_FIFTH'));
														$daysOfWeek = array(
															'Sun' => JText::_('EB_SUNDAY'),
															'Mon' => JText::_('EB_MONDAY'),
															'Tue' => JText::_('EB_TUESDAY'),
															'Web' => JText::_('EB_WEDNESDAY'),
															'THU' => JText::_('EB_THURSDAY'),
															'Fri' => JText::_('EB_FRIDAY'),
															'Sat' => JText::_('EB_SATURDAY')
														);
													?>
													<input type="radio" name="recurring_type" value="4" <?php if ($this->item->recurring_type == 4) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="weekly_number_months" size="5" class="input-mini clearfloat" value="<?php echo $params->get('weekly_number_months', ''); ?>" /> <?php echo JText::_('EB_MONTHS'); ?>
													<?php echo JText::_('EB_ON'); ?> <?php echo JHtml::_('select.genericlist', $options, 'week_in_month', ' class="input-small" ', 'value', 'text', $params->get('week_in_month', 'first')); ?>
													<?php echo JHtml::_('select.genericlist', $daysOfWeek, 'day_of_week', ' class="input-small" ', 'value', 'text', $params->get('day_of_week', 'Sun'));?>
													of the month
												</td>
											</tr>

										</table>																																										
									</td>
								</tr>
								<tr>
									<td class="key">
										<strong><?php echo JText::_('EB_RECURRING_ENDING'); ?></strong>
									</td>
									<td>							
										<input type="radio" name="repeat_until" value="1"  <?php if (($this->item->recurring_occurrencies > 0) || ($this->item->recurring_end_date == '') || ($this->item->recurring_end_date == '0000-00-00 00:00:00')) echo ' checked="checked" ' ; ?> /> <?php echo JText::_('EB_AFTER'); ?> <input type="text" name="recurring_occurrencies" size="5" class="inputbox clearfloat" value="<?php echo $this->item->recurring_occurrencies ; ?>" /> <?php echo JText::_('EB_OCCURENCIES'); ?>  
										<br />							
										<input type="radio" name="repeat_until" value="2" <?php if (($this->item->recurring_end_date != '') && ($this->item->recurring_end_date != '0000-00-00 00:00:00')) echo ' checked="checked"' ; ?> /> <?php echo JText::_('EB_AFTER_DATE') ?> <?php echo JHtml::_('calendar', $this->item->recurring_end_date != '0000-00-00 00:00:00' ? JHtml::_('date', $this->item->recurring_end_date, $format, null) : '', 'recurring_end_date', 'recurring_end_date'); ?>
										<br />
									</td>
								</tr>	
								<?php
									if ($this->item->id) {
									?>
										<tr>
											<td class="key"><strong><?php echo JText::_('EB_UPDATE_CHILD_EVENT');?></strong></td>
											<td>
												<input type="checkbox" name="update_children_event" value="1" class="inputbox" />
											</td>
										</tr>
									<?php	
									}
								?>				
							</table>					
						</fieldset>								
					<?php	
					}
				?>
                <div class="clearfix"></div>
                <fieldset class="adminform">
                    <legend class="adminform"><?php echo JText::_('EB_META_DATA'); ?></legend>
                    <table class="admintable">
                        <tr>
                            <td width="100" class="key">
                                <?php echo  JText::_('EB_META_KEYWORDS'); ?>
                            </td>
                            <td>
                                <textarea rows="5" cols="30" class="input-lage" name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="key">
                                <?php echo  JText::_('EB_META_DESCRIPTION'); ?>
                            </td>
                            <td>
                                <textarea rows="5" cols="30" class="input-lage" name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
                            </td>
                        </tr>
                    </table>
                </fieldset>
			</div>
		</div>												
		<div class="clearfix"></div>
	</div>
	<div class="tab-pane" id="discount-page">
		<table class="admintable">
			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MEMBER_DISCOUNT_GROUPS' );?>::<?php echo JText::_('EB_MEMBER_DISCOUNT_GROUPS_EXPLAIN'); ?>"><?php echo JText::_('EB_MEMBER_DISCOUNT_GROUPS'); ?></span>
				</td>
				<td>
					<?php echo $this->lists['discount_groups']; ?>
				</td>
			</tr>
			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MEMBER_DISCOUNT' );?>::<?php echo JText::_('EB_MEMBER_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_MEMBER_DISCOUNT'); ?></span>
				</td>
				<td>
					<input type="text" name="discount_amounts" id="discount_amounts" class="input-large" size="5" value="<?php echo $this->item->discount_amounts; ?>" />&nbsp;&nbsp;<?php echo $this->lists['discount_type'] ; ?>
				</td>
			</tr>
			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT'); ?></span>
				</td>
				<td>
					<input type="text" name="early_bird_discount_amount" id="early_bird_discount_amount" class="input-mini" size="5" value="<?php echo $this->item->early_bird_discount_amount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['early_bird_discount_type'] ; ?>
				</td>
			</tr>
			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT_DATE' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE'); ?></span>
				</td>
				<td>				
					<?php echo JHtml::_('calendar', $this->item->early_bird_discount_date != $this->nullDate ? JHtml::_('date', $this->item->early_bird_discount_date, $format, null) : '', 'early_bird_discount_date', 'early_bird_discount_date'); ?>
				</td>
			</tr>

			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_LATE_FEE' );?>::<?php echo JText::_('EB_LATE_FEE_EXPLAIN'); ?>"><?php echo JText::_('EB_LATE_FEE'); ?></span>
				</td>
				<td>
					<input type="text" name="late_fee_amount" id="late_fee_amount" class="input-mini" size="5" value="<?php echo $this->item->late_fee_amount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['late_fee_type'] ; ?>
				</td>
			</tr>
			<tr>
				<td class="key" width="30%">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_LATE_FEE_DATE' );?>::<?php echo JText::_('EB_LATE_FEE_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_LATE_FEE_DATE'); ?></span>
				</td>
				<td>
					<?php echo JHtml::_('calendar', $this->item->late_fee_date && $this->item->late_fee_date != $this->nullDate ? JHtml::_('date', $this->item->late_fee_date, $format, null) : '', 'late_fee_date', 'late_fee_date'); ?>
				</td>
			</tr>
		</table>
	</div>	
	<?php 
		if ($this->config->event_custom_field) {
		?>
			<div class="tab-pane" id="extra-information-page">				
				<table class="admintable" width="100%">
					<?php
						foreach ($this->form->getFieldset('basic') as $field) {
						?>
							<tr>
								<td class="key" width="15%">
									<?php echo $field->label ;?>
								</td>
								<td>
									<?php echo  $field->input ; ?>
								</td>
							</tr>
						<?php
						}					
					?>								
				</table>
			</div>
		<?php	
		}
	if ($translatable)
	{
	?>
	<div class="tab-pane" id="translation-page">
		<ul class="nav nav-tabs">
			<?php
			$i = 0;
			foreach ($this->languages as $language) {
				$sef = $language->sef;
				?>
				<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#translation-page-<?php echo $sef; ?>" data-toggle="tab"><?php echo $language->title; ?>
						<img src="<?php echo JUri::root(); ?>media/com_eventbooking/flags/<?php echo $sef.'.png'; ?>" /></a></li>
				<?php
				$i++;
			}
			?>
		</ul>
		<div class="tab-content">
			<?php
			$i = 0;
			foreach ($this->languages as $language)
			{
				$sef = $language->sef;
				?>
				<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key">
								<?php echo  JText::_('EB_TITLE'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="title_<?php echo $sef; ?>" id="title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'title_'.$sef}; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('EB_ALIAS'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="alias_<?php echo $sef; ?>" id="alias_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'alias_'.$sef}; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_SHORT_DESCRIPTION'); ?>
							</td>
							<td>
								<?php echo $editor->display( 'short_description_'.$sef,  $this->item->{'short_description_'.$sef} , '100%', '250', '75', '10' ) ; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_DESCRIPTION'); ?>
							</td>
							<td>
								<?php echo $editor->display( 'description_'.$sef,  $this->item->{'description_'.$sef} , '100%', '250', '75', '10' ) ; ?>
							</td>
						</tr>

						<tr>
							<td width="100" class="key">
								<?php echo  JText::_('EB_META_KEYWORDS'); ?>
							</td>
							<td>
								<textarea rows="5" cols="30" class="input-lage" name="meta_keywords_<?php echo $sef; ?>"><?php echo $this->item->{'meta_keywords_'.$sef}; ?></textarea>
							</td>
						</tr>
						<tr>
							<td width="100" class="key">
								<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
							</td>
							<td>
								<textarea rows="5" cols="30" class="input-lage" name="meta_description_<?php echo $sef; ?>"><?php echo $this->item->{'meta_description_'.$sef}; ?></textarea>
							</td>
						</tr>
					</table>
				</div>
				<?php
				$i++;
			}
			?>
		</div>
	</div>
	<?php
	}
	?>
	<div class="tab-pane" id="advance-settings-page">
		<table class="admintable">
			<tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_PAYMENT_METHODS'); ?>
				</td>				
				<td width="50%">
					<?php echo $this->lists['payment_methods'] ?>
				</td>
				<td>
					<?php echo JText::_('EB_PAYMENT_METHODS_EXPLAIN'); ?>				
				</td>
			</tr>
            <tr>
                <td class="key">
                    <?php echo JText::_('EB_FIXED_GROUP_PRICE'); ?>
                </td>
                <td>
                    <input type="text" name="fixed_group_price" id="fixed_group_price" class="inputbox" size="10" value="<?php echo $this->item->fixed_group_price; ?>" />
                </td>
                <td>
                    <?php echo JText::_('EB_FIXED_GROUP_PRICE_EXPLAIN');?>
                </td>
            </tr>
			<tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_CURRENCY'); ?>
				</td>				
				<td width="50%">
					<?php echo $this->lists['currency_code'] ?>
				</td>
				<td>
					<?php echo JText::_('EB_CURRENCY_CODE_EXPLAIN'); ?>				
				</td>
			</tr>
			<tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_CURRENCY_SYMBOL'); ?>
				</td>				
				<td width="50%">
					<input type="text" name="currency_symbol" size="5" class="inputbox" value="<?php echo $this->item->currency_symbol; ?>" />
				</td>
				<td>
					<?php echo JText::_('EB_CURRENCY_SYMBOL_EXPLAIN'); ?>				
				</td>
			</tr>		
			<tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_PAYPAL_EMAIL'); ?>
				</td>				
				<td width="50%">
					<input type="text" name="paypal_email" class="inputbox" size="50" value="<?php echo $this->item->paypal_email ; ?>" />
				</td>
				<td>
					<?php echo JText::_('EB_PAYPAL_EMAIL_EXPLAIN'); ?>
				</td>
			</tr>
            <tr>
                <td class="key"><?php echo JText::_('EB_API_LOGIN') ; ?></td>
                <td>
                    <input type="text" name="api_login" value="<?php echo $this->item->api_login; ?>" class="inputbox" size="30" />
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('EB_TRANSACTION_KEY') ; ?></td>
                <td>
                    <input type="text" name="transaction_key" value="<?php echo $this->item->transaction_key; ?>" class="inputbox" size="30" />
                </td>
                <td></td>
            </tr>
            <tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_CUSTOM_FIELD_IDS'); ?>
				</td>				
				<td width="50%">
					<input type="text" name="custom_field_ids" class="inputbox" size="70" value="<?php echo $this->item->custom_field_ids ; ?>" />
				</td>
				<td>
					<?php echo JText::_('EB_CUSTOM_FIELD_IDS_EXPLAIN'); ?>
				</td>
			</tr>		
			<tr>
				<td width="30%" class="key">
					<?php echo JText::_('EB_NOTIFICATION_EMAILS'); ?>
				</td>				
				<td>
					<input type="text" name="notification_emails" class="inputbox" size="70" value="<?php echo $this->item->notification_emails ; ?>" />
				</td>
				<td>
					<?php echo JText::_('EB_NOTIFICATION_EMAIL_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo  JText::_('EB_USER_EMAIL_BODY'); ?>
				</td>
				<td>
					<?php echo $editor->display( 'user_email_body',  $this->item->user_email_body , '100%', '250', '90', '10' ) ; ?>					
				</td>
				<td>
					<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
				</td>
			</tr>		
			<tr>
				<td class="key">
					<?php echo  JText::_('EB_USER_EMAIL_BODY_OFFLINE'); ?>
				</td>
				<td>
					<?php echo $editor->display( 'user_email_body_offline',  $this->item->user_email_body_offline , '100%', '250', '90', '10' ) ; ?>					
				</td>
				<td>
					<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
				</td>
			</tr>	
			<tr>
				<td class="key">
					<?php echo  JText::_('EB_THANKYOU_MESSAGE'); ?>
				</td>
				<td>
					<?php echo $editor->display( 'thanks_message',  $this->item->thanks_message , '100%', '180', '90', '6' ) ; ?>					
				</td>
				<td>
					&nbsp;
				</td>
			</tr>					
			<tr>
				<td class="key">
					<?php echo  JText::_('EB_THANKYOU_MESSAGE_OFFLINE'); ?>
				</td>
				<td>
					<?php echo $editor->display( 'thanks_message_offline',  $this->item->thanks_message_offline , '100%', '180', '90', '6' ) ; ?>					
				</td>
				<td>
					&nbsp;				
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo  JText::_('EB_REGISTRATION_APPROVED_EMAIL_BODY'); ?>
				</td>
				<td>
					<?php echo $editor->display( 'registration_approved_email_body',  $this->item->registration_approved_email_body , '100%', '180', '90', '6' ) ; ?>					
				</td>
				<td>
					&nbsp;				
				</td>
			</tr>			
		</table>	
	</div>
	<!-- Plugin support -->
	<?php 
		if (count($this->plugins)) {
			$count = 0 ;
			foreach ($this->plugins as $plugin) {						
				$form = $plugin['form'] ;
				$count++ ;
			?>
				<div class="tab-pane" id="tab_<?php echo $count; ?>">
					<?php 
						echo $form ;
					?>
				</div>
			<?php							
			}
		}
	?>	
</div>			
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				Joomla.submitform( pressbutton );
				return;				
			} else {
				//Should have some validations rule here
				//Check something here
				if (form.title.value == '') {
					alert("<?php echo JText::_('EB_PLEASE_ENTER_TITLE'); ?>");
					form.title.focus();
					return ;
				}
				if (form.main_category_id.value == 0) 
				{
					alert("<?php echo JText::_("EB_CHOOSE_CATEGORY"); ?>");
					form.category_id.focus();
					return ;
				}			
				if (form.event_date.value == '') {
					alert("<?php echo JText::_('EB_ENTER_EVENT_DATE'); ?>");
					form.event_date.focus();
					return ;
				}
				if (form.recurring_type) {
					//Check the recurring setting				
					if (form.recurring_type[1].checked) {
						if (form.number_days.value == '') {
							alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_DAYS"); ?>");
							form.number_days.focus();
							return ;
						}			
						if (!parseInt(form.number_days.value)) {
							alert("<?php echo JText::_("EB_NUMBER_DAY_INTEGER"); ?>");
							form.number_days.focus();
							return ;
						}		
					}else if (form.recurring_type[2].checked) {
						if (form.number_weeks.value == '') {
							alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_WEEKS"); ?>");
							form.number_weeks.focus();
							return ;
						}			
						if (!parseInt(form.number_weeks.value)) {
							alert("<?php echo JText::_("EB_NUMBER_WEEKS_INTEGER"); ?>");
							form.number_weeks.focus();
							return ;
						}
						//Check whether any days in the week
						var checked = false ;
						for (var i = 0 ; i < form['weekdays[]'].length ; i++) {
							if (form['weekdays[]'][i].checked)
								checked = true ;						
						}
						if (!checked) {
							alert("<?php echo JText::_("EB_CHOOSE_ONEDAY"); ?>");
							form['weekdays[]'][0].focus();
							return ;
						}										
					} else if (form.recurring_type[3].checked) {
						if (form.number_months.value == '') {
							alert("<?php echo JText::_("EB_ENTER_NUMBER_MONTHS"); ?>");
							form.number_months.focus();
							return ;
						}			
						if (!parseInt(form.number_months.value)) {
							alert("<?php echo JText::_("EB_NUMBER_MONTH_INTEGER"); ?>");
							form.number_months.focus();
							return ;
						}
						if (form.monthdays.value == '') {
							alert("<?php echo JText::_("EB_ENTER_DAY_IN_MONTH"); ?>");
							form.monthdays.focus();
							return ;
						}
					}
				}			

				<?php 
					$editorFields = array('short_description', 'description', 'user_email_body', 'user_email_body_offline', 'thanks_message', 'thanks_message_offline', 'registration_approved_email_body');
					foreach ($editorFields as $editorField) {
						echo $editor->save($editorField);
					}
					
				?>							 
				Joomla.submitform( pressbutton );
			}
		}
		function addRow() {
			var table = document.getElementById('price_list');
			var newRowIndex = table.rows.length - 1 ;
			var row = table.insertRow(newRowIndex);			
			var registrantNumber = row.insertCell(0);							
			var price = row.insertCell(1);						
			registrantNumber.innerHTML = '<input type="text" class="input-mini" name="registrant_number[]" size="10" />';
			price.innerHTML = '<input type="text" class="input-mini" name="price[]" size="10" />';
			
		}
		function removeRow() {
			var table = document.getElementById('price_list');
			var deletedRowIndex = table.rows.length - 2 ;
			if (deletedRowIndex >= 1) {
				table.deleteRow(deletedRowIndex);
			} else {
				alert("<?php echo JText::_('EB_NO_ROW_TO_DELETE'); ?>");
			}
		}

		function setDefaultData() {
			var form = document.adminForm ;
			if (form.recurring_type[1].checked) {
				if (form.number_days.value == '') {
					form.number_days.value =1 ;
				}
			} else if (form.recurring_type[2].checked) {
				if (form.number_weeks.value == '') {
					form.number_weeks.value = 1 ;
				}
			} else if (form.recurring_type[3].checked) {
				if (form.number_months.value == '') {
					form.number_months.value = 1 ;
				}
			} else if (form.recurring_type[4].checked) {
				if (form.number_months.value == '') {
					form.number_months.value = 1 ;
				}
			}
		}	
	</script>
</form>
</div>
