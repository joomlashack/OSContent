<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2014 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.4
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

jimport('joomla.database.tableasset');

/**
 * Table Contentn
 *
 * @since  1.0.0
 */
class TableContentAbstract extends JTable
{
    /**
     * Get the parent asset id for the record
     *
     * @param   JTable $table Table
     * @param   int    $id    Asset ID
     *
     * @access    public
     * @since     1.6
     * @return  int
     */
    protected function _getAssetParentIdBase($table = null, $id = null)
    {
        // Initialise variables.
        $assetId = null;
        $db      = $this->getDbo();

        // This is a article under a category.
        if ($this->catid) {
            // Build the query to get the asset id for the parent category.
            $query = $db->getQuery(true);
            $query->select('asset_id');
            $query->from('#__categories');
            $query->where('id = ' . (int)$this->catid);

            // Get the asset id from the database.
            $this->_db->setQuery($query);

            if ($result = $this->_db->loadResult()) {
                $assetId = (int)$result;
            }
        }

        // Return the asset id.
        if ($assetId) {
            return $assetId;
        } else {
            return parent::_getAssetParentId($table, $id);
        }
    }
}

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    /**
     * Alias Class for TableContentAbstract in Joomla! < 3.0
     *
     * @since  1.9.1
     */
    class TableContentBase extends TableContentAbstract
    {
        /**
         * Override the _getAssetParentId mehtod to avoid a PHP Strict Standard warning.
         * The warning was because the method signature was different before J3.0.
         *
         * @param   JTable $table Table
         * @param   int    $id    Asset ID
         *
         * @access    public
         * @since     1.9.1
         * @return  int
         */
        protected function _getAssetParentId($table = null, $id = null)
        {
            return parent::_getAssetParentIdBase($table, $id);
        }
    }
} else {
    /**
     * Alias Class for TableContentAbstract in Joomla! >= 3.0
     *
     * @since  1.9.1
     */
    class TableContentBase extends TableContentAbstract
    {
        /**
         * Override the _getAssetParentId mehtod to avoid a PHP Strict Standard warning.
         * The warning was because the method signature has changed on J3.0.
         *
         * @param   JTable $table Table
         * @param   int    $id    Asset ID
         *
         * @access    public
         * @since     1.9.1
         * @return  int
         */
        protected function _getAssetParentId(JTable $table = null, $id = null)
        {
            return parent::_getAssetParentIdBase($table, $id);
        }
    }
}

/**
 * Content table
 *
 * @package     Joomla.Framework
 * @subpackage  Table
 * @since       1.0
 */
class TableContent extends TableContentBase
{
    /**
     * Class constructor method
     *
     * @param   database &$db A database connector object
     *
     * @access    public
     * @since     1.0.0
     */
    public function __construct(&$db)
    {
        parent::__construct('#__content', 'id', $db);
    }

    /**
     * Get asset name
     *
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @since    1.6.0
     *
     * @return    string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_content.article.' . (int)$this->$k;
    }

    /**
     * Get asset title
     *
     * Method to return the title to use for the asset table.
     *
     * @since    1.6.0
     *
     * @return    string
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $array  Named array
     * @param   string $ignore To Ignore
     *
     * @see        JTable:bind
     * @since      1.5.0
     *
     * @return    mixed   Null is operation was satisfactory, otherwise returns an error
     */
    public function bind($array, $ignore = '')
    {
        // Search for the {readmore} tag and split the text up accordingly.
        if (isset($array['articletext'])) {
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos  = preg_match($pattern, $array['articletext']);

            if ($tagPos == 0) {
                $this->introtext = $array['articletext'];
                $this->fulltext  = '';
            } else {
                list($this->introtext, $this->fulltext) = preg_split($pattern, $array['articletext'], 2);
            }
        }

        if (isset($array['attribs']) && is_array($array['attribs'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['attribs']);
            $array['attribs'] = (string)$registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }

        // Bind the rules
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new JRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check function
     *
     * @see        JTable::check
     * @since      1.5.0
     *
     * @return    boolean
     */
    public function check()
    {
        if (trim($this->title) == '') {
            $this->setError(JText::_('COM_OSCONTENT_WARNING_PROVIDE_VALID_NAME'));

            return false;
        }

        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }

        $this->alias = JApplication::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '') {
            $this->fulltext = '';
        }

        if (trim($this->introtext) == '' && trim($this->fulltext) == '') {
            $this->setError(JText::_('JGLOBAL_ARTICLE_MUST_HAVE_TEXT'));

            return false;
        }

        // Check the publish down date is not earlier than publish up
        if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
            // Swap the dates
            $temp               = $this->publish_up;
            $this->publish_up   = $this->publish_down;
            $this->publish_down = $temp;
        }

        /*
         * Clean up keywords -- eliminate extra spaces between phrases
         * and cr (\r) and lf (\n) characters from string
         */
        if (!empty($this->metakey)) {
            // Only process if not empty
            $bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
            $after_clean    = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
            $keys           = explode(',', $after_clean); // create array using commas as delimiter
            $clean_keys     = array();

            foreach ($keys as $key) {
                if (trim($key)) {
                    // Ignore blank keywords
                    $clean_keys[] = trim($key);
                }
            }

            // Put array back together delimited by ", "
            $this->metakey = implode(", ", $clean_keys);
        }

        return true;
    }

    /**
     * Overriden JTable::store to set modified data and user id.
     *
     * @param   boolean $updateNulls True to update fields even if they are null
     *
     * @since    1.6.0
     *
     * @return    boolean
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        if ($this->id) {
            // Existing item
            $this->modified    = $date->toMySQL();
            $this->modified_by = $user->get('id');
        } else {
            // New article. An article created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!intval($this->created)) {
                $this->created = $date->toMySQL();
            }

            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }

        // Verify that the alias is unique
        $table = JTable::getInstance('Content', 'Table');

        if ($table->load(
                array('alias' => $this->alias, 'catid' => $this->catid)
            ) && ($table->id != $this->id || $this->id == 0)
        ) {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS'));

            return false;
        }

        return parent::store($updateNulls);
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed   $pks    An optional array of primary key values to update.  If not set the instance property value is used.
     * @param   integer $state  The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer $userId The user id of the user performing the operation.
     *
     * @since    1.0.4
     *
     * @return    boolean
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        try {
            // Initialise variables.
            $k = $this->_tbl_key;

            // Sanitize input.
            JArrayHelper::toInteger($pks);
            $userId = (int)$userId;
            $state  = (int)$state;

            // If there are no primary keys set check to see if the instance key is set.
            if (empty($pks)) {
                if ($this->$k) {
                    $pks = array($this->$k);
                } else {
                    // Nothing to set publishing state on, return false.
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
                $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int)$userId . ')';
            }

            // TODO: Add $db->NameQuote
            // Update the publishing state for rows with the given primary keys.
            $this->_db->setQuery(
                'UPDATE `' . $this->_tbl . '`' .
                ' SET `state` = ' . (int)$state .
                ' WHERE (' . $where . ')' .
                $checkin
            );
            $this->_db->query();

            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                // Check for a database error.
                if ($this->_db->getErrorNum()) {
                    $this->setError($this->_db->getErrorMsg());

                    return false;
                }
            }
        } catch (Exception $e) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
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

    /**
     * Converts record to XML
     *
     * @param   boolean $mapKeysToText Map foreign keys to text values
     *
     * @since   1.5
     *
     * @return XML
     */
    public function toXML($mapKeysToText = false)
    {
        $db = JFactory::getDbo();

        if ($mapKeysToText) {
            $query = 'SELECT name'
                . ' FROM #__categories'
                . ' WHERE id = ' . (int)$this->catid;

            $db->setQuery($query);
            $this->catid = $db->loadResult();

            $query = 'SELECT name'
                . ' FROM #__users'
                . ' WHERE id = ' . (int)$this->created_by;
            $db->setQuery($query);
            $this->created_by = $db->loadResult();
        }

        return parent::toXML($mapKeysToText);
    }
}
