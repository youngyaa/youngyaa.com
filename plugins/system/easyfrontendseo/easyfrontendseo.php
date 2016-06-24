<?php
/**
 * EFSEO - Easy Frontend SEO for Joomal! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Project page: https://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class PlgSystemEasyFrontendSeo extends JPlugin
{
	protected $db;
	protected $url;
	protected $url_old;
	protected $url_tostring;
	protected $allowed_user_groups;
	protected $session;
	protected $request;

	function __construct(&$subject, $config)
	{
		// First check whether version requirements are met for this specific version
		if($this->checkVersionRequirements(false, '3.2', 'Easy Frontend SEO', 'plg_system_easyfrontendseo', JPATH_ADMINISTRATOR))
		{
			parent::__construct($subject, $config);
			$this->loadLanguage('', JPATH_ADMINISTRATOR);
		}
	}

	/**
	 * Sets needed object variables in the trigger onAfterInitialise to avoid triggering the framework too early
	 */
	public function onAfterInitialise()
	{
		$this->set('allowed_user_groups', $this->allowedUserGroups());
		$this->set('db', JFactory::getDbo());
		$this->set('session', JFactory::getSession());
		$this->set('request', JFactory::getApplication()->input);

		$uri = JUri::getInstance();
		$this->set('url_tostring', $uri->toString());

		// Compatibility Mode
		$compatibility = $this->params->get('compatibility');
		$relative_urls = $this->params->get('relative_urls');

		if($compatibility != 2)
		{
			$internal_url = $this->buildInternalUrl($uri);
		}

		if($compatibility == 0)
		{
			if(empty($relative_urls))
			{
				$this->set('url', $internal_url);
				$this->set('url_old', array($this->url_tostring, str_replace(JURI::base(), '', $this->url_tostring), str_replace(JURI::base(), '', $internal_url)));
			}
			else
			{
				$this->set('url', str_replace(JURI::base(), '', $internal_url));
				$this->set('url_old', array($this->url_tostring, str_replace(JURI::base(), '', $this->url_tostring), $internal_url));
			}
		}
		elseif($compatibility == 1)
		{
			if(empty($relative_urls))
			{
				$this->set('url', $this->url_tostring);
				$this->set('url_old', array($internal_url, str_replace(JURI::base(), '', $internal_url), str_replace(JURI::base(), '', $this->url_tostring)));
			}
			else
			{
				$this->set('url', str_replace(JURI::base(), '', $this->url_tostring));
				$this->set('url_old', array($internal_url, str_replace(JURI::base(), '', $internal_url), $this->url_tostring));
			}
		}
		elseif($compatibility == 2)
		{
			$this->set('url', $this->url_tostring);
		}

		// Save data to session because some components do a redirection and entered data get lost
		$data_saved_to_session = $this->session->get('save_data_to_session', null, 'easyfrontendseo');

		if($this->request->get('easyfrontendseo') AND $this->allowed_user_groups == true AND empty($data_saved_to_session))
		{
			$this->saveDataToSession();
		}

		if($this->params->get('update') == 1 AND $compatibility != 2)
		{
			$this->updateDatabase($this->url, $this->url_old);
		}
	}

	/**
	 * Saves and edits the metadata in the trigger onBeforeCompileHead
	 */
	public function onBeforeCompileHead()
	{
		$document = JFactory::getDocument();
		$head = $document->getHeadData();

		$data_saved_to_session = $this->session->get('save_data_to_session', null, 'easyfrontendseo');

		if(!empty($data_saved_to_session) AND $this->allowed_user_groups == true)
		{
			$delete = $this->session->get('delete', null, 'easyfrontendseo');

			if(!empty($delete) AND $this->params->get('field_delete') == 1)
			{
				$query = "DELETE FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($this->url);
				$this->db->setQuery($query);
				$this->db->execute();
			}
			else
			{
				$query = "SELECT * FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($this->url);
				$this->db->setQuery($query);
				$row = $this->db->loadAssoc();

				if($this->params->get('field_title') == 0 OR $this->params->get('field_title') == 2)
				{
					if(!empty($row['title']))
					{
						$title = $row['title'];
					}
					elseif(!empty($head['title']))
					{
						$title = $head['title'];
					}
					else
					{
						$title = '';
					}
				}
				else
				{
					$title = $this->session->get('title', null, 'easyfrontendseo');

					$characters_title = $this->getCharactersLength('characters_title');

					if(strlen($title) > $characters_title)
					{
						$title = mb_substr($title, 0, $characters_title);
					}
				}

				if($this->params->get('field_description') == 0 OR $this->params->get('field_description') == 2)
				{
					if(!empty($row['description']))
					{
						$description = $row['description'];
					}
					elseif(!empty($head['description']))
					{
						$description = $head['description'];
					}
					else
					{
						$description = '';
					}
				}
				else
				{
					$description = $this->session->get('description', null, 'easyfrontendseo');

					$characters_description = $this->getCharactersLength('characters_description');

					if(strlen($description) > $characters_description)
					{
						$description = mb_substr($description, 0, $characters_description);
					}
				}

				if($this->params->get('field_keywords') == 0 OR $this->params->get('field_keywords') == 2)
				{
					if(!empty($row['keywords']))
					{
						$keywords = $row['keywords'];
					}
					elseif(!empty($head['metaTags']['standard']['keywords']))
					{
						$keywords = $head['metaTags']['standard']['keywords'];
					}
					else
					{
						$keywords = '';
					}
				}
				else
				{
					$keywords = $this->session->get('keywords', null, 'easyfrontendseo');
				}

				if($this->params->get('field_generator') == 0 OR $this->params->get('field_generator') == 2)
				{
					if(!empty($row['generator']))
					{
						$generator = $row['generator'];
					}
					else
					{
						if($this->params->get('global_generator'))
						{
							$generator = $this->params->get('global_generator');
						}
						else
						{
							$generator = $document->getGenerator();
						}
					}
				}
				else
				{
					$generator = $this->session->get('generator', null, 'easyfrontendseo');
				}

				if($this->params->get('field_robots') == 0 OR $this->params->get('field_robots') == 2)
				{
					if($row['robots'] != '')
					{
						$robots = $row['robots'];
					}
					else
					{
						if(!empty($head['metaTags']['standard']['robots']))
						{
							$robots = $head['metaTags']['standard']['robots'];
						}
						else
						{
							$robots = $this->params->get('global_robots');
						}
					}
				}
				else
				{
					$robots = $this->session->get('robots', null, 'easyfrontendseo');
				}

				if(empty($row))
				{
					$query = "INSERT INTO ".$this->db->quoteName('#__plg_easyfrontendseo')." (".$this->db->quoteName('url').", ".$this->db->quoteName('title').", ".$this->db->quoteName('description').", ".$this->db->quoteName('keywords').", ".$this->db->quoteName('generator').", ".$this->db->quoteName('robots').") VALUES (".$this->db->quote($this->url).", ".$this->db->quote($title).", ".$this->db->quote($description).", ".$this->db->quote($keywords).", ".$this->db->quote($generator).", ".$this->db->quote($robots).")";
					$this->db->setQuery($query);
					$this->db->execute();
				}
				else
				{
					$query = "UPDATE ".$this->db->quoteName('#__plg_easyfrontendseo')." SET ".$this->db->quoteName('title')." = ".$this->db->quote($title).", ".$this->db->quoteName('description')." = ".$this->db->quote($description).", ".$this->db->quoteName('keywords')." = ".$this->db->quote($keywords).", ".$this->db->quoteName('generator')." = ".$this->db->quote($generator).", ".$this->db->quoteName('robots')." = ".$this->db->quote($robots)." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($this->url);
					$this->db->setQuery($query);
					$this->db->execute();
				}

				// Save data to core tables
				if($this->params->get('save_data_table_content') == 1 OR $this->params->get('save_data_table_menu') == 1)
				{
					if($this->params->get('save_data_table_content') == 1)
					{
						if($this->request->get('option') == 'com_content' AND $this->request->get('view') == 'article')
						{
							$this->saveDataToTableContent($description, $keywords);
						}
					}

					if($this->params->get('save_data_table_menu') > 0)
					{
						if($this->request->get('Itemid'))
						{
							$this->saveDataToTableMenu($title, $description, $keywords);
						}
					}
				}
			}

			// Delete stored data from the session
			$this->deleteDataFromSession();
		}

		$query = "SELECT * FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($this->url);
		$this->db->setQuery($query);
		$metadata = $this->db->loadAssoc();

		if(!empty($metadata))
		{
			$title = $metadata['title'];
			$description = $metadata['description'];
			$keywords = $metadata['keywords'];
			$generator = $metadata['generator'];
			$robots = $metadata['robots'];

			// Prepare array with new metadata
			$metadata_new = array('title' => $title, 'description' => $description, 'metaTags' => array('standard' => array('robots' => $robots, 'keywords' => $keywords)));

			if(isset($head['metaTags']['http-equiv']['content-type']))
			{
				$metadata_new['metaTags']['http-equiv'] = array('content-type' => $head['metaTags']['http-equiv']['content-type']);
			}

			if(isset($head['metaTags']['standard']['rights']))
			{
				$metadata_new['metaTags']['standard']['rights'] = $head['metaTags']['standard']['rights'];
			}

			$document->setHeadData($metadata_new);
			$document->setGenerator($generator);
		}

		// Automatic replacement for 3rd party extensions
		$this->automaticReplacement($metadata, $head, $document);

		// Set global title tag
		if($this->params->get('global_title'))
		{
			if(empty($metadata['title']))
			{
				$global_title = preg_replace(array('@\[S\]@', '@\[D\]@', '@\[Y\]@'), array(JFactory::getConfig()->get('sitename'), $head['title'], date('Y')), $this->params->get('global_title'));

				$document->setTitle($global_title);
			}
		}

		// Set global generator tag
		if($this->params->get('global_generator'))
		{
			if(empty($metadata['generator']))
			{
				$document->setGenerator($this->params->get('global_generator'));
			}
		}

		// Set global robots tag
		$global_robots = $this->params->get('global_robots');

		if(!empty($global_robots))
		{
			if(empty($metadata['robots']))
			{
				$document->setMetaData('robots', $global_robots);
			}
		}

		// Set custom metatag
		if($this->params->get('custom_metatags'))
		{
			$custom_metatags = array_map('trim', explode("\n", $this->params->get('custom_metatags')));

			foreach($custom_metatags as $custom_metatag)
			{
				if(!empty($custom_metatag))
				{
					if(preg_match('@\|@', $custom_metatag))
					{
						list($metatag, $value) = array_map('trim', explode('|', $custom_metatag));

						if(!empty($metatag) AND !empty($value))
						{
							$document->setMetaData($metatag, $value);
						}
					}
				}
			}
		}

		// Collect all URLs which are not saved already in the database
		if($this->params->get('collect_urls') AND empty($metadata))
		{
			// First check whether the loaded component is not excluded
			$exclude_component = $this->excludeComponents();

			if(empty($exclude_component))
			{
				// Reload the head data because they could be updated by the automatic mode
				$head = $document->getHeadData();

				$query = "INSERT INTO ".$this->db->quoteName('#__plg_easyfrontendseo')." (".$this->db->quoteName('url').", ".$this->db->quoteName('title').", ".$this->db->quoteName('description').", ".$this->db->quoteName('keywords').", ".$this->db->quoteName('generator').", ".$this->db->quoteName('robots').") VALUES (".$this->db->quote($this->url).", ".$this->db->quote($head['title']).", ".$this->db->quote($head['description']).", ".$this->db->quote($head['metaTags']['standard']['keywords']).", ".$this->db->quote($document->getGenerator()).", ".$this->db->quote($head['metaTags']['standard']['robots']).")";
				$this->db->setQuery($query);
				$this->db->execute();
			}
		}

		if($this->allowed_user_groups == true)
		{
			$document->addStyleSheet('plugins/system/easyfrontendseo/assets/css/easyfrontendseo.css', 'text/css');

			JHtml::_('behavior.framework');

			if($this->params->get('style') == 1)
			{
				$document->addScript('plugins/system/easyfrontendseo/assets/js/simplemodal.js', 'text/javascript');
				$document->addStyleSheet('plugins/system/easyfrontendseo/assets/css/simplemodal.css', 'text/css');
			}

			if($this->params->get('word_count') == 1)
			{
				$document->addScript('plugins/system/easyfrontendseo/assets/js/wordcount.js', 'text/javascript');
			}

			$js = '';

			if($this->params->get('style') == 0)
			{
				// Load the needed JavaScript for the output in the head section
				JHtml::_('jquery.framework');

				$js .= 'jQuery(document).ready(function()
                        {
                            jQuery("#easyfrontendseo").hide();
                            jQuery("#toggle").click(function() {
                                jQuery("#easyfrontendseo").slideToggle("slow");
                            });
                        });';
			}
			elseif($this->params->get('style') == 1)
			{
				// Load the needed JavaScript for the output in the head section
				JHtml::_('behavior.framework', 'more');

				$head = $this->currentHeadData($document);

				$js .= "window.addEvent('domready', function(e){
                                $('modal').addEvent('click', function(e){
                                e.stop();
                                var EFSEO = new SimpleModal({'width':600, 'height':400, 'offsetTop': 10,'onAppend':function(){".$this->counterCode()."}});
                                    EFSEO.addButton('".JText::_('PLG_EASYFRONTENDSEO_CANCEL')."', 'btn');
                                    EFSEO.show({
                                        'model':'modal',
                                        'title':'Easy Frontend SEO - Joomla!',
                                        'contents':'".$this->buildForm($head['title'], $head['description'], $head['keywords'], $head['generator'], $head['robots'])."'
                                    });
                                });
                            });";
			}

			$document->addScriptDeclaration($js, 'text/javascript');
		}
	}

	/**
	 * Builds the whole output in the onAfterRender trigger
	 */
	public function onAfterRender()
	{
		if($this->allowed_user_groups == true)
		{
			$document = JFactory::getDocument();

			if($document instanceof JDocumentHTML)
			{
				$head = $this->currentHeadData($document);

				$output = $this->buildButtons($head['title'], $head['description'], $head['keywords'], $head['generator'], $head['robots']);

				if($this->params->get('style') == 0)
				{
					$output .= $this->buildForm($head['title'], $head['description'], $head['keywords'], $head['generator'], $head['robots']);
				}

				$body = JFactory::getApplication()->getBody();
				$pattern = "@<body[^>]*>@";

				if(preg_match($pattern, $body, $matches))
				{
					$bodystart = $matches[0];
					$body = str_replace($bodystart, $bodystart.$output, $body);
					JFactory::getApplication()->setBody($body);
				}
			}
		}
	}

	/**
	 * Gets the current head data of the document
	 *
	 * @param object $document
	 *
	 * @return array
	 */
	private function currentHeadData($document)
	{
		$head = $document->getHeadData();
		$current_head_data = array();

		if(!empty($head['title']))
		{
			$current_head_data['title'] = htmlspecialchars($head['title'], ENT_COMPAT, 'UTF-8', false);
		}
		else
		{
			$current_head_data['title'] = '';
		}

		if(!empty($head['description']))
		{
			$current_head_data['description'] = htmlspecialchars($head['description'], ENT_COMPAT, 'UTF-8', false);
		}
		else
		{
			$current_head_data['description'] = '';
		}

		if(!empty($head['metaTags']['standard']['keywords']))
		{
			$current_head_data['keywords'] = htmlspecialchars($head['metaTags']['standard']['keywords'], ENT_COMPAT, 'UTF-8', false);
		}
		else
		{
			$current_head_data['keywords'] = '';
		}

		if(!empty($head['metaTags']['standard']['robots']))
		{
			$current_head_data['robots'] = htmlspecialchars($head['metaTags']['standard']['robots'], ENT_COMPAT, 'UTF-8', false);
		}
		else
		{
			$current_head_data['robots'] = '';
		}

		$current_head_data['generator'] = htmlspecialchars($document->getGenerator(), ENT_COMPAT, 'UTF-8', false);

		return $current_head_data;
	}

	/**
	 * Builds the buttons for the overview
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $generator
	 * @param string $robots
	 *
	 * @return string
	 */
	private function buildButtons($title, $description, $keywords, $generator, $robots)
	{
		$check = JURI::base().'plugins/system/easyfrontendseo/assets/images/check.png';
		$cross = JURI::base().'plugins/system/easyfrontendseo/assets/images/cross.png';

		$metacheck = '';

		if($this->params->get('icon_title') == 1)
		{
			if($title != '')
			{
				$metacheck .= '<img src="'.$check.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_TITLE').'" title="'.JText::_('PLG_EASYFRONTENDSEO_TITLE').'" />';
			}
			else
			{
				$metacheck .= '<img src="'.$cross.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_TITLE').'" title="'.JText::_('PLG_EASYFRONTENDSEO_TITLE').'"  />';
			}
		}

		if($this->params->get('icon_description') == 1)
		{
			if($description != '')
			{
				$metacheck .= '<img src="'.$check.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').'" title="'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').'"  />';
			}
			else
			{
				$metacheck .= '<img src="'.$cross.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').'" title="'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').'"  />';
			}
		}

		if($this->params->get('icon_keywords') == 1)
		{
			if($keywords != '')
			{
				$metacheck .= '<img src="'.$check.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').'" title="'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').'"  />';
			}
			else
			{
				$metacheck .= '<img src="'.$cross.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').'" title="'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').'"  />';
			}
		}

		if($this->params->get('icon_generator') == 1)
		{
			if($generator != '')
			{
				$metacheck .= '<img src="'.$check.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').'" title="'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').'"  />';
			}
			else
			{
				$metacheck .= '<img src="'.$cross.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').'" title="'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').'"  />';
			}
		}

		if($this->params->get('icon_robots') == 1)
		{
			if($robots != '')
			{
				$metacheck .= '<img src="'.$check.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').'" title="'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').'"  />';
			}
			else
			{
				$metacheck .= '<img src="'.$cross.'" alt="'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').'" title="'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').'"  />';
			}
		}

		if($this->params->get('style') == 0)
		{
			$buttons = '<div id="easyfrontendseo_topbar"><a id="toggle" href="#"><strong>EFSEO</strong></a> '.$metacheck.'</div>';
		}
		elseif($this->params->get('style') == 1)
		{
			if(empty($metacheck))
			{
				$metacheck = '<strong>Easy Frontend SEO</strong>';
			}

			$buttons = '<div id="easyfrontendseo_lightbox_button_'.$this->params->get('modal_position').'"><a href="#" id="modal">'.$metacheck.'</a></div>';
		}

		return $buttons;
	}

	/**
	 * Builds the form for the modal window or the topbar
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @param string $generator
	 * @param string $robots
	 *
	 * @return string
	 */
	private function buildForm($title, $description, $keywords, $generator, $robots)
	{
		if($this->params->get('style') == 0)
		{
			$output = '<div id="easyfrontendseo">';
		}
		elseif($this->params->get('style') == 1)
		{
			$output = '<div id="easyfrontendseo_lightbox">';
		}

		if($this->params->get('style') == 0)
		{
			$output .= '<h1>Easy Frontend SEO</h1>';
		}

		$output .= '<form action="'.$this->url_tostring.'" method="post">';

		if($this->params->get('field_title') == 1)
		{
			$characters_title = $this->getCharactersLength('characters_title');

			$output .= '<label for="title">'.JText::_('PLG_EASYFRONTENDSEO_TITLE').':</label>
                <input type="text" value="'.$title.'" name="title" id="title" size="60" maxlength="'.$characters_title.'" />';

			if($this->params->get('word_count') == 1)
			{
				$output .= '<span id="counter_title" class="efseo_counter"></span>';
			}

			$output .= '<br />';
		}
		elseif($this->params->get('field_title') == 2)
		{
			$output .= '<label for="title">'.JText::_('PLG_EASYFRONTENDSEO_TITLE').':</label>
                <span class="efseo_disabled">'.$title.'</span><br />';
		}

		if($this->params->get('field_description') == 1)
		{
			$characters_description = $this->getCharactersLength('characters_description');

			$output .= '<label for="description">'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').':</label>
                <textarea name="description" id="description" rows="3" maxlength="'.$characters_description.'">'.$description.'</textarea>';

			if($this->params->get('word_count') == 1)
			{
				$output .= '<span id="counter_description" class="efseo_counter"></span>';
			}

			$output .= '<br />';
		}
		elseif($this->params->get('field_description') == 2)
		{
			$output .= '<label for="description">'.JText::_('PLG_EASYFRONTENDSEO_DESCRIPTION').':</label>
                <span class="efseo_disabled">'.$description.'</span><br />';
		}

		if($this->params->get('field_keywords') == 1)
		{
			$output .= '<label for="keywords">'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').':</label>
                <input type="text" value="'.$keywords.'" name="keywords" id="keywords" size="60" maxlength="255" />';

			if($this->params->get('word_count') == 1)
			{
				$output .= '<span id="counter_keywords" class="efseo_counter"></span>';
			}

			$output .= '<br />';
		}
		elseif($this->params->get('field_keywords') == 2)
		{
			$output .= '<label for="keywords">'.JText::_('PLG_EASYFRONTENDSEO_KEYWORDS').':</label>
                <span class="efseo_disabled">'.$keywords.'</span><br />';
		}

		if($this->params->get('field_generator') == 1)
		{
			$output .= '<label for="generator">'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').':</label>
                <input type="text" value="'.$generator.'" name="generator" id="generator" size="60" maxlength="255" />';

			if($this->params->get('word_count') == 1)
			{
				$output .= '<span id="counter_generator" class="efseo_counter"></span>';
			}

			$output .= '<br />';
		}
		elseif($this->params->get('field_generator') == 2)
		{
			$output .= '<label for="generator">'.JText::_('PLG_EASYFRONTENDSEO_GENERATOR').':</label>
                <span class="efseo_disabled">'.$generator.'</span><br />';
		}

		if($this->params->get('field_robots') == 1)
		{
			$output .= '<label for="robots">'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').':</label>
                <input type="text" value="'.$robots.'" name="robots" id="robots" size="60" maxlength="255" />';

			if($this->params->get('word_count') == 1)
			{
				$output .= '<span id="counter_robots" class="efseo_counter"></span>';
			}

			$output .= '<br />';
		}
		elseif($this->params->get('field_robots') == 2)
		{
			$output .= '<label for="robots">'.JText::_('PLG_EASYFRONTENDSEO_ROBOTS').':</label>
                <span class="efseo_disabled">'.$robots.'</span><br />';
		}

		if($this->params->get('field_delete') == 1)
		{
			$output .= '<label for="delete">'.JText::_('PLG_EASYFRONTENDSEO_DELETEDATA').':</label>
                <input type="checkbox" value="1" name="delete" id="delete" /><br />';
		}

		$output .= '<input class="btn btn-primary" type="submit" value="'.JText::_('PLG_EASYFRONTENDSEO_APPLY').'" name="easyfrontendseo" /></form>';

		// Overwrite notice
		if($this->params->get('overwrite_notice') AND ($this->params->get('save_data_table_content') == 1 OR $this->params->get('save_data_table_menu') > 0))
		{
			$output .= '<p class="overwrite_notice">'.JText::_('PLG_EASYFRONTENDSEO_OVERWRITENOTICE').'</p>';
		}

		$output .= '</div>';

		if($this->params->get('style') == 1)
		{
			// Adjust the output for the modal window
			$output = str_replace("'", "\'", preg_replace('@\s+@', ' ', $output));
		}

		return $output;
	}

	/**
	 * Builds output code for the word and character counter
	 *
	 * @return string
	 */
	private function counterCode()
	{
		$output = '';

		if($this->params->get('word_count') == 1)
		{
			if($this->params->get('field_title') == 1)
			{
				$output .= "new WordCount('counter_title', {inputName:'title', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_title', {inputName:'title', eventTrigger: 'click', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'});";
			}

			if($this->params->get('field_description') == 1)
			{
				$output .= "new WordCount('counter_description', {inputName:'description', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_description', {inputName:'description', eventTrigger: 'click', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'});";
			}

			if($this->params->get('field_keywords') == 1)
			{
				$output .= "new WordCount('counter_keywords', {inputName:'keywords', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_keywords', {inputName:'keywords', eventTrigger: 'click', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'});";
			}

			if($this->params->get('field_generator') == 1)
			{
				$output .= "new WordCount('counter_generator', {inputName:'generator', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_generator', {inputName:'generator', eventTrigger: 'click', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'});";
			}

			if($this->params->get('field_robots') == 1)
			{
				$output .= "new WordCount('counter_robots', {inputName:'robots', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_robots', {inputName:'robots', eventTrigger: 'click', wordText:'".JText::_('PLG_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('PLG_EASYFRONTENDSEO_CHARACTERS')."'});";
			}
		}

		return $output;
	}

	/**
	 * Builds internal URL - indepedent of SEF function
	 *
	 * @param object $uri
	 *
	 * @return string
	 */
	private function buildInternalUrl($uri)
	{
		// Clone JUri object to avoid an error because of the method -parse- in the next step
		$uri_clone = clone $uri;

		// Reference to JRouter object
		$route = JSite::getRouter();

		// Get the internal route
		$url_internal_array = $route->parse($uri_clone);

		// Move Itemid at the end
		if(array_key_exists('Itemid', $url_internal_array))
		{
			$itemid = $url_internal_array['Itemid'];
			unset($url_internal_array['Itemid']);
			$url_internal_array['Itemid'] = $itemid;
		}

		// Move lang at the end
		if(array_key_exists('lang', $url_internal_array))
		{
			$lang = $url_internal_array['lang'];
			unset($url_internal_array['lang']);
			$url_internal_array['lang'] = $lang;
		}

		$url_internal = JUri::base().'index.php?'.JUri::buildQuery($url_internal_array);

		return $url_internal;
	}

	/**
	 * Checks permission rights
	 *
	 * @return boolean
	 */
	private function allowedUserGroups()
	{
		$user = JFactory::getUser();
		$user_id = $user->id;

		$filter_groups = (array)$this->params->get('filter_groups');
		$user_groups = JAccess::getGroupsByUser($user_id);

		foreach($user_groups as $user_group)
		{
			foreach($filter_groups as $filter_group)
			{
				if($user_group == $filter_group)
				{
					return true;
				}
			}
		}

		if($this->params->get('allowed_user_ids'))
		{
			$allowed_user_ids = array_map('trim', explode(",", $this->params->get('allowed_user_ids')));

			foreach($allowed_user_ids as $allowed_user_id)
			{
				if($allowed_user_id == $user_id)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Saves entered data to session to avoid loss
	 */
	private function saveDataToSession()
	{
		if($this->request->get('delete'))
		{
			$this->session->set('delete', $this->request->get('delete'), 'easyfrontendseo');
		}
		else
		{
			if($this->request->get('title', '', 'STRING'))
			{
				$this->session->set('title', $this->request->get('title', '', 'STRING'), 'easyfrontendseo');
			}

			if($this->request->get('description', '', 'STRING'))
			{
				$this->session->set('description', stripslashes(preg_replace('@\s+(\r\n|\r|\n)@', ' ', $this->request->get('description', '', 'STRING'))), 'easyfrontendseo');
			}

			if($this->request->get('keywords', '', 'STRING'))
			{
				$this->session->set('keywords', $this->request->get('keywords', '', 'STRING'), 'easyfrontendseo');
			}

			if($this->request->get('generator', '', 'STRING'))
			{
				$this->session->set('generator', $this->request->get('generator', '', 'STRING'), 'easyfrontendseo');
			}

			if($this->request->get('robots', '', 'STRING'))
			{
				$this->session->set('robots', $this->request->get('robots', '', 'STRING'), 'easyfrontendseo');
			}
		}

		$this->session->set('save_data_to_session', true, 'easyfrontendseo');
	}

	/**
	 * Deletes saved data from session
	 */
	private function deleteDataFromSession()
	{
		$this->session->clear('title', 'easyfrontendseo');
		$this->session->clear('description', 'easyfrontendseo');
		$this->session->clear('keywords', 'easyfrontendseo');
		$this->session->clear('generator', 'easyfrontendseo');
		$this->session->clear('robots', 'easyfrontendseo');
		$this->session->clear('save_data_to_session', 'easyfrontendseo');
		$this->session->clear('delete', 'easyfrontendseo');
	}

	/**
	 * Gets maximum characters length
	 *
	 * @param string $field_name
	 *
	 * @return int
	 */
	private function getCharactersLength($field_name)
	{
		$characters_length = $this->params->get($field_name);

		if(!is_numeric($characters_length))
		{
			if($field_name == 'characters_title')
			{
				$characters_length = 65;
			}
			elseif($field_name == 'characters_description')
			{
				$characters_length = 160;
			}
		}

		return $characters_length;
	}

	/**
	 * Saves data to the core content table
	 *
	 * @param string $description
	 * @param string $keywords
	 */
	private function saveDataToTableContent($description, $keywords)
	{
		$query = "UPDATE ".$this->db->quoteName('#__content')." SET ".$this->db->quoteName('metakey')." = ".$this->db->quote($keywords).", ".$this->db->quoteName('metadesc')." = ".$this->db->quote($description)." WHERE ".$this->db->quoteName('id')." = ".$this->db->quote((int)$this->request->get('id'));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * Saves data to the core menu table
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 */
	private function saveDataToTableMenu($title, $description, $keywords)
	{
		$menu = JMenu::getInstance('site')->getActive();

		// Check whether menu entry for the specific item exists - e.g. do not overwrite data of blog entry
		foreach($menu->query as $key => $value)
		{
			if($value != $this->request->get($key, 'cmd'))
			{
				return;
			}
		}

		$menu_params_array = JMenu::getInstance('site')->getParams($menu->id)->toArray();
		$save_data_table_menu = $this->params->get('save_data_table_menu');

		$title_array = array(1, 4, 5, 7);
		$description_array = array(2, 4, 6, 7);
		$keywords_array = array(3, 5, 6, 7);

		if(in_array($save_data_table_menu, $title_array))
		{
			$menu_params_array['page_title'] = $title;
		}

		if(in_array($save_data_table_menu, $description_array))
		{
			$menu_params_array['menu-meta_description'] = $description;
		}

		if(in_array($save_data_table_menu, $keywords_array))
		{
			$menu_params_array['menu-meta_keywords'] = $keywords;
		}

		$menu_params = json_encode($menu_params_array);

		$query = "UPDATE ".$this->db->quoteName('#__menu')." SET ".$this->db->quoteName('params')." = ".$this->db->quote($menu_params)." WHERE ".$this->db->quoteName('id')." = ".$this->db->quote((int)$this->request->get('Itemid'));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * Replaces the metadata of extensions automatically from the given data
	 *
	 * @param array         $metadata
	 * @param array         $head
	 * @param JDocumentHTML $document
	 *
	 * @throws Exception
	 */
	private function automaticReplacement($metadata, $head, $document)
	{
		// Extension: com_content - View: article
		$content = $this->params->get('com_content_enable');

		if(!empty($content))
		{
			$option = $this->request->get('option');
			$view = $this->request->get('view');
			$article_id = $this->request->get('id', '', 'INT');

			if($option == 'com_content' AND $view == 'article' AND !empty($article_id))
			{
				$model = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));
				$model->setState('params', JFactory::getApplication()->getParams());
				$article = (array)$model->getItem($article_id);

				if(!empty($article))
				{
					// Get most often used keywords - do not replace keywords from EFSEO table
					if(empty($metadata['keywords']))
					{
						$content_overwrite_keywords = $this->params->get('com_content_overwrite_keywords');

						// Only set keywords automatically if no global keywords are entered or the overwrite option is enabled
						if(empty($head['metaTags']['standard']['keywords']) OR !empty($content_overwrite_keywords))
						{
							$content_number_keywords = $this->params->get('com_content_number_keywords');
							$content_blacklist_keywords = array_map('mb_strtolower', array_map('trim', explode(',', $this->params->get('com_content_blacklist_keywords'))));
							$content_keywords_whole_text = $article['introtext'].' '.$article['fulltext'];
							$content_min_length_keyword = $this->params->get('com_content_min_length_keywords', 3);

							$document->setMetaData('keywords', $this->automaticReplacementKeywords($content_number_keywords, $content_blacklist_keywords, $content_keywords_whole_text, $content_min_length_keyword));
						}
					}

					// Generate the description - do not replace description from EFSEO table
					if(empty($metadata['description']))
					{
						$content_overwrite_description = $this->params->get('com_content_overwrite_description');

						// Only set description automatically if no global description is entered or the overwrite option is enabled
						if(empty($head['description']) OR !empty($content_overwrite_description))
						{
							$content_description_select_text = $this->params->get('com_content_description_select_text');
							$content_description_add_dots = $this->params->get('com_content_description_add_dots');
							$clean_again = true;

							if($content_description_select_text == 0)
							{
								if(!empty($article['fulltext']))
								{
									$content_description_whole_text = $article['fulltext'];
								}
								else
								{
									$content_description_whole_text = $article['introtext'];
								}
							}
							elseif($content_description_select_text == 1)
							{
								if(!empty($article['introtext']))
								{
									$content_description_whole_text = $article['introtext'];
								}
								else
								{
									$content_description_whole_text = $article['fulltext'];
								}
							}
							elseif($content_description_select_text == 2)
							{
								$content_description_whole_text = $article['introtext'].' '.$article['fulltext'];
								$clean_again = false;
							}

							$document->setMetaData('description', $this->automaticReplacementDescription($content_description_whole_text, $content_description_add_dots, $clean_again));
						}
					}
				}

				return;
			}
		}

		// Extension: com_k2 - View: item
		$k2 = $this->params->get('com_k2_enable');

		if(!empty($k2))
		{
			$option = $this->request->get('option');
			$view = $this->request->get('view');
			$item_id = $this->request->get('id', '', 'INT');

			if($option == 'com_k2' AND $view == 'item' AND !empty($item_id))
			{
				$query = "SELECT * FROM #__k2_items WHERE id = ".$item_id;
				$this->db->setQuery($query);
				$item = $this->db->loadAssoc();

				if(!empty($item))
				{
					// Get most often used keywords - do not replace keywords from EFSEO table
					if(empty($metadata['keywords']))
					{
						$k2_overwrite_keywords = $this->params->get('com_k2_overwrite_keywords');

						// Only set keywords automatically if no global keywords are entered or the overwrite option is enabled
						if(empty($head['metaTags']['standard']['keywords']) OR !empty($k2_overwrite_keywords))
						{
							$k2_number_keywords = $this->params->get('com_k2_number_keywords');
							$k2_blacklist_keywords = array_map('mb_strtolower', array_map('trim', explode(',', $this->params->get('com_k2_blacklist_keywords'))));
							$k2_keywords_whole_text = $item['introtext'].' '.$item['fulltext'];
							$k2_min_length_keyword = $this->params->get('com_k2_min_length_keywords', 3);

							$document->setMetaData('keywords', $this->automaticReplacementKeywords($k2_number_keywords, $k2_blacklist_keywords, $k2_keywords_whole_text, $k2_min_length_keyword));
						}
					}

					// Generate the description - do not replace description from EFSEO table
					if(empty($metadata['description']))
					{
						$k2_overwrite_description = $this->params->get('com_k2_overwrite_description');

						// Only set description automatically if no global description is entered or the overwrite option is enabled
						if(empty($head['description']) OR !empty($k2_overwrite_description))
						{
							$k2_description_select_text = $this->params->get('com_k2_description_select_text');
							$k2_description_add_dots = $this->params->get('com_k2_description_add_dots');
							$clean_again = true;

							if($k2_description_select_text == 0)
							{
								if(!empty($item['fulltext']))
								{
									$k2_description_whole_text = $item['fulltext'];
								}
								else
								{
									$k2_description_whole_text = $item['introtext'];
								}
							}
							elseif($k2_description_select_text == 1)
							{
								if(!empty($item['introtext']))
								{
									$k2_description_whole_text = $item['introtext'];
								}
								else
								{
									$k2_description_whole_text = $item['fulltext'];
								}
							}
							elseif($k2_description_select_text == 2)
							{
								$k2_description_whole_text = $item['introtext'].' '.$item['fulltext'];
								$clean_again = false;
							}

							$document->setMetaData('description', $this->automaticReplacementDescription($k2_description_whole_text, $k2_description_add_dots, $clean_again));
						}
					}
				}

				return;
			}
		}
	}

	/**
	 * Creates the keywords list for the automatic replacement
	 *
	 * @param int    $number_keywords
	 * @param array  $blacklist_keywords
	 * @param string $keywords_whole_text
	 *
	 * @return string
	 */
	private function automaticReplacementKeywords($number_keywords, $blacklist_keywords, $keywords_whole_text, $min_length_keyword)
	{
		$keywords_whole_text = $this->cleanString($keywords_whole_text);
		$pattern = array('@<[^>]+>@U', '@[,;:!"\.\?]@', '@\s+@');
		$content_words_array = explode(' ', preg_replace($pattern, ' ', $keywords_whole_text));
		$counter = array();

		foreach($content_words_array as $value)
		{
			if(!empty($value))
			{
				$value = mb_strtolower($value);

				if(!in_array($value, $blacklist_keywords) AND mb_strlen($value) >= $min_length_keyword)
				{
					if(isset($counter[$value]))
					{
						$counter[$value]++;
					}
					else
					{
						$counter[$value] = 1;
					}
				}
			}
		}

		arsort($counter);

		return implode(', ', array_keys(array_slice($counter, 0, $number_keywords)));
	}

	/**
	 * Creates the description for the automatic replacement
	 *
	 * @param string $content_description_whole_text
	 * @param bool   $content_description_add_dots
	 * @param bool   $clean_again
	 *
	 * @return type
	 */
	private function automaticReplacementDescription($content_description_whole_text, $content_description_add_dots, $clean_again = false)
	{
		$content_description_whole_text = $this->cleanString($content_description_whole_text, $clean_again);
		$content_number_description = $this->getCharactersLength('characters_description');

		if(strlen($content_description_whole_text) > $content_number_description)
		{
			if(!empty($content_description_add_dots))
			{
				$content_description = mb_substr($content_description_whole_text, 0, $content_number_description - 3).'...';
			}
			else
			{
				$content_description = mb_substr($content_description_whole_text, 0, $content_number_description);
			}
		}
		else
		{
			$content_description = $content_description_whole_text;
		}

		return $content_description;
	}

	/**
	 * Updates all entries if the url identification has been changed
	 *
	 * @param string $url           The corrrect URL which is used to identify the loaded page
	 * @param array  $url_old_array All other possible URLs which are not used but could have an entry in the database
	 */
	private function updateDatabase($url, $url_old_array)
	{
		foreach($url_old_array as $url_old)
		{
			if($url != $url_old)
			{
				// Load saved metadata
				$query = "SELECT * FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($url_old);
				$this->db->setQuery($query);
				$metadata = $this->db->loadAssoc();

				if(!empty($metadata))
				{
					// Check whether the internal url is already in the database
					$query = "SELECT * FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($url);
					$this->db->setQuery($query);
					$row = $this->db->loadRow();

					// Save metadata with internal URL
					if(!empty($row))
					{
						$query = "UPDATE ".$this->db->quoteName('#__plg_easyfrontendseo')." SET ".$this->db->quoteName('title')." = ".$this->db->quote($metadata['title']).", ".$this->db->quoteName('description')." = ".$this->db->quote($metadata['description']).", ".$this->db->quoteName('keywords')." = ".$this->db->quote($metadata['keywords']).", ".$this->db->quoteName('generator')." = ".$this->db->quote($metadata['generator']).", ".$this->db->quoteName('robots')." = ".$this->db->quote($metadata['robots'])." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($url);
						$this->db->setQuery($query);
						$this->db->execute();
					}
					else
					{
						// New entry in the database
						$query = "INSERT INTO ".$this->db->quoteName('#__plg_easyfrontendseo')." (".$this->db->quoteName('url').", ".$this->db->quoteName('title').", ".$this->db->quoteName('description').", ".$this->db->quoteName('keywords').", ".$this->db->quoteName('generator').", ".$this->db->quoteName('robots').") VALUES (".$this->db->quote($url).", ".$this->db->quote($metadata['title']).", ".$this->db->quote($metadata['description']).", ".$this->db->quote($metadata['keywords']).", ".$this->db->quote($metadata['generator']).", ".$this->db->quote($metadata['robots']).")";
						$this->db->setQuery($query);
						$this->db->execute();
					}

					// Delete old entry
					$query = "DELETE FROM ".$this->db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->db->quoteName('url')." = ".$this->db->quote($url_old);
					$this->db->setQuery($query);
					$this->db->execute();
				}
			}
		}
	}

	/**
	 * Excludes certain components from collection process
	 *
	 * @return boolean
	 */
	private function excludeComponents()
	{
		$option = $this->request->get('option');
		$exclude_components = array_map('trim', explode("\n", $this->params->get('exclude_components')));

		if(in_array($option, $exclude_components))
		{
			return true;
		}

		return false;
	}

	/**
	 * Prepares and cleans the string
	 *
	 * @param string $string
	 * @param bool   $clean_again
	 *
	 * @return mixed|string
	 */
	private function cleanString($string, $clean_again = false)
	{
		static $string_clean = false;

		if($string_clean === false OR $clean_again == true)
		{
			// Replace plugins with correct content
			JPluginHelper::importPlugin('content');
			$string = JHtml::_('content.prepare', $string, '');

			// Convert quotes and decode HTML entities
			$search = array(chr(145), chr(146), chr(147), chr(148), chr(151), '&#39;');
			$replace = array("'", "'", '"', '"', '-', "'", "'");
			$string = html_entity_decode(str_replace($search, $replace, $string), ENT_COMPAT, 'UTF-8');

			// Strip HTML tags and remove invisible chars
			$string = preg_replace('@\s+(\r\n|\r|\n|\t)@', ' ', (strip_tags($string)));

			// Exchange double quotes and remove white spaces for the description
			$string = str_replace('"', "'", $string);
			$string = preg_replace('@\s+@', ' ', $string);

			// Remove all bad UTF8 characters with the help of the UTF8 library
			jimport('phputf8.utils.bad');
			$string = utf8_bad_strip($string);

			$string_clean = trim(htmlspecialchars($string));
		}

		return $string_clean;
	}

	/**
	 * Checks whether all requirements are met for the execution
	 * Written generically to be used in all Kubik-Rubik Joomla! Extensions
	 *
	 * @param bool   $admin                 Allow backend execution - true or false
	 * @param string $version_min           Minimum required Joomla! version - e.g. 3.2
	 * @param string $extension_name        Name of the extension of the warning message
	 * @param string $extension_system_name System name of the extension for the language file loading - e.g. plg_system_xxx
	 * @param string $jpath                 Path of the language file - JPATH_ADMINISTRATOR or JPATH_SITE
	 *
	 * @return bool
	 */
	private function checkVersionRequirements($admin, $version_min, $extension_name, $extension_system_name, $jpath)
	{
		$execution = true;
		$version = new JVersion();

		if(!$version->isCompatible($version_min))
		{
			$execution = false;
			$backend_message = true;
		}

		if(empty($admin))
		{
			if(JFactory::getApplication()->isAdmin())
			{
				$execution = false;

				if(!empty($backend_message))
				{
					$this->loadLanguage($extension_system_name, $jpath);
					JFactory::getApplication()->enqueueMessage(JText::sprintf('KR_JOOMLA_VERSION_REQUIREMENTS_NOT_MET', $extension_name, $version_min), 'warning');
				}
			}
		}

		return $execution;
	}
}
