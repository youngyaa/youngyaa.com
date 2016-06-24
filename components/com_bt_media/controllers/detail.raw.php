<?php

/**
 * @package 	bt_portfolio - BT Portfolio Component
 * @version		1.2.6
 * @created		Feb 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class Bt_mediaControllerDetail extends Bt_mediaController {

    function rate() {
        $params = JComponentHelper::getParams('com_bt_media');
        $input = JFactory::getApplication()->input;

        $result = array();
        $result['success'] = true;

        $user = JFactory::getUser();
        if ($user->id == 0 && $params->get('allow_guest_vote', 0) == 0) {
            $result['success'] = false;
            $result['message'] = JText::_('COM_BT_MEDIA_RATING_LOGIN_NOTICE');
        } else {
            $mediaid = $input->get('id', 0);
            $rating = $input->get('rating', 0);
            $db = JFactory::getDbo();

            $sqlQuery = "SELECT * FROM #__bt_media_items WHERE id=" . $mediaid;
            $db->setQuery($sqlQuery);
            $media_item = $db->loadObject();
            
            // Fake submit
            if (!$media_item || $rating == 0 || $rating > 5) {
                die();
            }
            $ip = $_SERVER['REMOTE_ADDR'];
            if ($user->id)
                $sqlQuery = "SELECT COUNT(*) FROM #__bt_media_vote WHERE item_id={$mediaid} AND user_id={$user->id}";
            else
                $sqlQuery = "SELECT COUNT(*) FROM #__bt_media_vote WHERE item_id={$mediaid} AND ip='{$ip}' AND user_id = 0";

            $db->setQuery($sqlQuery);
            if ($db->loadResult() > 0) {
                $result['success'] = false;
                $result['message'] = JText::_('COM_BT_MEDIA_RATING_HAVE_VOTED');
            } else {
                $sqlQuery = "UPDATE #__bt_media_items SET vote_sum=vote_sum + {$rating}, vote_count=vote_count + 1 WHERE id={$mediaid}";
                $db->setQuery($sqlQuery);
                $db->query();

                $date = JFactory::getDate();
                $created = $date->toSql();

                $sqlQuery = "INSERT INTO #__bt_media_vote(item_id, user_id, created_date, vote,ip) VALUES({$mediaid}, {$user->id}, '{$created}', {$rating}, '{$ip}')";
                $db->setQuery($sqlQuery);
                $db->query();

                $result['message'] = JText::_('COM_BT_MEDIA_RATING_SUCCESS_MESSAGE');
                $result['rating_sum'] = $media_item->vote_sum + $rating;
                $result['rating_count'] = $media_item->vote_count + 1;
                $result['rating'] = $result['rating_sum'] / $result['rating_count'];
                $result['rating_text'] = sprintf(JText::_('COM_BT_MEDIA_RATING_TEXT'), $result['rating'], $result['rating_count']);
                $result['rating_width'] = round(15 * $result['rating']);
            }
        }

        echo json_encode($result);
    }

}

?>