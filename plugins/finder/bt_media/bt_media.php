<?php

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');

require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class plgFinderBt_media extends FinderIndexerAdapter {

    protected $context = 'Media';
    protected $extension = 'com_bt_media';
    protected $layout = 'detail';
    protected $type_title = 'BT Media Item';
    protected $table = '#__bt_media_items';

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onFinderCategoryChangeState($extension, $pks, $value) {
        if ($extension == 'com_bt_media') {
            $this->categoryStateChange($pks, $value);
        }
    }

    public function onFinderDelete($context, $table) {
        if ($context == 'com_bt_media.detail') {
            $id = $table->id;
        } elseif ($context == 'com_finder.index') {
            $id = $table->link_id;
        } else {
            return true;
        }
        return $this->remove($id);
    }

    public function onFinderAfterSave($context, $row, $isNew) {
        if ($context == 'com_bt_media.detail') {
            if (!$isNew && $this->old_access != $row->access) {
                $this->itemAccessChange($row);
                $this->reindex($row->id);
            }
        }
        if ($context == 'com_bt_media.category') {
            if (!$isNew && $this->old_cataccess != $row->access) {
                $this->categoryAccessChange($row);
            }
        }

        return true;
    }

    public function onFinderBeforeSave($context, $row, $isNew) {
        if ($context == 'com_bt_media.detail') {
            if (!$isNew) {
                $this->checkItemAccess($row);
            }
        }
        if ($context == 'com_bt_media.category') {
            if (!$isNew) {
                $this->checkCategoryAccess($row);
            }
        }
        return true;
    }

    public function onFinderChangeState($context, $pks, $value) {
        if ($context == 'com_bt_media.detail' || $context == 'com_bt_media.category') {
            $this->itemStateChange($pks, $value);
        }
        if ($context == 'com_plugins.plugin' && $value === 0) {
            $this->pluginDisable($pks);
        }
    }

    protected function index(FinderIndexerResult $item, $format = 'html') {

        if (JComponentHelper::isEnabled($this->extension) == false) {
            return;
        }


        $registry = new JRegistry;
        $registry->loadString($item->params);
        $item->params = $registry;
        $item->summary = FinderIndexerHelper::prepareContent($item->summary, $item->params);
        $item->url = $this->getURL($item->id, $this->extension, $this->layout);
        $item->route = 'index.php?option=com_bt_media&view=detail&id=' . $item->id . ':' . $item->alias;
        if ($item->catslug > 0) {
            $item->route.= '&catid_rel=' . $item->catslug;
        }
        $item->path = FinderIndexerHelper::getContentPath($item->route);
        $title = $this->getItemMenuTitle($item->url);

        if (!empty($title) && $this->params->get('use_menu_title', true)) {
            $item->title = $title;
        } else {
            $item->title = $item->name;
        }
        // Handle the link to the meta-data.	
        $meta = json_decode($item->params);
        foreach ($meta AS $key) {
            $item->addInstruction(FinderIndexer::META_CONTEXT, $key->metakey);
            $item->addInstruction(FinderIndexer::META_CONTEXT, $key->metadesc);
            $item->addInstruction(FinderIndexer::META_CONTEXT, $key->author);
        }
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'link');
        // Add the type taxonomy data.
        $item->addTaxonomy('Type', 'Detail');
        // Add the category taxonomy data.	
        $item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
        // Add the language taxonomy data.
        $item->addTaxonomy('Language', $item->language);
        // Add the type taxonomy data.
        $item->addTaxonomy('Author', $item->author);
        // Get content extras.
        FinderIndexerHelper::getContentExtras($item);
        // Index the item.
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $this->indexer->index($item);
        } else {
            FinderIndexer::index($item);
        }
    }

    protected function setup() {
        return true;
    }

    protected function getListQuery($sql = null) {
        $db = JFactory::getDbo();
        // Check if we can use the supplied SQL query.

        if (version_compare(JVERSION, '3.0.0', 'ge')) {
            $sql = $sql instanceof JDatabaseQuery ? $sql : $db->getQuery(true);
        } else {
            $sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
        }
        $sql->select('a.id, a.cate_id, a.name, a.alias, a.description AS summary ');
        $sql->select('a.featured, a.hits, a.state, a.vote_count, a.vote_sum');
        $sql->select('a.created_date, a.created_by, a.ordering, a.checked_out, a.checked_out_time ');
        $sql->select('a.access , a.language, a.source_of_media, a.params');
        $sql->select('c.name AS category, c.state AS cat_state, c.access AS cat_access');

        // Handle the alias CASE WHEN portion of the query
        $case_when_item_alias = ' CASE WHEN ';
        $case_when_item_alias .= $sql->charLength('a.alias');
        $case_when_item_alias .= ' THEN ';
        $a_id = $sql->castAsChar('a.id');
        $case_when_item_alias .= $sql->concatenate(array($a_id, 'a.alias'), ':');
        $case_when_item_alias .= ' ELSE ';
        $case_when_item_alias .= $a_id . ' END as slug';
        $sql->select($case_when_item_alias);

        $sql->select('u.name AS author');
        $sql->from('#__bt_media_items AS a');
        $sql->join('INNER', "#__bt_media_categories AS c ON  a.cate_id = c.id");
        $sql->join('INNER', '#__users AS u ON u.id = a.created_by');
        return $sql;
    }

}
