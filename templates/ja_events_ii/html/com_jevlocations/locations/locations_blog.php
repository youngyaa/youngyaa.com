<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: locations.php 3335 2012-03-14 10:42:05Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
if (!$this->loadedFromTemplate('com_jevlocations.locations.bloglist', "bloglist"))
{
	$compparams = JComponentHelper::getParams("com_jevlocations");
	$usecats = $compparams->get("usecats", 0);

	$mediaparams = JComponentHelper::getParams('com_media');
	$mediabase = JURI::root() . $mediaparams->get('image_path', 'images/stories');
	// folder relative to media folder
	$folder = "jevents/jevlocations";
	$Itemid = JRequest::getInt("Itemid");
	$targetid = intval($compparams->get("targetmenu", 0));
	if ($targetid > 0)
	{
		$menu = JFactory::getApplication()->getMenu();
		$targetmenu = $menu->getItem($targetid);
		if ($targetmenu->component != "com_jevents")
		{
			$targetid = JEVHelper::getItemid();
		}
		else
		{
			$targetid = $targetmenu->id;
		}
	}
	else
	{
		$targetid = JEVHelper::getItemid();
	}
	$task = $compparams->get("jevview", "month.calendar");
	if ($compparams->get('showlocationlatestevents', 0))
	{
		require_once (JPATH_SITE . "/modules/mod_jevents_latest/helper.php");
		$jevhelper = new modJeventsLatestHelper();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JPluginHelper::importPlugin("jevents");
		$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest', $theme . "/" . "latest", $compparams);
		// record what is running - used by the filters
		$registry = JRegistry::getInstance("jevents");
		$registry->set("jevents.activeprocess", "mod_jevents_latest");
		$registry->set("jevents.moduleid", "cb");
	}
	?>

	<?php
	$app = JFactory::getApplication('site');
	$params = $app->getParams();
	$active = $app->getMenu()->getActive();
	if ($active)
	{
		$params->merge($active->params);
	}
	if ($params->get('show_page_heading', 0))
	{
		?>
		<h1>
			<?php echo $this->escape($params->get('page_heading', $params->get('page_title', $active ? $active->title : ""))); ?>
		</h1>
			<?php } ?>


	<form action="<?php echo JRoute::_("index.php?option=com_jevlocations&task=locations.locations&layout=locations_blog&Itemid=$Itemid"); ?>" method="post" name="adminForm">
			<?php if ($compparams->get("showfilters", 1))
			{ ?>
		    <div class="jevloc-category-filter"><ul>
		    <li>
				<label><?php echo JText::_('COM_JEVLOCATIONS_FILTER'); ?>:</label>
				<input class="inputbox" type="text" name="search" id="jevsearch" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				</li>
				<?php
					$cities = false;
				?>
				<li><label><?php echo JText::_('COM_JEVLOCATIONS_CATEGORY'); ?>:</label><?php echo $this->lists['loccat']; ?></li>
				<li>
				<label>&nbsp;</label>
				<button class="btn btn-default" onclick="document.getElementById('jevsearch').value = '';
						this.form.getElementById('filter_loccat').value = '0';
						<?php if ($cities) { ?> this.form.getElementById('loccity_fv').value=''; <?php } ?>
						this.form.submit();"><?php echo JText::_('COM_JEVLOCATIONS_RESET'); ?></button>
				<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_('COM_JEVLOCATIONS_GO'); ?></button>
				</li>
		    </ul></div>
		<?php } ?>
		<div class="row equal-height equal-height-child">
		<?php
		$k = 0;
		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$row = &$this->items[$i];
			$tmpl = "";
			if (JRequest::getString("tmpl", "") == "component")
			{
				$tmpl = "&tmpl=component";
			}

			$link = JRoute::_('index.php?option=com_jevlocations&task=locations.detail&loc_id=' . $row->loc_id . $tmpl . "&se=1" . "&title=" . JApplication::stringURLSafe($row->title));
			$targetmenu = $row->targetmenu > 0 ? $row->targetmenu : $targetid;
			$eventslink = JRoute::_("index.php?option=com_jevents&task=$task&loclkup_fv=" . $row->loc_id . "&Itemid=" . $targetmenu);

			// global list
			$global = $this->_globalHTML($row, $i);

			if ($this->usecats)
			{
				if (isset($row->c3title))
				{
					$country = $row->c3title;
					$province = $row->c2title;
					$city = $row->c1title;
				}
				else if (isset($row->c2title))
				{
					$country = $row->c2title;
					$province = $row->c1title;
					$city = false;
				}
				else
				{
					$country = $row->c1title;
					$province = false;
					$city = false;
				}
			}
			else
			{
				$country = $row->country;
				$province = $row->state;
				$city = $row->city;
			}
			?>

		    <div class="jevloc-container col"><div class="inner">
		<?php if ($compparams->get('showimage', 1))
		{ ?>
			<?php
			if ($row->image != "")
			{
				$thimg = '<img class="jevloc-bloglayout-image" src="' . $mediabase . '/' . $folder . '/thumbnails/thumb_' . $row->image . '" />';
				?>
						<span class="jevloc-image editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_LOCATION'); ?>::<?php echo $this->escape($row->title); ?>">
							<a href="<?php echo $link; ?>"><?php echo $thimg; ?></a>
						</span>
				<?php
			}
			?>
					<?php } ?>
		        <h3>
		            <span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_LOCATION'); ?>::<?php echo $this->escape($row->title); ?>">
						<a href="<?php echo $link; ?>"><?php echo $this->escape($row->title); ?></a>
					</span>
				</h3>

					<?php if ($row->hasEvents)
					{ ?>
			        <div class="jevloc-field"><strong><?php echo JText::_('COM_JEVLOCATIONS_LOCATION_EVENTS'); ?></strong>:
						<?php if (!$compparams->get('showlocationlatestevents', 0)): ?>
				            <span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_EVENTS_AT'); ?>::<?php echo $this->escape($row->title); ?>">
								<a href="<?php echo $eventslink; ?>"><img src="<?php echo JURI::base(); ?>components/com_jevlocations/assets/images/jevents_event_sml.png" alt="Calendar" style="height:24px;margin:0px;"/></a>
				            </span>
						<?php else: ?>
							<?php
							$loclkup_fv = JRequest::setVar("loclkup_fv", $row->loc_id);
							$compparams->set("extras0", "jevl:" . $row->loc_id);
							$compparams->set("target_itemid", $targetmenu);
							$registry->set("jevents.moduleparams", $compparams);
							$modview = new $viewclass($compparams, 0);
							echo $modview->displayLatestEvents();
							JRequest::setVar("loclkup_fv", $loclkup_fv);

							echo "<br style='clear:both'/>";

							$task = $compparams->get("jevview", "month.calendar");
							$link = JRoute::_("index.php?option=com_jevents&task=$task&loclkup_fv=" . $row->loc_id . "&Itemid=" . $targetmenu);

							echo "<strong>" . JText::sprintf("COM_JEVLOCATIONS_ALL_EVENTS", $link) . "</strong>";
							?>
			<?php endif; ?>
			        </div>
		<?php } ?>
		        <div class="jevloc-field"><strong><?php echo JText::_('COM_JEVLOCATIONS_COUNTRY'); ?></strong>:
					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_LOCATION'); ?>::<?php echo $this->escape($row->title); ?>">
						<a href="<?php echo $link; ?>"><?php echo $this->escape($country); ?></a>
					</span>
				</div>

		        <div class="jevloc-field"><strong><?php echo JText::_('COM_JEVLOCATIONS_STATE'); ?></strong>:
					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_LOCATION'); ?>::<?php echo $this->escape($row->title); ?>">
						<a href="<?php echo $link; ?>"><?php echo $this->escape($province); ?></a>
					</span>
				</div>

				<div class="jevloc-field"><strong><?php echo JText::_('COM_JEVLOCATIONS_CITY'); ?></strong>:
					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEVLOCATIONS_VIEW_LOCATION'); ?>::<?php echo $this->escape($row->title); ?>">
						<a href="<?php echo $link; ?>"><?php echo $this->escape($city); ?></a>
				</div>
				</span>
		    </div></div> <!--  End Container -->
			<?php
			$k = 1 - $k;
		}
		?>
			<!--  We need to clear to keep within template cells -->
		</div> <!--  End Row -->

	<?php
//We set the layout to locations to use the same locations_map template
	$this->setLayout("locations");
	?>
		<?php if ($compparams->get("showmap", 0)) echo $this->loadTemplate("map"); ?>
		<?php
//We set the layout to locations to use the same locations_map template
		$this->setLayout("locations_blog");
		?>
		<input type="hidden" name="limitstart" value="0"/>
		<input type="hidden" name="option" value="com_jevlocations" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
		<input type="hidden" name="task" value="locations.locations" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php if (JRequest::getString("tmpl", "") == "component")
	{ ?>
			<input type="hidden" name="tmpl" value="component" />
	<?php } ?>
	<?php echo JHTML::_('form.token'); ?>
	<?php if($this->pagination->pagesCurrent > 1) : ?>
		<div style="width:100%" class="jevpagination">
			<?php echo $this->pagination->getListFooter(); ?>
		</div>
	<?php endif; ?>
	</form>
<?php } ?>