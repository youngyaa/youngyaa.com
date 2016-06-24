<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$selectedState = '';
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			Joomla.submitform(pressbutton, form);
			return;
		}
		else
		{
			//Validate the entered data before submitting
			if (form.plan_id.value == 0)
			{
				alert("<?php echo JText::_('OSM_PLEASE_SELECT_PLAN'); ?>");
				return;
			}

			if (form.group_admin_id.value == 0)
			{
				alert("<?php echo JText::_('OSM_PLEASE_SELECT_GROUP'); ?>");
				return;
			}

			if (form.user_id.value == 0)
			{
				// Require user to enter username and password
				if (form.username.value == '')
				{
					alert("<?php echo JText::_('OSM_PLEASE_ENTER_USERNAME'); ?>");
					return;
				}

				if (form.password.value == '')
				{
					alert("<?php echo JText::_('OSM_PLEASE_ENTER_PASSWORD'); ?>");
					return;
				}
			}
			Joomla.submitform(pressbutton, form);
		}
	}
</script>
<form action="index.php?option=com_osmembership&view=groupmember" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">
<div class="row-fluid" style="float:left">
<table class="admintable adminform">
<tr>
	<td class="key">
		<?php echo JText::_('OSM_PLAN'); ?>
	</td>
	<td>
		<?php echo $this->lists['plan_id'] ; ?>
	</td>
</tr>
<tr>
	<td class="key">
		<?php echo JText::_('OSM_GROUP'); ?>
	</td>
	<td id="group_admin_container">
		<?php echo $this->lists['group_admin_id'] ; ?>
	</td>
</tr>
<?php
if (!$this->item->id)
{
?>
	<tr id="username_container">
		<td class="key">
			<?php echo JText::_('OSM_USERNAME'); ?>
		</td>
		<td>
			<input type="text" id="username" name="username" size="20" value="" />
			<?php echo JText::_('OSM_USERNAME_EXPLAIN'); ?>
		</td>
	</tr>
	<tr id="password_container">
		<td class="key">
			<?php echo JText::_('OSM_PASSWORD'); ?>
		</td>
		<td>
			<input type="password" id="password" name="password" size="20" value="" />
		</td>
	</tr>
<?php
}
?>
<tr>
	<td class="key">
		<?php echo JText::_('OSM_USER'); ?>
	</td>
	<td>
		<?php echo OSMembershipHelper::getUserInput($this->item->user_id, (int) $this->item->id) ; ?>
	</td>
</tr>
<?php
$fields = $this->form->getFields();
if (isset($fields['state']))
{
	if ($fields['state']->type == 'State')
	{
		$stateType = 1;
	}
	else
	{
		$stateType = 0;
	}
	$selectedState = $fields['state']->value;
}
foreach ($fields as $field)
{
	switch (strtolower($field->type))
	{
		case 'heading' :
			?>
			<tr><td colspan="2"><h3 class="osm-heading"><?php echo JText::_($field->title) ; ?></h3></td></tr>
			<?php
			break ;
		case 'message' :
			?>
			<tr>
				<td colspan="2">
					<p class="osm-message">
						<?php echo $field->description ; ?>
					</p>
				</td>
			</tr>
			<?php
			break ;
		default:
			?>
				<tr id="field_<?php echo $field->name; ?>">
					<td class="key">
						<?php echo JText::_($field->title); ?>
					</td>
					<td class="controls">
						<?php echo $field->input; ?>
					</td>
				</tr>
			<?php
			break;
	}
}

if ($this->item->id)
{
?>
	<tr>
		<td class="key">
			<?php echo  JText::_('OSM_CREATED_DATE'); ?>
		</td>
		<td>
			<?php echo JHtml::_('calendar', $this->item->created_date, 'created_date', 'created_date') ; ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<?php echo  JText::_('OSM_SUBSCRIPTION_START_DATE'); ?>
		</td>
		<td>
			<?php echo JHtml::_('calendar', $this->item->from_date, 'from_date', 'from_date') ; ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<?php echo  JText::_('OSM_SUBSCRIPTION_END_DATE'); ?>
		</td>
		<td>
			<?php
			if ($this->item->lifetime_membership || $this->item->to_date == '2099-12-31 23:59:59')
			{
				echo JText::_('OSM_LIFETIME');
			}
			else
			{
				echo JHtml::_('calendar', $this->item->to_date, 'to_date', 'to_date') ;
			}
			?>
		</td>
	</tr>
<?php
}
?>
</table>
</div>
<div class="clr"></div>
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" id="current_group_admin_id=<?php echo $this->item->group_admin_id; ?>" />
<?php echo JHtml::_( 'form.token' ); ?>
<script type="text/javascript">
	var siteUrl = "<?php echo JUri::root(); ?>";
	(function($){
		populateSubscriberData = (function(id, planId, title){
			$('#username_container').hide();
			$('#password_container').hide();
			$.ajax({
				type : 'POST',
				url : 'index.php?option=com_osmembership&task=get_profile_data&user_id=' + id + '&plan_id=' +planId,
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
					$('#user_id').val(id);
					$('#user_id_name').val(title);
				}
			})
		});

		buildGroupAdmin = (function(planId){
			var groupAdminId = $('#current_group_admin_id').val();
			$.ajax({
				type : 'POST',
				url : 'index.php?option=com_osmembership&view=groupmember&format=raw&group_admin_id=' + groupAdminId + '&plan_id=' +planId,
				success: function(data) {
					$('#group_admin_container').html(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(textStatus);
				}
			})
		});

		<?php
			if ($stateType)
			{
			?>
				function buildStateField(stateFieldId, countryFieldId, defaultState)
				{
					if($('#' + stateFieldId).length)
					{
						//set state
						if ($('#' + countryFieldId).length)
						{
							var countryName = $('#' + countryFieldId).val();
						}
						else
						{
							var countryName = '';
						}
						$.ajax({
							type: 'POST',
							url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
							success: function(data) {
								$('#field_' + stateFieldId + ' .controls').html(data);
							},
							error: function(jqXHR, textStatus, errorThrown) {
								alert(textStatus);
							}
						});
						//Bind onchange event to the country
						if ($('#' + countryFieldId).length)
						{
							$('#' + countryFieldId).change(function(){
								$.ajax({
									type: 'POST',
									url: siteUrl + 'index.php?option=com_osmembership&task=register.get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
									success: function(data) {
										$('#field_' + stateFieldId + ' .controls').html(data);
									},
									error: function(jqXHR, textStatus, errorThrown) {
										alert(textStatus);
									}
								});

							});
						}
					}//end check exits state
				}
			<?php
			}
		?>

		$(document).ready(function(){
			<?php
				if ($stateType)
				{
				?>
					buildStateField('state', 'country', '<?php echo $selectedState; ?>');
				<?php
				}
			?>
		});
	})(jQuery);
</script>
</form>