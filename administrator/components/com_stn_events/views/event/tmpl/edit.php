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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_stn_events/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'event.cancel') {
			Joomla.submitform(task, document.getElementById('event-form'));
		}
		else {
			
				js = jQuery.noConflict();
				if(js('#jform_eventimage').val() != ''){
					js('#jform_eventimage_hidden').val(js('#jform_eventimage').val());
				}
				if (js('#jform_eventimage').val() == '' && js('#jform_eventimage_hidden').val() == '') {
					alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
					return;
				}
				js = jQuery.noConflict();
				if(js('#jform_prizeimage').val() != ''){
					js('#jform_prizeimage_hidden').val(js('#jform_prizeimage').val());
				}
			if (task != 'event.cancel' && document.formvalidator.isValid(document.id('event-form'))) {
				
				Joomla.submitform(task, document.getElementById('event-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_stn_events&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="event-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_STN_EVENTS_TITLE_EVENT', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php if(empty($this->item->created_by)){ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

				<?php } ?>
				<?php if(empty($this->item->modified_by)){ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />

				<?php } ?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('desription'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('desription'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('eventimage'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('eventimage'); ?></div>
			</div>

				<?php if (!empty($this->item->eventimage)) : ?>
					<?php foreach ((array)$this->item->eventimage as $fileSingle) : ?>
						<?php if (!is_array($fileSingle)) : ?>
							<a href="<?php echo JRoute::_(JUri::root() . 'images/stnevents' . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> | 
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<input type="hidden" name="jform[eventimage][]" id="jform_eventimage_hidden" value="<?php echo implode(',', (array)$this->item->eventimage); ?>" />			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('startdate'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('startdate'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('starttime'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('starttime'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('enddate'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('enddate'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('endtime'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('endtime'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('grepinterval'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('grepinterval'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('prize'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('prize'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('prizeimage'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('prizeimage'); ?></div>
			</div>

				<?php if (!empty($this->item->prizeimage)) : ?>
					<?php foreach ((array)$this->item->prizeimage as $fileSingle) : ?>
						<?php if (!is_array($fileSingle)) : ?>
							<a href="<?php echo JRoute::_(JUri::root() . 'images/stnevents' . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> | 
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<input type="hidden" name="jform[prizeimage][]" id="jform_prizeimage_hidden" value="<?php echo implode(',', (array)$this->item->prizeimage); ?>" />			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('prizedescription'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('prizedescription'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('prizeprovider'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('prizeprovider'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('eventrules'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('eventrules'); ?></div>
			</div>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (JFactory::getUser()->authorise('core.admin','stn_events')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
