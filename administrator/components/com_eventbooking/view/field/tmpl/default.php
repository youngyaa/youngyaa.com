<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select#event_id,select#category_id');
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') 
		{
			Joomla.submitform( pressbutton );
			return;				
		} 
		else 
		{
			//Should validate the information here
			if (form.name.value == "") 
			{
				alert("<?php echo JText::_('EB_ENTER_FIELD_NAME'); ?>");
				form.name.focus();
				return ;
			}
			if (form.title.value == "") 
			{
				alert("<?php echo JText::_("EB_ENTER_FIELD_TITLE"); ?>");
				form.title.focus();
				return ; 
			}				
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=field" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
<?php
if ($translatable)
{
?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('EB_GENERAL'); ?></a></li>
	<li><a href="#translation-page" data-toggle="tab"><?php echo JText::_('EB_TRANSLATION'); ?></a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="general-page">
		<?php
		}
		?>
	<table class="admintable adminform">		
		<?php 
			if ($this->config->custom_field_by_category)
			{
			?>
				<tr>
					<td class="key" valign="top" width="20%"> 
						<?php echo JText::_('EB_CATEGORY'); ?>
					</td>
					<td style="margin-bottom: 10px;">
						<?php echo $this->lists['category_id'] ; ?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>	
			<?php	
			}
			else 
			{
			?>
				<tr>
					<td class="key" valign="top" width="20%"> 
						<?php echo JText::_('EB_EVENT'); ?>
					</td>
					<td>
						<?php echo $this->lists['event_id'] ; ?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>		
			<?php	
			}
		?>					
		<tr>
			<td class="key">
				<?php echo JText::_('EB_FIELD_TYPE'); ?>
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
				<?php echo  JText::_('EB_NAME'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" onchange="checkFieldName();" <?php if ($this->item->is_core) echo 'readonly="readonly"' ;?> />
			</td>
			<td>
				<?php echo JText::_('EB_FIELD_NAME_REQUIREMENT'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_TITLE'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="50" maxlength="250" value="<?php echo $this->item->title;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
		<tr class="eb-field eb-list">	
			<td class="key">
				<?php echo JText::_('EB_MULTIPLE'); ?>
			</td>
			<td>
				<?php echo $this->lists['multiple']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('EB_REQUIRED'); ?>
			</td>
			<td>
				<?php echo $this->lists['required']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>	
		<tr>
			<td class="key">
				<?php echo JText::_('EB_DATATYPE_VALIDATION') ; ?>
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
				<?php echo JText::_('EB_VALIDATION_RULES') ; ?>
			</td>
			<td>
				<input type="text" class="input-xlarge" size="50" name="validation_rules" value="<?php echo $this->item->validation_rules ; ?>" />
			</td>
			<td>
				<?php echo JText::_('EB_VALIDATION_RULES_EXPLAIN'); ?>
			</td>
		</tr>						
		<tr class="eb-field eb-list eb-checkboxes eb-radio">
			<td class="key">
				<?php echo JText::_('EB_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="values" class="input-xlarge"><?php echo $this->item->values; ?></textarea>
			</td>
			<td>
				<?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('EB_DEFAULT_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="default_values" class="input-xlarge"><?php echo $this->item->default_values; ?></textarea>
			</td>
			<td>
				<?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
			</td>
		</tr>	
		<tr>
			<td class="key"><?php echo JText::_('EB_FEE_FIELD') ; ?></td>
			<td>
				<?php echo $this->lists['fee_field']; ?>
			</td>
			<td>
				&nbsp;
			</td>			
		</tr>	
		<tr class="eb-field eb-list eb-checkboxes eb-radio">
			<td class="key">
				<?php echo JText::_('EB_FEE_VALUES'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="fee_values" class="input-xlarge"><?php echo $this->item->fee_values; ?></textarea>
			</td>
			<td>
				 <?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
			</td>
		</tr>			
		<tr>
			<td class="key">
				<?php echo JText::_('EB_FEE_FORMULA') ; ?>
			</td>
			<td>
				<input type="text" class="inputbox" size="50" name="fee_formula" value="<?php echo $this->item->fee_formula ; ?>" />
			</td>
			<td>
				<?php echo JText::_('EB_FEE_FORMULA_EXPLAIN'); ?>
			</td>
		</tr>

		<tr class="eb-field eb-list eb-radio eb-checkboxes">
			<td class="key">
				<?php echo JText::_('EB_QUANTITY_FIELD'); ?>
			</td>
			<td>
				<?php echo $this->lists['quantity_field'];?>
			</td>
			<td>
				<?php echo JText::_('EB_QUANTITY_FIELD_EXPLAIN'); ?>
			</td>
		</tr>
		<tr class="eb-field eb-list eb-radio eb-checkboxes">
			<td class="key">
				<?php echo JText::_('EB_QUANITY_VALUES') ; ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="quantity_values" class="input-xlarge"><?php echo $this->item->quantity_values; ?></textarea>
			</td>
			<td>
				<?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<?php echo  JText::_('EB_DESCRIPTION'); ?>
			</td>
			<td>
				<textarea rows="5" cols="50" name="description" class="input-xlarge"><?php echo $this->item->description;?></textarea>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
		<tr>
            <td class="key">
                <?php echo JText::_('EB_ACCESS'); ?>
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
				<?php echo JText::_('EB_PUBLISHED'); ?>
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
				<?php echo  JText::_('EB_CSS_CLASS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="css_class" id="css_class" size="10" maxlength="250" value="<?php echo $this->item->css_class;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="eb-field eb-text eb-textarea">
			<td class="key">
				<?php echo  JText::_('EB_PLACE_HOLDER'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="place_holder" id="place_holder" size="50" maxlength="250" value="<?php echo $this->item->place_holder;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="eb-field eb-text eb-checkboxes eb-radio eb-list">
			<td class="key">
				<?php echo  JText::_('EB_SIZE'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="size" id="size" size="10" maxlength="250" value="<?php echo $this->item->size;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>				
		<tr class="eb-field eb-text eb-textarea">
			<td class="key">
				<?php echo  JText::_('EB_MAX_LENGTH'); ?>				
			</td>
			<td>
				<input class="text_area" type="text" name="max_length" id="max_lenth" size="50" maxlength="250" value="<?php echo $this->item->max_length;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr	class="eb-field eb-textarea">
			<td class="key">
				<?php echo  JText::_('EB_ROWS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="rows" id="rows" size="10" maxlength="250" value="<?php echo $this->item->rows;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr class="eb-field eb-textarea">
			<td class="key">
				<?php echo  JText::_('EB_COLS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="cols" id="cols" size="10" maxlength="250" value="<?php echo $this->item->cols;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_EXTRA'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="extra_attributes" id="extra" size="40" maxlength="250" value="<?php echo $this->item->extra_attributes;?>" />
			</td>
			<td>
				&nbsp;
			</td>
		</tr>	
		<tr>
			<td class="key">
				<?php echo JText::_('EB_DISPLAY_IN'); ?>
			</td>
			<td>
				<?php echo $this->lists['display_in']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>

		<tr>
			<td class="key">
				<?php echo JText::_('EB_ONLY_SHOW_FOR_FIRST_GROUP_MEMBER'); ?>
			</td>
			<td>
				<?php echo $this->lists['only_show_for_first_member']; ?>
			</td>
			<td>
				<?php echo JText::_('EB_ONLY_SHOW_FOR_FIRST_GROUP_MEMBER_EXPLAIN'); ?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<?php echo JText::_('EB_ONLY_REQUIRE_FOR_FIRST_GROUP_MEMBER'); ?>
			</td>
			<td>
				<?php echo $this->lists['only_require_for_first_member']; ?>
			</td>
			<td>
				<?php echo JText::_('EB_ONLY_REQUIRE_FOR_FIRST_GROUP_MEMBER_EXPLAIN'); ?>
			</td>
		</tr>

		<!--<tr class="validation-rules">
			<td class="key">
				<?php echo JText::_('EB_VALIDATION_ERROR_MESSAGE') ; ?>
			</td>
			<td>
				<input type="text" class="inputbox" size="50" name="validation_error_message" value="<?php echo $this->item->validation_error_message ; ?>" />
			</td>
			<td>
				<?php echo JText::_('EB_VALIDATION_ERROR_MESSAGE_EXPLAIN'); ?>
			</td>
		</tr>
		 -->						
		<?php	
			if ($this->integration) 
			{
			?>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_FIELD_MAPPING') ; ?>
					</td>
					<td>
						<?php echo $this->lists['field_mapping'] ; ?>						
					</td>
					<td>
						<?php echo JText::_('EB_FIELD_MAPPING_EXPLAIN'); ?> 
					</td>
				</tr>
			<?php	
			}
		?>
        <tr>
            <td class="key">
                <?php echo JText::_('EB_DEPEND_ON_FIELD');?>
            </td>
            <td colspan="2">
                <?php echo $this->lists['depend_on_field_id']; ?>
            </td>
        </tr>
        <tr id="depend_on_options_container" style="display: <?php echo $this->item->depend_on_field_id ? '' : 'none'; ?>">
            <td class="key">
                <?php echo JText::_('EB_DEPEND_ON_OPTIONS');?>
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
								<?php echo  JText::_('EB_TITLE'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="title_<?php echo $sef; ?>" id="title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'title_'.$sef}; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_DESCRIPTION'); ?>
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
								<?php echo JText::_('EB_VALUES'); ?>
							</td>
							<td>
								<textarea rows="5" cols="50" name="values_<?php echo $sef; ?>"><?php echo $this->item->{'values_'.$sef}; ?></textarea>
							</td>
							<td>
								<?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('EB_DEFAULT_VALUES'); ?>
							</td>
							<td>
								<textarea rows="5" cols="50" name="default_values_<?php echo $sef; ?>"><?php echo $this->item->{'default_values_'.$sef}; ?></textarea>
							</td>
							<td>
								<?php echo JText::_('EB_EACH_ITEM_LINE'); ?>
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
        var siteUrl = "<?php echo JUri::base(); ?>";

        (function($)
        {
			$(document).ready(function(){				
				var validateEngine = <?php  echo EventbookingHelper::validateEngine(); ?>;
				$("input[name='required']").bind( "click", function() {
					var change = 1;
					validateRules(change);
				});
				$( "#datatype_validation" ).bind( "change", function() {	
					var change = 1;				
					validateRules(change);
				});

				$( "#fieldtype" ).bind( "change", function() {
						changeFiledType($(this).val());
				});
				
				changeFiledType('<?php echo $this->item->fieldtype;  ?>');
				function validateRules(change)
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
					if(change == 1)
					{																
						$("input[name='validation_rules']").val(validationString);
					}
				}			
				validateRules();	
				function changeFiledType(fieldType)
				{			
					if (fieldType == '')
					{
						$('tr.eb-field').hide();
					}
					else 
					{
						var cssClass = '.eb-' + fieldType.toLowerCase();	
						$('tr.eb-field').show();
						$('tr.eb-field').not(cssClass).hide();
					}																												
				}
			});
		})(jQuery);
        
		function checkFieldName() 
		{
			var form = document.adminForm ;
			var name = form.name.value ;
			var oldValue = name ;			
			name = name.replace('eb_','');			
			name = name.replace(/[^a-zA-Z0-9_]*/ig, '');
			form.name.value = name;							
		}

        function updateDependOnOptions()
        {
            (function($) {
                var fieldId = $('#depend_on_field_id').val();
                if (fieldId > 0) {
                    $.ajax({
                        type: 'POST',
                        url: siteUrl + 'index.php?option=com_eventbooking&view=field&format=raw&field_id=' + fieldId,
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
            })(jQuery);
        }
	</script>
</form>