<?php
/**
 * ------------------------------------------------------------------------
 * JA Multilingual Component for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

if(JalangHelper::isJoomla3x()) {
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	JHtml::_('formbehavior.chosen', 'select');
}

JHtml::_('behavior.modal', 'a.modal', array('fullScreen'=>true, 'onClose'=>'\\function(){ window.location.reload(); }'));

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');

$languages = JalangHelper::getListInstalledLanguages();

$defaultLanguage = JalangHelper::getLanguage();

$params = JComponentHelper::getParams('com_jalang');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'tool.find') {
			var form = jQuery('#adminForm');
			form.removeAttr('target');
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}

	function translate(itemtype) {
		var form = document.getElementById('adminForm');
		form.itemtype.value = itemtype;
		Joomla.submitbutton('tool.translate');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jalang&view=tool&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" target="ja-translation">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span5">
<?php else : ?>
	<div id="j-main-container" class="span7">
<?php endif;?>
		<div class="clearfix"> </div>

		<?php if(($params->get('translator_api_active', 'bing') == 'google' && $params->get('google_browser_api_key', '') == '') || ($params->get('translator_api_active', 'bing') == 'bing' && ($params->get('bing_client_id', '') == '' || $params->get('bing_client_secret', '') == ''))): ?>
			<div class="alert alert-danger">
				<?php echo JText::_('ALERT_COMPONENT_SETTING'); ?>
			</div>
		<?php endif; ?>

		<table class="table table-striped adminlist vertical" id="ja-table-form">
			<thead>
			<tr>
				<th class="nowrap hidden-phone" style="width: 100px;">
					<?php echo JText::_('FROM') ?>
					<!--<sup>[?]</sup>-->
				</th>
				<td class="hidden-phone">
					<strong><?php echo $defaultLanguage->name ; ?></strong>
					(<?php echo $defaultLanguage->element ; ?> -
					<a class="modal" rel="{handler: 'iframe'}" href="<?php echo JRoute::_('index.php?option=com_languages&view=installed&client=0'); ?>" target="_blank" title="<?php echo JText::_('CHANGE'); ?>"><?php echo JText::_('CHANGE'); ?></a>
					)
				</td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th class="nowrap hidden-phone">
					<?php echo JText::_('TO') ?>
					<!--<sup>[?]</sup>-->
				</th>
				<td class="hidden-phone">
					<ol>
						<?php foreach($languages as $lang): ?>
							<?php if($lang->element == $defaultLanguage->element) continue; ?>
							<li>
								<?php
								$manifest = json_decode($lang->manifest_cache);
								echo (is_object($manifest) ? $manifest->name : $lang->name).' ('.$lang->element.')';
								?>
							</li>
						<?php endforeach; ?>
					</ol>
					<a class="modal" href="<?php echo JRoute::_('index.php?option=com_installer&view=languages'); ?>" rel="{handler: 'iframe'}" title="<?php echo JText::_('INSTALL_MORE'); ?>">
						<span class="small"><?php echo JText::_('INSTALL_MORE'); ?></span>
					</a>
				</td>
			</tr>
			<tr>
				<th class="nowrap hidden-phone">
					<?php echo JText::_('COMPONENT') ?>
				</th>
				<td class="hidden-phone">
					<?php echo JText::_('WHICH_COMPONENT_WILL_BEING_TRANSLATED') ?>
					<a href="#" onclick="jQuery('#component-list').toggle(300);" title="<?php echo JText::_('JHIDE'); ?>"><?php echo JText::_('JHIDE'); ?></a>
					<div id="component-list" style="">
						<ol>
							<?php foreach($this->adapters as $itemtype => $props): ?>
							<li><?php echo $props['title']; ?></li>
							<?php endforeach; ?>
						</ol>
					</div>
				</td>
			</tr>
			<tr class="last">
				<td class="hidden-phone" colspan="2" style="text-align: center;">
					<button class="btn btn-large" onclick="Joomla.submitbutton('tool.translate_all');"><?php echo JText::_('TRANSLATE_ALL'); ?></button>
				</td>
			</tr>
			</tbody>
		</table>

		<input type="hidden" name="itemtype" value="" />
		<input type="hidden" name="task" value="tool.translate_all" />
		<input type="hidden" name="boxchecked" value="1" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

	<div class="span5">
		<fieldset class="adminform">
			<legend><?php echo JText::_('TRANSLATION_RESULT'); ?></legend>
			<iframe name="ja-translation" src="about:blank" style="width: 100%; height: 500px;" frameborder="0"></iframe>
		</fieldset>
	</div>
</form>
