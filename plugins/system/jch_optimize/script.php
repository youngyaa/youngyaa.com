<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for 
 *   optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall. All rights reserved.
 * @license GNU/GPLv3, See LICENSE file 
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
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>. 
 * 
 * This plugin includes other copyrighted works. See individual 
 * files for details.
 */
defined('_JEXEC') or die('Restricted access');

class Plgsystemjch_optimizeInstallerScript
{
        public function preflight($type, $parent)
        {
                $app = JFactory::getApplication();
                
                if ($type == 'install')
                {
                        if (version_compare(PHP_VERSION, '5.3.0', '<'))
                        {
                                $app->enqueueMessage(
                                        JText::sprintf('JCH_REQUIRES_PHP_UPDATE' . PHP_VERSION), 'error'
                                );

                                return false;
                        }
                }        
                        $compatible = TRUE;
                        
//                        if (version_compare(JVERSION, '3.0', '<'))
//                        {
//                                $minimum_version = '2.5.25';
//                                
//                                if(version_compare(JVERSION, $minimum_version, '<'))
//                                {
//                                        $compatible = FALSE;
//                                }
//                        }
//                        else
//                        {
                                $minimum_version = '3.3.0';
                                
                                if(version_compare(JVERSION, $minimum_version, '<'))
                                {
                                        $compatible = FALSE;
                                }
//                        }
                        
                        if(!$compatible)
                        {
                                $app->enqueueMessage(
                                        JText::sprintf('JCH_JOOMLA_VERSION_NOT_COMPATIBLE', $minimum_version), 'error'
                                );

                                return FALSE;
                        }
//                }

                $manifest = $parent->get('manifest');
                $new_variant = (string) $manifest->variant;
                
                $file = JPATH_SITE . '/plugins/system/jch_optimize/jch_optimize.xml';
                
                if(file_exists($file))
                {
                        $xml = JFactory::getXML($file);
                        $old_variant = (string) $xml->variant;

                        if ($old_variant == 'PRO' && $new_variant == 'FREE')
                        {
                                $app->enqueueMessage(
                                        JText::_('JCH_CANNOT_INSTALL_FREE_VERSION_OVER_PRO'), 'error'
                                );

                                return false;
                        }
                }
        }
        
        /**
         * 
         * @param type $type
         * @param type $parent
         */
        public function postflight($type, $parent)
        {
                require_once(JPATH_ROOT . '/plugins/system/jch_optimize/jchoptimize/loader.php');
                require_once(JPATH_ROOT . '/plugins/system/jch_optimize/fields/autoorder.php');
                
                if($type == 'install')
                {
                        JFormFieldAutoorder::fixFilePermissions(true);
                }
                
                if($type == 'update')
                {
                        JFormFieldAutoorder::cleanCache(true);
                        
                        $params = JchPlatformPlugin::getPluginParams();
                        
                        if(($exludeAllExtensions = $params->get('excludeAllExtensions', '')) !== '')
                        {
                                $params->set('includeAllExtensions', !$exludeAllExtensions);
                                $params->set('excludeAllExtensions', '');
                                
                                JchPlatformPlugin::saveSettings($params);
                        }
                }
                
                JFormFieldAutoorder::orderPlugins(true);
        }
        
        /**
         * 
         * @param type $parent
         */
        public function uninstall($parent)
        {
                jimport('joomla.filesystem.folder');
                require_once(JPATH_ROOT . '/plugins/system/jch_optimize/jchoptimize/loader.php');
                
                $sprites = JPATH_ROOT . '/images/jch-optimize';
                
                if(file_exists($sprites))
                {
                        JFolder::delete($sprites);
                }
                
                $oJchCache = JchPlatformCache::getCacheObject();
                $oJchCache->clean('plg_jch_optimize');
                $oJchCache->clean('page');
                
                $htaccess = JPATH_ROOT . '/.htaccess';
                
                if(file_exists($htaccess))
                {
                        $contents = file_get_contents($htaccess);
                        $regex = '@\n?## BEGIN EXPIRES CACHING - JCH OPTIMIZE ##.*?## END EXPIRES CACHING - JCH OPTIMIZE ##@s';
                        
                        $clean_contents = preg_replace($regex, '', $contents);
                        
                        file_put_contents($htaccess, $clean_contents);
                }
        }
}
