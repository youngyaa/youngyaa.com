<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$selectedState = '';
$bootstrapHelper = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
?>
<script type="text/javascript">
	var siteUrl = '<?php echo OSMembershipHelper::getSiteUrl();  ?>';
</script>
<?php
OSMembershipHelperJquery::validateForm();
?>
<form method="post" name="os_form" id="os_form" action="<?php echo JRoute::_('index.php?option=com_osmembership&task=groupmember.save&Itemid='.$this->Itemid, false, 0); ?>" enctype="multipart/form-data" autocomplete="off" class="form form-horizontal">
<?php
	if ($this->item->id)
	{
	?>
		<h1 class="osm-page-title"><?php echo JText::_('OSM_EDIT_GROUP_MEMBER'); ?></h1>
	<?php
	}
	else
	{
	?>
		<h1 class="osm-page-title"><?php echo JText::_('OSM_NEW_GROUP_MEMBER'); ?></h1>
	<?php
	}
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="plan_id">
			<?php echo  JText::_('OSM_PLAN') ?>
			<span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php
				if (isset($this->plan))
				{
					echo $this->plan->title;
				}
				else
				{
					echo $this->lists['plan_id'];
				}
			?>
		</div>
	</div>
	<?php
		if (!$this->item->id)
		{
			$params = JComponentHelper::getParams('com_users');
			$minimumLength = $params->get('minimum_length', 4);
			($minimumLength) ? $minSize = ",minSize[$minimumLength]" : $minSize = "";
			if(version_compare(JVERSION, '3.1.2', 'ge'))
			{
				$passwordValidation = ',ajax[ajaxValidatePassword]';
			}
			else
			{
				$passwordValidation = '';
			}
			if ($this->showExistingUsers)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>" for="username1">
						<?php echo  JText::_('OSM_EXISTING_GROUP_MEMBER') ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo $this->lists['user_id']; ?>
					</div>
				</div>
			<?php
			}
		?>
			<div class="member-existing <?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="username1">
					<?php echo  JText::_('OSM_USERNAME') ?><span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="username" id="username1" class="validate[required,ajax[ajaxUserCall]]" value="<?php echo JRequest::getVar('username', null,'post'); ?>" size="15" autocomplete="off"/>
				</div>
			</div>
			<div class="member-existing <?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="password1">
					<?php echo  JText::_('OSM_PASSWORD') ?>
					<span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input value="" class="validate[required<?php echo $minSize.$passwordValidation;?>] text-input osm_inputbox inputbox" type="password" name="password1" id="password1" autocomplete="off"/>
				</div>
			</div>
			<div class="member-existing <?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>" for="password2">
					<?php echo  JText::_('OSM_RETYPE_PASSWORD') ?>
					<span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input value="" class="validate[required,equals[password1]] text-input osm_inputbox inputbox" type="password" name="password2" id="password2" />
				</div>
			</div>
		<?php
		}
		$fields = $this->form->getFields();
		if (isset($fields['state']))
		{
			$selectedState = $fields['state']->value;
		}
		if (isset($fields['email']))
		{
			$emailField = $fields['email'];
			$cssClass = $emailField->getAttribute('class');
			if ($this->item->id)
			{
				// No validation
				$cssClass = str_replace(',ajax[ajaxEmailCall]', '', $cssClass);
			}
			else
			{
				$cssClass = str_replace('ajax[ajaxEmailCall]', 'ajax[ajaxValidateGroupMemberEmail]', $cssClass);
			}
			$emailField->setAttribute('class', $cssClass);
		}
		foreach ($fields as $field)
		{
			echo $field->getControlGroup($bootstrapHelper);
		}
	?>
	<div class="form-actions">
		<input type="submit" class="<?php echo $btnClass; ?> btn-primary" name="btnSubmit" id="btn-submit" value="<?php echo  JText::_('OSM_SAVE_MEMBER') ;?>">
		<img id="ajax-loading-animation" src="<?php echo JUri::base();?>media/com_osmembership/ajax-loadding-animation.gif" style="display: none;"/>
	</div>
<div class="clearfix"></div>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" id="member_id" value="<?php echo $this->item->id; ?>" />
	<?php
	if (isset($this->plan))
	{
	?>
		<input type="hidden" name="plan_id" value="<?php echo $this->plan->id; ?>" />
	<?php
	}
	?>
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		OSM.jQuery(function($){
			$(document).ready(function(){
				OSMVALIDATEFORM("#os_form");
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
				$('#user_id').change(function(){
					changeGroupMember($(this).val())
					populateGroupMemberData($(this).val(),$('#plan_id').val());
				})
			})
			changeGroupMember = (function(userId){
				if(userId == 0)
				{
					$('.member-existing').slideDown('slow');
					// Clear input data
					$('#os_form').find("input[type=text], input[type=password], textarea").val("");
				}
				else
				{
					$('.member-existing').slideUp('slow');
				}
			})
			populateGroupMemberData = (function(id, planId){
				$.ajax({
					type : 'POST',
					url : 'index.php?option=com_osmembership&task=groupmember.get_member_data&user_id=' + id + '&plan_id=' +planId,
					dataType: 'json',
					success : function(json){
						var selecteds = [];
						for (var field in json)
						{
							value = json[field];
							if ($("input[name='" + field + "[]']").length)
							{
								//This is a checkbox or multiple select
								if ($.isArray(value))
								{
									selecteds = value;
								}
								else
								{
									selecteds.push(value);
								}
								$("input[name='" + field + "[]']").val(selecteds);
							}
							else if ($("input[type='radio'][name='" + field + "']").length)
							{
								$("input[name="+field+"][value=" + value + "]").attr('checked', 'checked');
							}
							else
							{
								$('#' + field).val(value);
							}
						}
						if (id == 0 && $('#member_id').val() == 0)
						{
							$('#email').attr('class','class="validate[required,custom[email],ajax[ajaxValidateGroupMemberEmail]]"').removeAttr('readonly')
						}
						else
						{
							$('#email').removeAttr('class').attr('readonly','readonly');
						}
					}
				})
			});
		});
	</script>
</form>