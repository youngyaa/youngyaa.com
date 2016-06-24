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
$editor = JFactory::getEditor();
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			Joomla.submitform( pressbutton );
			return;				
		}
		else
		{
			<?php echo $editor->save('description'); ?>
			Joomla.submitform( pressbutton );
		}
	}
</script>
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
<form action="index.php?option=com_eventbooking&view=category" method="post" name="adminForm" id="adminForm">
	<table class="admintable adminform">
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_NAME'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="40" maxlength="250" value="<?php echo $this->item->name;?>" />
			</td>
		</tr>	
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_ALIAS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" maxlength="250" value="<?php echo $this->item->alias;?>" />
			</td>
		</tr>			
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_PARENT'); ?>
			</td>
			<td>
				<?php echo $this->lists['parent']; ?>	
			</td>				
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_LAYOUT'); ?>
			</td>
			<td>
				<?php echo $this->lists['layout']; ?>	
			</td>				
		</tr>	
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_ACCESS_LEVEL'); ?>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>	
			</td>				
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('EB_COLOR'); ?>
			</td>
			<td>
				<input type="text" name="color_code" class="inputbox color {required:false}" value="<?php echo $this->item->color_code; ?>" size="10" />						
				<?php echo JText::_('EB_COLOR_EXPLAIN'); ?> 
			</td>
		</tr>
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
		<tr>
			<td class="key">
				<?php echo JText::_('EB_DESCRIPTION'); ?>
			</td>
			<td>
				<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '75', '10' ) ; ?>
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
									<?php echo  JText::_('EB_NAME'); ?>
								</td>
								<td>
									<input class="input-xlarge" type="text" name="name_<?php echo $sef; ?>" id="name_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'name_'.$sef}; ?>" />
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
									<?php echo JText::_('EB_DESCRIPTION'); ?>
								</td>
								<td>
									<?php echo $editor->display( 'description_'.$sef,  $this->item->{'description_'.$sef} , '100%', '250', '75', '10' ) ; ?>
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
<div class="clearfix"></div>	
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
</form>
</div>