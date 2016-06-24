<?php
/**
 * @package         NoNumber Framework
 * @version         16.3.25323
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNN_Framework_Needed extends NNFormField
{
	public $type = 'Framework_Needed';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$extensions = array(
			'addtomenu'               => 5,
			'advancedmodulemanager'   => 6,
			'advancedtemplatemanager' => 2,
			'articlesanywhere'        => 5,
			'betterpreview'           => 5,
			'cachecleaner'            => 5,
			'cdnforjoomla'            => 5,
			'componentsanywhere'      => 3,
			'contenttemplater'        => 6,
			'dbreplacer'              => 5,
			'dummycontent'            => 3,
			'emailprotector'          => 3,
			'geoip'                   => 1,
			'iplogin'                 => 3,
			'modals'                  => 7,
			'modulesanywhere'         => 5,
			'extensionmanager'        => 6,
			'rereplacer'              => 7,
			'sliders'                 => 6,
			'snippets'                => 5,
			'sourcerer'               => 6,
			'tabs'                    => 6,
			'tooltips'                => 5,
			'whatnothing'             => 11,
		);

		foreach ($extensions as $extension => $version)
		{
			if (!$current_version = $this->getCurrentVersion($extension))
			{
				// Extension not found
				continue;
			}

			if ($current_version < $version)
			{
				// An extension (version) is installed that still needs the NoNumber framework
				return;
			}
		}

		// No extensions found that still needs the NoNumber framework
		return '</div><div class="alert alert-danger">' . JText::_('NN_FRAMEWORK_NO_LONGER_USED');
	}

	private function getCurrentVersion($extension)
	{

		if (!$xml = $this->getXmlFile($extension))
		{
			return;
		}
		$xml = JInstaller::parseXMLInstallFile($xml);

		if (!isset($xml['version']))
		{
			return;
		}

		return $xml['version'];
	}

	private function getXmlFile($extension)
	{
		$paths = array(
			JPATH_ADMINISTRATOR . '/components/com_' . $extension . '/com_' . $extension . '.xml',
			JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/mod_' . $extension . '.xml',
			'/plugins/system/' . $extension . '.xml',
			'/plugins/editors-xtd/' . $extension . '.xml',
		);

		foreach ($paths as $path)
		{
			if (!JFile::exists($path))
			{
				continue;
			}

			return $path;
		}

		return false;
	}
}
