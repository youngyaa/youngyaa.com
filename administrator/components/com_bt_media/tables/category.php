<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.0.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die;

/**
 * category Table class
 */
class Bt_mediaTablecategory extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__bt_media_categories', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {

        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }

        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new JRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Redefined asset name, as we support action control
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_bt_media.category.' . (int) $this->$k;
    }

    /**
     * We provide our global ACL as parent
     * @see JTable::_getAssetParentId()
     */
    protected function _getAssetParentId($table = null, $id = null) {
        $asset = JTable::getInstance('Asset');
        if ($this->parent_id) {
			//Get parent category asset id
            $asset->loadByName('com_bt_media.category.' . $this->parent_id);
        } else {
			//Get component asset id
            $asset->loadByName('com_bt_media');
        }
        if($asset->id){
			return $asset->id;
		}else{
			// return root asset id
			return parent::_getAssetParentId($table, $id);
		}
    }

    /**
     * Overloaded check function
     */
    public function check() {

        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }

        return parent::check();
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0) {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
                'UPDATE `' . $this->_tbl . '`' .
                ' SET `state` = ' . (int) $state .
                ' WHERE (' . $where . ')' .
                $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin each row.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');
        return true;
    }

    public function delete($pk = null) {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets) {
            // Get and the asset name.
            $this->$k = $pk;
            $name = $this->_getAssetName();
            $asset = JTable::getInstance('Asset');

            if ($asset->loadByName($name)) {
                if (!$asset->delete()) {
                    $this->setError($asset->getError());
                    return false;
                }
            } else {
                $this->setError($asset->getError());
                return false;
            }
        }

        // Delete the row by primary key.
        if ($this->checkCanDoDeleteCategory($pk)) {
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this->_tbl);
            $query->where($this->_tbl_key . ' = ' . $this->_db->quote($pk));
            $this->_db->setQuery($query);

            // Check for a database error.
            if (!$this->_db->execute()) {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
                $this->setError($e);
                return false;
            }
        } else {
            $e = new JException(JText::_("Can't delete this category. Because this category contain items"));
//            $this->setError($e);
            JError::raiseWarning('100', $e);
            return FALSE;
        }

        return true;
    }

    private function checkCanDoDeleteCategory($cate_id) {
        $is_ok = FALSE;
        $query = $this->_db->getQuery(TRUE);
        $query->select('a.*');
        $query->from('#__bt_media_items as a');
        $query->where("a.cate_id = '" . $cate_id . "'");
        $this->_db->setQuery($query);

        $rs = $this->_db->loadObjectList();
        if (empty($rs)) {
            $is_ok = TRUE;
        }
        return $is_ok;
    }

}
