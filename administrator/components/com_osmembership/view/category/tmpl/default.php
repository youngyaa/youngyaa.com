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
$editor = JFactory::getEditor();
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
			//Validate the entered data before submitting
			if (form.title.value == '') {
				alert("<?php echo JText::_('OSM_ENTER_CATEGORY_TITLE'); ?>");
				form.title.focus();
				return ;
			}
																
			Joomla.submitform(pressbutton, form);
		}								
	}		
</script>
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
<form action="index.php?option=com_osmembership&view=category" method="post" name="adminForm" id="adminForm">
		<table class="admintable adminform" style="width: 100%;">
			<tr>
				<td width="220" class="key">
					<?php echo  JText::_('OSM_TITLE'); ?>
				</td>
				<td>
					<input class="text_area" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $this->item->title;?>" />
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
            <tr>
                <td width="220" class="key">
                    <?php echo  JText::_('OSM_ALIAS'); ?>
                </td>
                <td>
                    <input class="text_area" type="text" name="alias" id="alias" size="40" maxlength="250" value="<?php echo $this->item->alias;?>" />
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>

			<tr>
				<td width="220" class="key">
					<?php echo  JText::_('OSM_PARENT_CATEGORY'); ?>
				</td>
				<td>
					<?php echo OSMembershipHelperHtml::buildCategoryDropdown($this->item->parent_id); ?>
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
					<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '75', '10' ) ; ?>
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
					<?php echo JText::_('OSM_PUBLISHED'); ?>
				</td>
				<td>
					<?php echo $this->lists['published']; ?>
				</td>
				<td>
					&nbsp;
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
                                        <?php echo  JText::_('OSM_ALIAS'); ?>
                                    </td>
                                    <td>
                                        <input class="input-xlarge" type="text" name="alias_<?php echo $sef; ?>" id="alias_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'alias_'.$sef}; ?>" />
                                    </td>
                                </tr>
                                <tr>
									<td class="key">
										<?php echo JText::_('OSM_DESCRIPTION'); ?>
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
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_osmembership" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>
</div>
<div class="clearfix"></div>