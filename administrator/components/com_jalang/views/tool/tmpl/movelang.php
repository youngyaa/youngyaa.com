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

$input = JFactory::getApplication()->input;
if($input->get('debug', 0)) {
	$lang = $input->get('lang', '');
	if($lang) {
		$db = JFactory::getDbo();
		$query = "SELECT language FROM #__content WHERE `alias` LIKE '%-{$lang}'";
		$db->setQuery($query);
		$langtag = $db->loadResult();
		var_dump($langtag);
	}
}

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'tool.move_all') {
			if(!confirm('<?php echo JText::_('ALERT_CONFIRM_MOVE_ITEM', true) ?>')) {
				return false;
			}
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jalang&view=tool&layout=movelang&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" target="ja-translation">
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
					<td class="hidden-phone" colspan="2">
						<h3><?php echo JText::_('MOVE_LANGUAGE') ?></h3>
						<?php echo JText::_('MOVE_LANGUAGE_DESCRIPTION') ?>
					</td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th class="nowrap hidden-phone" style="width: 100px;">
						<?php echo JText::_('MOVE_FROM_LANGUAGE') ?>
						<!--<sup>[?]</sup>-->
					</th>
					<td class="hidden-phone">

						<select name="from_language" class="inputbox">
							<option value=""><?php echo JText::_('SELECT_A_SOURCE_LANGUAGE'); ?></option>
							<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', false, true), 'value', 'text', ''); ?>
						</select>
						<br />
						<?php echo JText::_('MOVE_FROM_LANGUAGE_TAG') ?>
						<br/>
						<input type="text" name="from_language_tag" class="inputbox" size="8" />
					</td>
				</tr>
				<tr>
					<th class="nowrap hidden-phone">
						<?php echo JText::_('MOVE_TO_LANGUAGE') ?>
						<!--<sup>[?]</sup>-->
					</th>
					<td class="hidden-phone">

						<select name="to_language" class="inputbox">
							<option value=""><?php echo JText::_('SELECT_A_DESTINATION_LANGUAGE'); ?></option>
							<?php foreach($languages as $lang): $manifest = json_decode($lang->manifest_cache); ?>
								<option value="<?php echo $lang->element; ?>">
									<?php echo (is_object($manifest) ? $manifest->name : $lang->name).' ('.$lang->element.')'; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="nowrap hidden-phone">
						<?php echo JText::_('COMPONENT') ?>
					</th>
					<td class="hidden-phone">
						<?php echo JText::_('WHICH_COMPONENT_WILL_BEING_MOVED_ITEMS') ?>
						<a href="#" onclick="jQuery('#component-list').toggle(300);" title="<?php echo JText::_('JHIDE'); ?>"><?php echo JText::_('JHIDE'); ?></a>
						<div id="component-list" style="">
							<ol>
								<?php foreach($this->adapters as $itemtype => $props): ?>
									<?php
									$adapter = JalangHelperContent::getInstance($props['name']);
									if(!$adapter || $adapter->table_type != 'native') continue;
									?>
									<li><?php echo $props['title']; ?></li>
								<?php endforeach; ?>
							</ol>
						</div>
					</td>
				</tr>
				<tr class="last">
					<td class="hidden-phone" colspan="2" style="text-align: center;">
						<button class="btn btn-large" onclick="return Joomla.submitbutton('tool.move_all');"><?php echo JText::_('MOVE_ALL'); ?></button>
					</td>
				</tr>
				</tbody>
			</table>

			<input type="hidden" name="itemtype" value="" />
			<input type="hidden" name="task" value="tool.move_all" />
			<?php echo JHtml::_('form.token'); ?>
		</div>

		<div class="span5">
			<fieldset class="adminform">
				<legend><?php echo JText::_('MOVE_LANGUAGE_RESULT'); ?></legend>
				<iframe name="ja-translation" src="about:blank" style="width: 100%; height: 500px;" frameborder="0"></iframe>
			</fieldset>
		</div>
</form>