<?php

/**
 * @package 	bt_media - BT Media Component
 * @version		2.0.0
 * @created		Dec 2011
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
require_once(JPATH_SITE.'/components/com_bt_media/router.php');
global $sh_LANG;
$sefConfig = & Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);
if ($dosef == false)
    return;
if (isset($option)) {
    shRemoveFromGETVarsList('option');
}
shRemoveFromGETVarsList('lang');
if (!empty($limit))
    shRemoveFromGETVarsList('limit');
if (isset($limitstart))
    shRemoveFromGETVarsList('limitstart');
if (isset($catid)) {
    shRemoveFromGETVarsList('catid');
} else {
    $catid = '';
}
if (isset($view)) {
    shRemoveFromGETVarsList('view');
} else {
    $view = "";
}
if (isset($catid_rel)) {
    shRemoveFromGETVarsList('catid_rel');
} else {
    $catid_rel = '';
}

if (isset($id)) {
    shRemoveFromGETVarsList('id');
}

// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? $task : null;
$Itemid = isset($Itemid) ? $Itemid : null;
if (!$Itemid) {
    $catid_rel = $catid_rel ? $catid_rel : $catid;
    $db = JFactory::getDbo();
    $user = JFactory::getUser();
    $groups = implode(',', $user->getAuthorisedViewLevels());
    $query = "select id from #__menu where type='component' and link like '%index.php?option=com_bt_media&view=list&catid=" . $catid . "%' and published = 1 and access in(" . $groups . ") order by lft limit 1";
    $db->setQuery($query);

    $Itemid2 = $db->loadResult();

    if (!$Itemid2 && $catid) {
        $query = 'select parent_id from #__bt_media_categories where id = ' . $catid;
        $db->setQuery($query);
        $catid = intval($db->loadResult());
        $Itemid2 = BTMediaFindItemID($catid);
    }
    if ($Itemid2) {
        $Itemid = $Itemid2;
        $string .= '&Itemid=' . $Itemid;
    }
}

$shMedia = shGetComponentPrefix($option);
$shMedia = empty($shMedia) ? getMenuTitle($option, $task, $Itemid, null, $shLangName) : $shMedia;
$title[] = (empty($shMedia) || $shMedia == '/') ? 'list' : $shMedia;

switch ($view) {
    case 'list' :
    case 'featured' :
        if (!empty($catid)) {
            $database = JFactory::getDbo();
            $q = "SELECT id,parent_id, title FROM #__bt_media_categories where id ='$catid' and published = 1";
            $database->setQuery($q);
            if (shTranslateUrl($option, $shLangName))
                $result = $database->loadObject();
            else
                $result = $database->loadObject(false);
            if (!empty($result)) {
                if ($result->parent_id == 0) {
                    $title[] = $result->name;
                    $title[] = '/';
                } else {
                    $root_id = $result->parent_id;
                    $temp = array();
                    while ($root_id != "") {
                        $string = "SELECT * FROM #__bt_media_categories where id ='$root_id'";
                        $database->setQuery($string);
                        $rs = $database->loadObject();
                        if (!empty($rs)) {
                            $temp[$rs->id] = $rs->name;
                        } else {
                            $title[] = "";
                        }
                        $arr = array_reverse($temp);
                        @$root_id = $rs->parent_id;
                    }
                    $title[] = implode($arr, '/');
                    $title[] = $result->name;
                }
            } else {
                $title[] = $catid;
                $title[] = ':';
            }
        }
        break;
    case'detail':
        if (!empty($catid_rel) and !empty($id)) {
            shRemoveFromGETVarsList('id');
            $database = JFactory::getDbo();
            $query = "SELECT id, name from #__bt_media_items where cate_id = '$catid_rel' and id='$id' and published = 1";
            $database->setQuery($query);
            if (shTranslateUrl($option, $shLangName))
                $result = $database->loadObject();
            else
                $result = $database->loadObject(false);
            if (!empty($result)) {
                $temp = array();
                while ($catid_rel != "") {
                    $string = "SELECT * FROM #__bt_media_categories where id ='$catid_rel'";
                    $database->setQuery($string);
                    $rs = $database->loadObject();
                    if (!empty($rs)) {
                        $temp[$rs->id] = $rs->name;
                    } else {
                        $title[] = "";
                    }
                    $arr = array_reverse($temp);
                    @$catid_rel = $rs->parent_id;
                }
                $title[] = implode($arr, '/');
                $title[] = $result->name;
            } else {
                $title[] = $id;
            }
        } else {
            $query = "SELECT id, title from #__bt_media_items where id='$id' and published = 1";
            $database->setQuery($query);
            $result = $database->loadObject();
            if (!empty($result)) {
                $title[] = $result->name;
            } else {
                $title[] = $id;
            }
        }
    case '':
        $task = isset($task) ? $task : null;
        if ($task) {
            $title[] = $task;
            shRemoveFromGETVarsList('task');
        }
        break;
    default:
        $dosef = false;
        break;
}
$Itemid = isset($Itemid) ? $Itemid : null;
if (isset($Itemid)) {
    shRemoveFromGETVarsList('Itemid');
}

if ($dosef) {
    $string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString, (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLangName) ? @$shLangName : null));
}  
