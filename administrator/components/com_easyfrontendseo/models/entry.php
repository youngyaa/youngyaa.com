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

class EasyFrontendSeoModelEntry extends JModelLegacy
{
    protected $data = null;
    protected $id = null;
    protected $input;
    protected $error;

    function __construct()
    {
        parent::__construct();

        $this->input = JFactory::getApplication()->input;

        $array = $this->input->get('id', 0, 'ARRAY');
        $this->setId((int)$array[0]);
    }

    /**
     * Gets the lengths of characters for title and description
     *
     * @return array
     */
    function getCharactersLength()
    {
        $plugin = JPluginHelper::getPlugin('system', 'easyfrontendseo');

        if(!empty($plugin))
        {
            $plugin_params = new JRegistry();
            $plugin_params->loadString($plugin->params);

            $characters_length = array('title' => $plugin_params->get('characters_title'), 'description' => $plugin_params->get('characters_description'));
        }
        else
        {
            $characters_length = array('title' => 65, 'description' => 160);
        }

        return $characters_length;
    }

    function getId()
    {
        return $this->id;
    }

    function setId($id)
    {
        $this->id = $id;
        $this->data = null;
    }

    function getError($i = null, $toString = true)
    {
        return $this->error;
    }

    /**
     * Loads the data from the database
     *
     * @return bool|JTable|mixed|null|stdClass
     * @throws Exception
     */
    function getData()
    {
        if($this->state->get('task') != 'add')
        {
            $this->data = $this->getInputSession();

            if(empty($this->data))
            {
                $query = "SELECT * FROM ".$this->_db->quoteName('#__plg_easyfrontendseo')." WHERE ".$this->_db->quoteName('id')." = ".$this->_db->quote($this->id);
                $this->_db->setQuery($query);
                $this->data = $this->_db->loadObject();

                if(empty($this->data))
                {
                    $this->data = $this->getTable('entry', 'EasyFrontendSeoTable');
                    $this->data->id = 0;
                }
            }
        }
        else
        {
            $this->data = $this->getTable('entry', 'EasyFrontendSeoTable');
            $this->data->id = 0;
        }

        return $this->data;
    }

    /**
     * Restores the inpute data from the session
     *
     * @return bool|stdClass
     */
    private function getInputSession()
    {
        $session = JFactory::getSession();
        $input = $session->get('efseo_data');

        if(!empty($input))
        {
            $data = new stdClass();

            $data->id = $input->getInt('id');
            $data->url = trim($input->get('url', '', 'RAW'));
            $data->title = trim($input->get('title', '', 'RAW'));
            $data->description = trim($input->get('description', '', 'RAW'));
            $data->keywords = trim($input->get('keywords', '', 'RAW'));
            $data->generator = trim($input->get('generator', '', 'RAW'));
            $data->robots = trim($input->get('robots', '', 'RAW'));

            $session->clear('efseo_data');

            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * Saves the entry into the database
     *
     * @return bool
     * @throws Exception
     */
    function store()
    {
        $row = $this->getTable('entry', 'EasyFrontendSeoTable');
        $data = array();

        // Get entered data
        $data['id'] = $this->input->get('id', '', 'INT');
        $data['url'] = trim($this->input->get('url', '', 'STRING'));
        $data['title'] = trim($this->input->get('title', '', 'STRING'));
        $data['description'] = trim(stripslashes(preg_replace('@\s+(\r\n|\r|\n)@', ' ', $this->input->get('description', '', 'STRING'))));
        $data['keywords'] = trim($this->input->get('keywords', '', 'STRING'));
        $data['generator'] = trim($this->input->get('generator', '', 'STRING'));
        $data['robots'] = trim($this->input->get('robots', '', 'STRING'));

        // Do not save same URLs more than once
        if($this->checkEntry($data['url']) == false)
        {
            $this->error = 'duplicate';

            return false;
        }

        if(!$row->save($data))
        {
            $this->error = 'database';

            return false;
        }

        return true;
    }

    /**
     * Checks whether an URL already exists
     *
     * @param $url
     *
     * @return bool
     */
    private function checkEntry($url)
    {
        $db = JFactory::getDbo();

        $query = "SELECT * FROM ".$db->quoteName('#__plg_easyfrontendseo')." WHERE ".$db->quoteName('url')." = ".$db->quote($url);
        $this->_db->setQuery($query);
        $row = $this->_db->loadAssoc();

        if(empty($row))
        {
            return true;
        }
        else
        {
            if($row['id'] == $this->id)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Updates the entries from the database in a batch process
     *
     * @return bool
     * @throws Exception
     */
    function batch()
    {
        $ids = $this->input->get('id', 0, 'ARRAY');
        $vars = $this->input->get('batch', array('generator' => '', 'robots' => ''), 'ARRAY');
        $row = $this->getTable('entry', 'EasyFrontendSeoTable');

        foreach($ids as $id)
        {
            $row->reset();
            $row->load($id);

            if(!empty($vars['generator']['activated']))
            {
                $row->generator = $vars['generator']['value'];
            }

            if(!empty($vars['robots']['activated']))
            {
                $row->robots = $vars['robots']['value'];
            }

            if(!$row->store())
            {
                $this->error = 'database';

                return false;
            }
        }

        return true;
    }

    /**
     * Deletes the entries from the database
     *
     * @return bool
     * @throws Exception
     */
    function delete()
    {
        $ids = $this->input->get('id', 0, 'ARRAY');
        $row = $this->getTable('entry', 'EasyFrontendSeoTable');

        foreach($ids as $id)
        {
            if(!$row->delete($id))
            {
                $this->error = 'database';

                return false;
            }
        }

        return true;
    }

    /**
     * Changes the state of the entries
     *
     * @param $state
     *
     * @return bool
     * @throws Exception
     */
    function publish($state)
    {
        $id = $this->input->get('id', 0, 'ARRAY');
        $row = $this->getTable('entry', 'EasyFrontendSeoTable');

        if(!$row->publish($id, $state))
        {
            $this->error = 'database';

            return false;
        }

        return true;
    }

    /**
     * Stores the input data into the session
     *
     * @param $input
     */
    public function storeInputSession($input)
    {
        $session = JFactory::getSession();
        $session->set('efseo_data', $input);

        return;
    }
}
