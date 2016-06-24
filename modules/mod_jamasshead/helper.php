<?php
/**
 * ------------------------------------------------------------------------
 * JA Masshead Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.html.parameter');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
/**
 *
 * JA MASSHEAD HELPER CLASS
 * @author JoomlArt
 *
 */
class ModJAMassheadHelper
{
    protected $_item = array();


    /**
     *
     * reference to the global ModJAMassheadHelper object
     * @Returns a reference to the global ModJAMassheadHelper object
     */
    static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            $instance = new ModJAMassheadHelper();
        }
        return $instance;
    }


    /**
     *
     * Get all information of masshead
     * @param object $params
     * @return Array
     */
    public function getMasshead($params)
    {
        //global $mainframe;
        $masshead 			= array();
        $masshead['title'] 	= '';
        $masshead['description'] = '';
        $masshead['params'] = array();
        //default title & description in configuration
        $default_title 			= trim($params->get('default-title'));
        $default_description 	= trim($params->get('default-description'));
        //config for specific masshead
        $config = $params->get('config');

        //get the inputs from request
        $view 	= JRequest::getCmd('view');
        $option = JRequest::getCmd('option');
        $layout = JRequest::getCmd('layout');
        $task 	= JRequest::getCmd('task');
        $id 	= JRequest::getInt('id');
        $Itemid = JRequest::getInt('Itemid');

        if (isset($config) && ($config != '')) {

            //support for multiple language
            $configArr 	 = preg_split('/<lang=([^>]*)>/', $config, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $description = '';

            //if language configured
            if (count($configArr) > 1) {

                //get the atribute configured for current language
                for ($i = 0; $i < count($configArr); $i = $i + 2) {
                    if ($configArr[$i] == $iso_client_lang) {
                        $description = $configArr[($i + 1)];
                        break;
                    }
                }
                //not found, get the first one
                if (!$description) {
                    $description = $configArr[1];
                }

            } else if (isset($configArr[0])) {

                //all languages are configured the same
                $description = $configArr[0];

            }

            //parse the configuration
            $configArr = $this->parseDescNew($description);

            foreach ($configArr as $config) {
                if(isset($config['Itemid'])){
                    $ItemidArray = explode(',', $config['Itemid']) ;
                    if (!empty($ItemidArray) && in_array($Itemid, $ItemidArray)) {

                        //if config for current page found
                        $masshead['title'] = @$config['title'];
                        $masshead['description'] = @$config['description'];
						$masshead['params'] = $config;

                        //don't need check for other condition if found
                        break;

                    }
                }
                if (isset($config['option']) && ($config['option'] != '')) {

                    //if config for current component found
                    if ($config['option'] == $option || "com_".$config['option'] == $option) {

                        $check = true;

                        //check if not match view/layout/task/id
                        if ((($config['view'] != '') 	&& ($config['view'] != $view)) ||
                            (($config['layout'] != '') 	&& ($config['layout'] != $layout)) ||
                            (($config['task'] != '') 	&& ($config['task'] != $task)) ||
                            (($config['id'] != '') 		&& ($config['id'] != $id))) {

                            $check = false;

                        }

                        if ($check) {

                            $masshead['title'] 			= $config['title'];
                            $masshead['description'] 	= $config['description'];

							$masshead['params'] = $config;
                           //don't need check for other condition if found
                            break;
                        }
                    }
                }

            }
        }

        //not specific configured, detect title & desc base on input
        if (!$masshead['title'] && !$masshead['description']) {

            $id = JRequest::getInt('id');

            if (($option == 'com_content') && ($view == 'article')) {

                //Get title & desc if this is article view
                $item = $this->loadArticle($id, $params);
				if ($item) {
					$masshead['title'] = trim($item->title);
					$masshead['description'] = trim($item->metadesc);
				}
            } else {

                //get from page title or default title configured in module
				$app	= JFactory::getApplication();
				$menus	= $app->getMenu();

				// Because the application sets a default page title,
				// we need to get it from the menu item itself
				$menu = $menus->getActive();

				if($menu && $menu->params->get('page_heading', '') != '') {
					$masshead['title'] = $menu->params->get('page_heading', '');
				}

            }
        }

        //default value if empty
        if (!$masshead['title']) {
            $masshead['title'] = $default_title;
        }
        if (!$masshead['description']) {
            $masshead['description'] = $default_description;
        }

        return $masshead;
    }


    /**
     * Parse the description to array
     * description in format
     * Format 1: [Masshead option="com_name" view="view_name" layout="layout_name" task="task_name" id="id" title="Title" ]Description here[/Masshead]
     * Format 2: [Masshead Itemid="page_id" title="Title" ]Description here[/Masshead]
     * @param string $description
     * @return array
     */
    public function parseDescNew($description)
    {

        $regex = '#\[Masshead ([^\]]*)\]([^\[]*)\[/Masshead\]#m';
        preg_match_all($regex, $description, $matches, PREG_SET_ORDER);
        $descriptionArray = array();

        foreach ($matches as $match) {

            $params = $this->parseParams($match[1]);
            $description = $match[2];

            if (is_array($params)) {

                if (isset($params['option'])) {
					$params['view'] 	= isset($params['view']) 	? trim($params['view']) : '';
					$params['layout'] 	= isset($params['layout']) 	? trim($params['layout']) : '';
					$params['task'] 	= isset($params['task']) 	? trim($params['task']) : '';
					$params['id'] 		= isset($params['id']) 		? trim($params['id']) : '';
					$params['title'] 	= isset($params['title']) 	? trim($params['title']) : '';
				}
                $params['description'] = $description;

				$descriptionArray[] = $params;
            }
        }

        return $descriptionArray;
    }


    /**
     *
     * Parse Params
     * @param string $string
     * @return array
     */
    public function parseParams($string)
    {
        $string = html_entity_decode($string, ENT_QUOTES);
        $regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
        $params = null;
        if (preg_match_all($regex, $string, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $key = $matches[1][$i];
                $value = $matches[3][$i] ? $matches[3][$i] : ($matches[4][$i] ? $matches[4][$i] : $matches[5][$i]);
                $params[$key] = $value;
            }
        }
        return $params;
    }


    /**
     *
     * Load Article title and metadesc
     * @param int $id
     * @param object $params
     * @return object
     */
    public function loadArticle($id, $params)
    {
		if (!$id) {
			return;
		}

        $mainframe = JFactory::getApplication();

        // Get the dbo
        $db = JFactory::getDbo();

        // Get an instance of the generic articles model
        
		if (version_compare(JVERSION, '3.0', 'ge')) {
			$model = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		} else if (version_compare(JVERSION, '2.5', 'ge')) {
		   	$model = JModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		} else {
			
			$model = JModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));
		}
        // Set application parameters in model
        $appParams = $mainframe->getParams();

        $model->setState('params', $appParams);

        $model->setState('filter.published', 1);
		$model->setState('filter.archived',2);

        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        $data = $model->getItem($id);

        return $data;

    }
}
