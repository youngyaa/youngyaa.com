<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
?>
<script type="text/javascript">	
		Joomla.submitbutton = function(pressbutton)
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				Joomla.submitform(pressbutton, form);
				return;				
			} else {
				if (form.name.value == "") {
					alert('<?php echo JText::_('OSM_ENTER_CUSTOM_FIELD_NAME'); ?>');
					form.name.focus();
					return ;
				}
				if (form.title.value == "") {
					alert("<?php echo JText::_("OSM_ENTER_CUSTOM_FIELD_TITLE"); ?>");
					form.title.focus();
					return ; 
				}
				if (form.fieldtype.value == -1) {
					alert("<?php echo JText::_("OSM_CHOOSE_CUSTOM_FIELD_TYPE") ; ?>");
					return ; 
				}					
				//Validate the entered data before submitting									
				Joomla.submitform(pressbutton, form);
			}
		}											
</script>
<form action="index.php?option=com_osmembership&view=field" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
<?php 
	if ($translatable)
	{
	?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('OSM_GENERAL'); ?></a></li>
			<li><a href="#translation-page" data-toggle="tab"><?php echo JText::_('OSM_TRANSLATION'); ?></a></li>									
		</ul>		
		<div class="tab-content">
			<div class="tab-pane active" id="general-page">			
	<?php	
	}
?>				
	<table class="admintable adminform">		
		<tr>
			<td class="key" valign="top"  width="25%"> 
				<?php echo JText::_('OSM_PLAN'); ?>
			</td>
			<td>
				<?php echo $this->lists['plan_id'] ; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>			
		<tr>
			<td class="key">
				<?php echo  JText::_('OSM_NAME'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" onchange="checkFieldName();" <?php if ($this->item->is_core) echo 'readonly="readonly"' ; ?> />
			</td>
			<td>
				<?php echo JText::_('OSM_FIELD_NAME_REQUIREMNET'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('OSM_TITLE'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="50" maxlength="250" value="<?php echo $this->item->title;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_FIELD_TYPE'); ?>
			</td>
			<td>
				<?php echo $this->lists['fieldtype']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_DESCRIPTION'); ?>
			</td>
			<td>
				<textarea rows="7" cols="50" name="description" class="input-xlarge"><?php echo $this->item->description;?></textarea>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_REQUIRED'); ?>
			</td>
			<td>
				<?php echo $this->lists['required']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		
		<tr class="osm-field osm-list osm-checkboxes osm-radio">
			<td class="key">
				<?php echo JText::_('OSM_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="values"><?php echo $this->item->values; ?></textarea>
			</td>
			<td>
				<?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_DEFAULT_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="default_values"><?php echo $this->item->default_values; ?></textarea>
			</td>
			<td>
				<?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
			</td>
		</tr>					
		<tr class="osm-field osm-text osm-list osm-checkboxes osm-radio">
			<td class="key"><?php echo JText::_('OSM_FEE_FIELD') ; ?></td>
			<td>
				<?php echo $this->lists['fee_field']; ?>
			</td>
			<td>
				&nbsp;
			</td>			
		</tr>			
		<tr class="osm-fee-field osm-field osm-list osm-checkboxes osm-radio">
			<td class="key">
				<?php echo JText::_('OSM_FEE_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="fee_values"><?php echo $this->item->fee_values; ?></textarea>
			</td>
			<td>
				 <?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
			</td>
		</tr>			
		<tr class="osm-fee-field osm-field osm-list osm-checkboxes osm-radio">
			<td class="key">
				<?php echo JText::_('OSM_FEE_FORMULA') ; ?>
			</td>
			<td>
				<input type="text" class="inputbox" size="50" name="fee_formula" value="<?php echo $this->item->fee_formula ; ?>" />
			</td>
			<td>
				<?php echo JText::_('OSM_FEE_FORMULA_EXPLAIN'); ?>
			</td>
		</tr>				
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_DATATYPE_VALIDATION') ; ?>
			</td>
			<td>
				<?php echo $this->lists['datatype_validation']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>			
		
		<tr class="validation-rules">
			<td class="key">
				<?php echo JText::_('OSM_VALIDATION_RULES') ; ?>
			</td>
			<td>
				<input type="text" class="inputbox" size="50" name="validation_rules" value="<?php echo $this->item->validation_rules ; ?>" />
			</td>
			<td>
				<?php echo JText::_('OSM_VALIDATION_RULES_EXPLAIN'); ?>
			</td>
		</tr>		
		
		<tr class="validation-rules">
			<td class="key">
				<?php echo JText::_('OSM_VALIDATION_ERROR_MESSAGE') ; ?>
			</td>
			<td>
				<input type="text" class="inputbox" size="50" name="validation_error_message" value="<?php echo $this->item->validation_error_message ; ?>" />
			</td>
			<td>
				<?php echo JText::_('OSM_VALIDATION_ERROR_MESSAGE_EXPLAIN'); ?>
			</td>
		</tr>				
		<?php	
			if (isset($this->lists['field_mapping']))
			{
			?>
				<tr>
					<td class="key">
						<?php echo JText::_('OSM_FIELD_MAPPING') ; ?>
					</td>
					<td>
						<?php echo $this->lists['field_mapping'] ; ?>						
					</td>
					<td>
						<?php echo JText::_('OSM_FIELD_MAPPING_GUIDE'); ?> 
					</td>
				</tr>
			<?php	
			}
			if (JPluginHelper::isEnabled('osmembership', 'userprofile')) 
			{			
			?>
				<tr>
					<td class="key">
						<?php echo JText::_('OSM_PROFILE_FIELD_MAPPING') ; ?>
					</td>
					<td>
						<?php echo $this->lists['profile_field_mapping'] ; ?>						
					</td>
					<td>
						<?php echo JText::_('OSM_PROFILE_FIELD_MAPPING_GUIDE'); ?> 
					</td>
				</tr>
			<?php	
			}
		?>		
		<tr class="osm-field osm-list">
			<td class="key">
				<?php echo JText::_('OSM_MULTIPLE'); ?>
			</td>
			<td>
				<?php echo $this->lists['multiple']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-field osm-textarea">
			<td class="key">
				<?php echo  JText::_('OSM_ROWS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="rows" id="rows" size="10" maxlength="250" value="<?php echo $this->item->rows;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-field osm-textarea">
			<td class="key">
				<?php echo  JText::_('OSM_COLS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="cols" id="cols" size="10" maxlength="250" value="<?php echo $this->item->cols;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-field osm-text osm-checkboxes osm-radio">
			<td class="key">
				<?php echo  JText::_('OSM_SIZE'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="size" id="size" size="10" maxlength="250" value="<?php echo $this->item->size;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr> 
			<td class="key">
				<?php echo  JText::_('OSM_CSS_CLASS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="css_class" id="css_class" size="10" maxlength="250" value="<?php echo $this->item->css_class;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-field osm-text osm-textarea">
			<td class="key">
				<?php echo  JText::_('OSM_PLACE_HOLDER'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="place_holder" id="place_holder" size="50" maxlength="250" value="<?php echo $this->item->place_holder;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-field osm-text osm-textarea">
			<td class="key">
				<?php echo  JText::_('OSM_MAX_LENGTH'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="max_length" id="max_lenth" size="50" maxlength="250" value="<?php echo $this->item->max_length;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="osm-all">
			<td class="key">
				<?php echo  JText::_('OSM_EXTRA'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="extra" id="extra" size="40" maxlength="250" value="<?php echo $this->item->extra;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_ACCESS'); ?>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_SHOW_ON_MEMBER_LIST'); ?>
			</td>
			<td>
				<?php echo $this->lists['show_on_members_list']; ?>
			</td>
			<td>
				<?php echo JText::_('OSM_SHOW_ON_MEMBER_LIST_EXPLAIN'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_HIDE_ON_MEMBERSHIP_RENEWAL'); ?>
			</td>
			<td>
				<?php echo $this->lists['hide_on_membership_renewal']; ?>
			</td>
			<td>
				<?php echo JText::_('OSM_HIDE_ON_MEMBERSHIP_RENEWAL_EXPLAIN'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('OSM_PUBLISHED'); ?>
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>

		<tr>
			<td class="key">
				<?php echo JText::_('OSM_DEPEND_ON_FIELD');?>
			</td>
			<td colspan="2">
				<?php echo $this->lists['depend_on_field_id']; ?>
			</td>
		</tr>
		<tr id="depend_on_options_container" style="display: <?php echo $this->item->depend_on_field_id ? '' : 'none'; ?>">
			<td class="key">
				<?php echo JText::_('OSM_DEPEND_ON_OPTIONS');?>
			</td>
			<td id="options_container" colspan="2">
				<?php
				if (count($this->dependOptions))
				{
					?>
					<table cellspacing="3" cellpadding="3" width="100%">
						<?php
						$optionsPerLine = 3;
						for ($i = 0 , $n = count($this->dependOptions) ; $i < $n ; $i++)
						{
							$value = $this->dependOptions[$i] ;
							if ($i % $optionsPerLine == 0) {
								?>
								<tr>
							<?php
							}
							?>
							<td>
								<input class="inputbox" value="<?php echo $value; ?>" type="checkbox" name="depend_on_options[]" <?php if (in_array($value, $this->dependOnOptions)) echo 'checked="checked"'; ?>><?php echo $value;?>
							</td>
							<?php
							if (($i+1) % $optionsPerLine == 0)
							{
								?>
								</tr>
							<?php
							}
						}
						if ($i % $optionsPerLine != 0)
						{
							$colspan = $optionsPerLine - $i % $optionsPerLine ;
							?>
                                <td colspan="<?php echo $colspan; ?>">&nbsp;</td>
                                </tr>
                            <?php
						}
						?>
					</table>
				<?php
				}
				?>
			</td>
		</tr>

	</table>		
	<?php 
	if ($translatable)
	{
	?>
		</div>
		<div class="tab-pane" id="translation-page">
			<ul class="nav nav-tabs">
				<?php
					$i = 0;
					foreach ($this->languages as $language) {						
						$sef = $language->sef;
						?>
						<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#translation-page-<?php echo $sef; ?>" data-toggle="tab"><?php echo $language->title; ?>
							<img src="<?php echo JUri::root(); ?>media/com_osmembership/flags/<?php echo $sef.'.png'; ?>" /></a></li>
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
										<?php echo  JText::_('OSM_TITLE'); ?>
									</td>
									<td>
										<input class="input-xlarge" type="text" name="title_<?php echo $sef; ?>" id="title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'title_'.$sef}; ?>" />
									</td>								
								</tr>										
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_DESCRIPTION'); ?>
									</td>
									<td>
										<textarea rows="5" cols="50" name="description_<?php echo $sef; ?>"><?php echo $this->item->{'description_'.$sef};?></textarea>
									</td>
									<td>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_VALUES'); ?>
									</td>
									<td>
										<textarea rows="5" cols="50" name="values_<?php echo $sef; ?>"><?php echo $this->item->{'values_'.$sef}; ?></textarea>
									</td>
									<td>
										<?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_DEFAULT_VALUES'); ?>
									</td>
									<td>
										<textarea rows="5" cols="50" name="default_values_<?php echo $sef; ?>"><?php echo $this->item->{'default_values_'.$sef}; ?></textarea>
									</td>
									<td>
										<?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('OSM_FEE_VALUES'); ?>
									</td>
									<td>
										<textarea rows="5" cols="50" name="fee_values_<?php echo $sef; ?>"><?php echo $this->item->{'fee_values_'.$sef}; ?></textarea>
									</td>
									<td>
										<?php echo JText::_('OSM_EACH_ITEM_IN_ONELINE'); ?>
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
				
</div>		
<div class="clearfix"></div>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_( 'form.token' ); ?>	
	<script type="text/javascript">		
		(function($){
			$(document).ready(function(){				
				var validateEngine = <?php  echo OSMembershipHelper::validateEngine(); ?>;
				$("input[name='required']").bind( "click", function() {
					validateRules();
				});
				$( "#datatype_validation" ).bind( "change", function() {
					validateRules();
				});

				$( "#fieldtype" ).bind( "change", function() {
						changeFiledType($(this).val());
				});
				
				changeFiledType('<?php echo $this->item->fieldtype;  ?>');
				<?php
					if($this->item->id && $this->item->fee_field)
				 	{
				?>
					$('.osm-fee-field').show();
				<?php
				 	}  
				?>
				function validateRules()
				{
					var validationString;
					if ($("input[name='name']").val() == 'email')
					{
						//Hardcode the validation rule for email
						validationString = 'validate[required,custom[email],ajax[ajaxEmailCall]]';
					}	
					else 
					{
						var validateType = parseInt($('#datatype_validation').val());
						validationString = validateEngine[validateType];
						var required = $("input[name='required']:checked").val();					
						if (required == 1)
						{
							if (validationString == '')
							{
								validationString = 'validate[required]';
							}
							else 
							{
								if (validationString.indexOf('required') == -1)
								{
									validationString = [validationString.slice(0, 9), 'required,', validationString.slice(9)].join('');
								}
							}
						}
						else 
						{						
							if (validationString == 'validate[required]')
							{
								validationString = '';
							}
							else 
							{							
								validationString = validationString.replace('validate[required', 'validate[');
							}
						}		
					}							
																					
					$("input[name='validation_rules']").val(validationString);
				}
				
				
				function changeFiledType(fieldType)
				{
					if (fieldType == '')
					{
						$('tr.osm-field').hide();
					}
					else 
					{
						var cssClass = '.osm-' + fieldType.toLowerCase();	
						$('tr.osm-field').show();
						$('tr.osm-field').not(cssClass).hide();
					}	
					if(fieldType == 'List' || fieldType == 'Checkboxes' || fieldType == 'Radio' || fieldType == 'Text')
					{
						if($('[name^=fee_field]').val() == 0)
						{
							$('tr.osm-fee-field').hide();
						}
						else
						{
							$('.osm-fee-field').show();
						}
						<?php
							if($this->item->id && $this->item->fee_field)
						 	{
						?>
							$('.osm-fee-field').show();
						<?php
						 	}  
						?>
					}																											
				}

				//change fee field
				$('[name^=fee_field]').click(function(){
					if($(this).val() == 1)
					{
						$('tr.osm-fee-field').show();
					}						
					else
					{
						$('tr.osm-fee-field').hide();
					}						
				})



			});
		})(jQuery);		
		function checkFieldName() {
			var form = document.adminForm ;
			var name = form.name.value ;
			var oldValue = name ;			
			name = name.replace('osm_', '');
			while(name.indexOf('  ') >=0)
			 	name = name.replace('  ', ' ');											
			while(name.indexOf(' ') >=0)
			 	name = name.replace(' ', '_');
		 	name = name.replace(/[^a-zA-Z0-9_]*/ig, '');
			form.name.value='osm_' + name;						
		}

		(function($){
			updateDependOnOptions = (function()
			{
				var siteUrl = "<?php echo JUri::base(); ?>";
				var fieldId = $('#depend_on_field_id').val();
				if (fieldId > 0) {
					$.ajax({
						type: 'POST',
						url: siteUrl + 'index.php?option=com_osmembership&view=field&format=raw&field_id=' + fieldId,
						dataType: 'html',
						success: function(msg, textStatus, xhr) {
							$('#options_container').html(msg);
							$('#depend_on_options_container').show();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(textStatus);
						}
					});

				}
				else
				{
					$('#options_container').html('');
					$('#depend_on_options_container').hide();
				}
			});
		})(jQuery);


	</script>	
</form>