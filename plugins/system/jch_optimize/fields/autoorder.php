<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
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
 */
defined('_JEXEC') or die;

if (version_compare(PHP_VERSION, '5.3.0', '<'))
{
        require_once dirname(__FILE__) . '/compat.php';

        class JFormFieldAutoorder extends JFormFieldCompat
        {

                public $type = 'autoorder';

                protected function getInput()
                {
                        
                }

        }

}
else
{
        include_once dirname(__FILE__) . '/auto.php';

        class JFormFieldAutoorder extends JFormFieldAuto
        {

                protected $type = 'autoorder';

                public function __construct($form = null)
                {
                        parent::__construct($form);

                        switch (JFactory::getApplication()->input->get('jchtask'))
                        {
                                case 'orderplugins':
                                        $this->orderPlugins();
                                        ;
                                        break;
                                case 'cleancache':
                                        $this->cleanCache();
                                        break;
                                case 'browsercaching':
                                        $this->leverageBrowserCaching();
                                        break;
                                case 'filepermissions':
                                        $this->fixFilePermissions();
                                        break;
                                default:
                                        break;
                        }
                }

                protected function getInput()
                {
                        $cache_path = JPATH_SITE . '/cache/plg_jch_optimize/';

                        if (file_exists($cache_path))
                        {
                                $fi = new FilesystemIterator($cache_path, FilesystemIterator::SKIP_DOTS);

                                $size = 0;

                                foreach ($fi as $file)
                                {
                                        $size += $file->getSize();
                                }

                                $decimals = 2;
                                $sz       = 'BKMGTP';
                                $factor   = (int) floor((strlen($size) - 1) / 3);
                                $size     = sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . $sz[$factor];

                                $no_files = number_format(iterator_count($fi));
                        }
                        else
                        {
                                $no_files = 0;
                                $size     = '0B';
                        }

                        $sField = parent::getInput();

                        $sField .= '<div><br><div><em>' . JText::sprintf('JCH_FILES', $no_files) . '</em></div>'
                                . '<div><em>' . JText::sprintf('JCH_SIZE', $size) . '</em></div></div>';


                        return $sField;
                }

                protected function getButtons()
                {
                        $aButtons               = array();
                        $aButtons[4]['link']    = JURI::getInstance()->toString() . '&amp;jchtask=orderplugins';
                        $aButtons[4]['icon']    = 'fa-sort-numeric-asc';
                        $aButtons[4]['color']   = '#278EB1';
                        $aButtons[4]['text']    = JchPlatformUtility::translate('Order Plugin');
                        $aButtons[4]['script']  = '';
                        $aButtons[4]['class']   = 'enabled';
                        $aButtons[4]['tooltip'] = JchPlatformUtility::translate('The published order of the plugin is important! When you click on this icon, it will attempt to order the plugin correctly.');

                        $icons = JchOptimizeAdmin::getUtilityIcons();
                        array_splice($icons, 2, 0, $aButtons );
                        
                        return $icons;
                }

                /**
                 * 
                 */
                public static function cleanCache($install=false)
                {
                        $oJchCache = JchPlatformCache::getCacheObject();

                        $oController = new JControllerLegacy();
                        
                        $plugin_cache = $oJchCache->clean('plg_jch_optimize');
                        $page_cache = $oJchCache->clean('page');
                        
                        if($install)
                        {
                                return;
                        }

                        JchOptimizeHelper::clearHiddenValues(JchPlatformPlugin::getPluginParams());
                                
                        if ($plugin_cache === FALSE || $page_cache === FALSE)
                        {
                                $oController->setMessage(JText::_('JCH_CACHECLEAN_FAILED'), 'error');
                        }
                        else
                        {
                                $oController->setMessage(JText::_('JCH_CACHECLEAN_SUCCESS'));
                        }

                        self::display($oController);
                }

                /**
                 * 
                 * @return type
                 */
                protected static function getPlugins()
                {
                        $oDb    = JFactory::getDbo();
                        $oQuery = $oDb->getQuery(TRUE);
                        $oQuery->select($oDb->quoteName(array('extension_id', 'ordering', 'element')))
                                ->from($oDb->quoteName('#__extensions'))
                                ->where(array(
                                        $oDb->quoteName('type') . ' = ' . $oDb->quote('plugin'),
                                        $oDb->quoteName('folder') . ' = ' . $oDb->quote('system')
                                        ), 'AND');

                        $oDb->setQuery($oQuery);

                        return $oDb->loadAssocList('element');
                }

                /**
                 * 
                 */
                protected function leverageBrowserCaching()
                {
                        $oController = new JControllerLegacy();

                        $expires = JchOptimizeAdmin::leverageBrowserCaching();

                        if ($expires === FALSE)
                        {
                                $oController->setMessage(JText::_('JCH_LEVERAGEBROWSERCACHE_FAILED'), 'error');
                        }
                        elseif ($expires == 'FILEDOESNTEXIST')
                        {
                                $oController->setMessage(JText::_('JCH_LEVERAGEBROWSERCACHE_FILEDOESNTEXIST'), 'warning');
                        }
                        elseif ($expires == 'CODEALREADYINFILE')
                        {
                                $oController->setMessage(JText::_('JCH_LEVERAGEBROWSERCACHE_CODEALREADYINFILE'), 'notice');
                        }
                        else
                        {
                                $oController->setMessage(JText::_('JCH_LEVERAGEBROWSERCACHE_SUCCESS'));
                        }

                        $this->display($oController);
                }

                /**
                 * 
                 */
                public static function fixFilePermissions($install=false)
                {
                        jimport('joomla.filesystem.folder');

                        $wds = array(
                                'plugins/system/jch_optimize',
                                'media/plg_jchoptimize'
                        );

                        $result = true;

                        foreach ($wds as $wd)
                        {
                                $files = JFolder::files(JPATH_ROOT . '/' . $wd, '.', TRUE, TRUE);

                                foreach ($files as $file)
                                {
                                        if (!chmod($file, 0644))
                                        {
                                                $result = false;

                                                break 2;
                                        }
                                }

                                $folders = JFolder::folders(JPATH_ROOT . '/' . $wd, '.', TRUE, TRUE);

                                foreach ($folders as $folder)
                                {
                                        if (!chmod($folder, 0755))
                                        {
                                                $result = false;

                                                break 2;
                                        }
                                }
                        }
                        
                        if($install)
                        {
                                return;
                        }

                        $oController = new JControllerLegacy();

                        if ($result)
                        {
                                $oController->setMessage(JText::_('JCH_FIXFILEPERMISSIONS_SUCCESS'));
                        }
                        else
                        {
                                $oController->setMessage(JText::_('JCH_FIXFILEPERMISSIONS_FAIL'), 'error');
                        }

                        self::display($oController);
                }

                /**
                 * 
                 * @return type
                 */
                public static function orderPlugins($install=false)
                {
                        $aOrder = array(
                                'jscsscontrol',
                                'eorisis_jquery',
                                'jqueryeasy',
                                'jch_optimize',
                                'plugin_googlemap3',
                                'cdnforjoomla',
                                'bigshotgoogleanalytics',
                                'GoogleAnalytics',
                                'ykhoonhtmlprotector',
                                'jat3',
                                'cache',
                                'homepagecache',
                                'jSGCache',
                                'jotcache',
                                'vmcache_last'
                        );

                        $aPlugins = self::getPlugins();

                        $aLowerPlugins = array_values(array_filter($aOrder,
                                                                   function($aVal) use ($aPlugins)
                                {
                                        return (array_key_exists($aVal, $aPlugins));
                                }
                        ));

                        $iNoPlugins      = count($aPlugins);
                        $iNoLowerPlugins = count($aLowerPlugins);
                        $iBaseOrder      = $iNoPlugins - $iNoLowerPlugins;

                        $cid   = array();
                        $order = array();

                        foreach ($aPlugins as $key => $value)
                        {
                                if (in_array($key, $aLowerPlugins))
                                {
                                        $value['ordering'] = $iBaseOrder + 1 + array_search($key, $aLowerPlugins);
                                }
                                elseif ($value['ordering'] >= $iBaseOrder)
                                {
                                        $value['ordering'] = $iBaseOrder - 1;
                                }

                                $cid[]   = $value['extension_id'];
                                $order[] = $value['ordering'];
                        }

                        JArrayHelper::toInteger($cid);
                        JArrayHelper::toInteger($order);

                        $aOrder          = array();
                        $aOrder['cid']   = $cid;
                        $aOrder['order'] = $order;

                        $oController = new JControllerLegacy;

                        $oController->addModelPath(JPATH_ADMINISTRATOR . '/components/com_plugins/models', 'PluginsModel');
                        $oPluginModel = $oController->getModel('Plugin', 'PluginsModel');

                        $saved = $oPluginModel->saveorder($aOrder['cid'], $aOrder['order']);
                        
                        if($install)
                        {
                                return;
                        }
                        
                        if ($saved === FALSE)
                        {
                                $oController->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $oPluginModel->getError()), 'error');
                        }
                        else
                        {
                                $oController->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
                        }

                        self::display($oController);
                }
        }

}