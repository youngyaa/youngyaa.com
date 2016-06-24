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
	function removelang(lang) {
		if(!confirm('<?php echo JText::_('ALERT_CONFIRM_REMOVE_LANGUAGE', true) ?>')) {
			return false;
		}
		var form = document.getElementById('adminForm');
		form.lang_remove.value = lang;
		Joomla.submitbutton('tool.remove_language');
	}
	Joomla.submitbutton = function(task)
	{
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

			<h3><?php echo JText::_('DELETE_LANGUAGE_CONTENT') ?></h3>
			<div class="warning">
				<?php echo JText::_('DELETE_LANGUAGE_DESCRIPTION') ?>
				<br/>
				<strong><?php echo JText::_('COMPONENT').':'; ?></strong><br/>
				<?php echo JText::_('WHICH_COMPONENT_WILL_BEING_REMOVED_ITEMS') ?>
				<a href="#" onclick="jQuery('#component-list').toggle(300);" title="<?php echo JText::_('JHIDE'); ?>"><?php echo JText::_('JHIDE'); ?></a>
				<div id="component-list" style="">
					<ol>
						<?php foreach($this->adapters as $itemtype => $props): ?>
							<li><?php echo $props['title']; ?></li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>

			<table class="table table-striped adminlist vertical" id="ja-table-form">
				<thead>
				<tr>
					<th class="hidden-phone"><?php echo JText::_('LANGUAGE'); ?></th>
					<th class="hidden-phone">&nbsp;</th>
				</tr>
				</thead>
				<tbody>

				<?php foreach($languages as $lang): ?>
					<?php
					$manifest = json_decode($lang->manifest_cache);
					if($lang->element == $defaultLanguage->element) continue;
					?>
					<tr>
						<td class="nowrap hidden-phone">
							<?php echo (is_object($manifest) ? $manifest->name : $lang->name).' ('.$lang->element.')'; ?>
						</td>
						<td class="hidden-phone">
							<button class="btn btn-small" onclick="return removelang('<?php echo $lang->element; ?>');"><?php echo JText::_('JALANG_ACTION_REMOVE'); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>

				</tbody>
			</table>

			<input type="hidden" name="lang_remove" value="" />
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