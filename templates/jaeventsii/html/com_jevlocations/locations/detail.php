<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
defined('_JEXEC' ) or die('Restricted access');

if (!$this->loadedFromTemplate('com_jevlocations.locations.detail', $this->location))
{
	// pass location through content plugins
	JPluginHelper::importPlugin('content');
	// pass location through jevents plugins
	JPluginHelper::importPlugin('jevents');
	$tmprow = new stdClass();
	$tmprow->text = $this->location->description;
	$params =new JRegistry(null);
	$dispatcher	= JDispatcher::getInstance();
	$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$tmprow, &$params, 0 ));
	$this->location->description = $tmprow->text ;
	$dispatcher->trigger('onLocationDisplay', array(&$this->location));

	?><div class="jevlocation-detail"><?php echo "<h3>" . $this->location->title . "</h3>" ?>

		<fieldset class="adminform jevlocation-description">
			<legend><?php echo JText::_('DESCRIPTION'); ?></legend>
			<div class="row">
			<?php
			$compparams = JComponentHelper::getParams("com_jevlocations");
			if ($this->location->image != "") { 
				echo '<div class="col-sm-6 col-md-5 jevlocation-image">';
				// Get the media component configuration settings
				$params = JComponentHelper::getParams('com_media');
				// Set the path definitions
				$mediapath = JURI::root(true) . '/' . $params->get('image_path', 'images/stories');

				// folder relative to media folder
				$locparams = JComponentHelper::getParams("com_jevlocations");
				$folder = "jevents/jevlocations";
				$thimg = '<img src="' . $mediapath . '/' . $folder . '/thumbnails/thumb_' . $this->location->image . '" />';
				$img = '<img src="' . $mediapath . '/' . $folder . '/' . $this->location->image . '" />';
				echo $thimg;
				echo '</div>';
			}

			echo '<div class="col-sm-6 col-md-7 jevlocation-address">';
			$usecats = $compparams->get("usecats", 0);
			echo '<p><i class="fa fa-location-arrow"></i>';
			if ($usecats)
			{
				echo $this->location->street . ", ";
				echo $this->location->category;
			}
			else
			{
				if (strlen($this->location->street) > 0)
					echo $this->location->street . ", ";
				if (strlen($this->location->city) > 0)
					echo $this->location->city . ", ";
				if (strlen($this->location->state) > 0)
					echo $this->location->state . ", ";
				if (strlen($this->location->postcode) > 0)
					echo $this->location->postcode . ", ";
				if (strlen($this->location->country) > 0)
					echo $this->location->country . ". </p>";
			}

			if (strlen($this->location->phone) > 0)
				echo '<p><i class="fa fa-phone"></i>'.$this->location->phone . "</p>";
			if (strlen($this->location->url) > 0)
			{
				$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
				if (strpos($this->location->url, "http://") === false)
					$this->location->url = "http://" . trim($this->location->url);
				$this->location->url = preg_replace('#(http://)(' . $pattern . '*)#i', '<a href="\\1\\2" target="_blank">\\1\\2</a>', $this->location->url);
				echo '<p><i class="fa fa-link"></i>'.$this->location->url . "</p>";
			}

			echo '</div>';

			echo '<div class="col-sm-12">';
			echo $this->location->description;
			echo '</div">';
			$compparams = JComponentHelper::getParams("com_jevlocations");
			$template = $compparams->get("fieldtemplate", "");
			if ($template != "")
			{
				$html = "";
				// New custom fields
				if (isset($this->location->customfields))
				{
					$customfields = $this->location->customfields;
				}
				else {
					$customfields = array();
				}

				$plugin = JPluginHelper::getPlugin('jevents', 'jevcustomfields' );
				$pluginparams = new JRegistry($plugin->params);

				$templatetop = $pluginparams->get("templatetop", "<table border='0'>");
				$templaterow = $pluginparams->get("templatebody", "<tr><td class='label'>{LABEL}</td><td>{VALUE}</td>");
				$templatebottom = $pluginparams->get("templatebottom", "</table>");

				$html = $templatetop;
				$user = JFactory::getUser();

				foreach ($customfields as $customfield)
				{

					if (version_compare(JVERSION, '1.6.0', '>='))
					{
						if (!in_array(intval($customfield["access"]), JEVHelper::getAid($user, 'array')))
							continue;
					}
					else
					{
						if ($user->aid < intval($customfield["access"]))
							continue;
					}
					if (!is_null($customfield["hiddenvalue"]) && trim($customfield["value"]) == $customfield["hiddenvalue"])
						continue;
					$outrow = str_replace("{LABEL}", $customfield["label"], $templaterow);
					$outrow = str_replace("{VALUE}", nl2br($customfield["value"]), $outrow);
					$html .= $outrow;
				}
				$html .= $templatebottom;

				echo $html;
			}
			?>
			</div>
		</fieldset>

		<fieldset class="adminform jevlocation-map">
			<legend><?php echo JText::_('Google_Map'); ?></legend>
			<?php echo JText::_('COM_JEVLOCATIONS_CLICK_MAP'); ?><br/><br/>
			<div id="gmap" style="width: 100%; height: 300px"></div>
		</fieldset>

		<?php
		if (JRequest::getInt("se", 0))
		{
			?>
			<fieldset class="adminform jevlocation-events">
				<legend><?php echo JText::_('COM_JEVLOCATIONS_UPCOMING_EVENTS'); ?></legend>
				<?php
				require_once (JPATH_SITE . "/modules/mod_jevents_latest/helper.php");

				$jevhelper = new modJeventsLatestHelper();
				$theme = JEV_CommonFunctions::getJEventsViewName();

				JPluginHelper::importPlugin("jevents");
				$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest', $theme ."/". "latest", $compparams);

				// record what is running - used by the filters
				$registry = JRegistry::getInstance("jevents");
				$registry->set("jevents.activeprocess", "mod_jevents_latest");
				$registry->set("jevents.moduleid", "cb");

				$menuitem = intval($compparams->get("targetmenu", 0));
				if ($this->location->targetmenu > 0)
				{
					$menuitem = $this->location->targetmenu;
				}
				if ($menuitem > 0)
				{
					$compparams->set("target_itemid", $menuitem);
				}
				// ensure we use these settings
				$compparams->set("modlatest_useLocalParam", 1);
				// disable link to main component
				$compparams->set("modlatest_LinkToCal", 0);
				$compparams->set("layout", $compparams->get("loclayout",""));

				$registry->set("jevents.moduleparams", $compparams);

				//we set parameters to show only this location
				$loclkup_fv = JRequest::setVar("loclkup_fv", $this->location->loc_id);
				$compparams->set("extras19","jevl:".$this->location->loc_id);

				//Display latest events
				$modview = new $viewclass($compparams, 0);
				echo $modview->displayLatestEvents();

				//We set loclkup parameter back to original state.
				JRequest::setVar("loclkup_fv", $loclkup_fv);
				$compparams->set("extras19","");

				JRequest::setVar("loclkup_fv", $loclkup_fv);

				$task = $compparams->get("jevview", "month.calendar");
				$link = JRoute::_("index.php?option=com_jevents&view=range&layout=listevents&Itemid=" . $menuitem);

				echo "<strong>" . JText::sprintf("COM_JEVLOCATIONS_ALL_EVENTS", $link) . "</strong>";
				?>
			</fieldset>
			<?php			
		}		
		if(property_exists($this->location, "_jcomments"))
		{
			echo $this->location->_jcomments;
		}
		?>
	</div>
	<?php
}