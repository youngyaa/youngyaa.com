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

class Com_EasyFrontendSeoInstallerScript
{
    function install($parent)
    {
        $manifest = $parent->get('manifest');
        $parent = $parent->getParent();
        $source = $parent->getPath('source');

        $installer = new JInstaller();

        foreach($manifest->plugins->plugin as $plugin)
        {
            $attributes = $plugin->attributes();
            $plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];
            $installer->install($plg);
        }
    }

    function uninstall($parent)
    {
        // Not needed at the moment
    }

    function update($parent)
    {
        $manifest = $parent->get('manifest');
        $parent = $parent->getParent();
        $source = $parent->getPath('source');

        $installer = new JInstaller();

        foreach($manifest->plugins->plugin as $plugin)
        {
            $attributes = $plugin->attributes();
            $plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];
            $installer->install($plg);
        }
    }

    function preflight($type, $parent)
    {
        // First check whether version requirements are met for this specific version
        if(!$this->checkVersionRequirements('3.2', 'Easy Frontend SEO'))
        {
            return false;
        }
    }

    function postflight($type, $parent)
    {
        $db = JFactory::getDbo();

        // Enable the pluign
        $db->setQuery("UPDATE ".$db->quoteName('#__extensions')." SET ".$db->quoteName('enabled')." = 1 WHERE ".$db->quoteName('element')." = 'easyfrontendseo' AND ".$db->quoteName('type')." = 'plugin'");
        $db->execute();

        // Add the column id if the component is installed the first time but the plugin was already installed previously
        $db->setQuery("DESCRIBE ".$db->quoteName('#__plg_easyfrontendseo'));
        $column_first = $db->loadAssoc();

        if($column_first['Field'] != 'id')
        {
            $db->setQuery("ALTER TABLE ".$db->quoteName('#__plg_easyfrontendseo')." ADD ".$db->quoteName('id')." INT NOT NULL AUTO_INCREMENT FIRST , ADD PRIMARY KEY (".$db->quoteName('id').") , ADD UNIQUE (".$db->quoteName('id').")");
            $db->execute();
        }
    }

    /**
     * Checks whether all requirements are met for the execution
     * Written generically to be used in all Kubik-Rubik Joomla! Extensions
     *
     * @param $version_min    Minimum required Joomla! version - e.g. 3.2
     * @param $extension_name Name of the extension of the warning message
     *
     * @return bool
     */
    private function checkVersionRequirements($version_min, $extension_name)
    {
        $version = new JVersion();

        if(!$version->isCompatible($version_min))
        {
            JFactory::getApplication()->enqueueMessage(JText::sprintf('KR_JOOMLA_VERSION_REQUIREMENTS_NOT_MET_INSTALLATION', $extension_name, $version_min), 'error');

            return false;
        }

        return true;
    }
}