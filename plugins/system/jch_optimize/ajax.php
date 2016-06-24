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
require_once dirname(__FILE__) . '/bootstrap.php';

$app = JFactory::getApplication('site');

$action = $app->input->get('action', '', 'string');
$format = strtolower($app->input->getWord('format'));

if(!$action)
{
        exit();
}

jimport('joomla.event.dispatcher');

$plugin = JPluginHelper::getPlugin('system', 'jch_optimize');

if(!$plugin)
{
        exit();
}

JPluginHelper::importPlugin('system', $plugin->name);
$dispatcher = JDispatcher::getInstance();

try
{
        $results = $dispatcher->trigger('onAjax' . ucfirst($action));
}
catch (Exception $e)
{
        $results = $e;
}


if (is_scalar($results))
{
        $out = (string) $results;
}
else
{
        $out = implode((array) $results);
}

echo $out;
