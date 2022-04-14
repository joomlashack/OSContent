<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2022 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSContent.
 *
 * OSContent is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSContent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSContent.  If not, see <http://www.gnu.org/licenses/>.
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

jimport('joomla.database.tablenested');

/**
 * Base Class for TableCategoriesBase, for J3.0 backward compatibility
 *
 * @since  1.9.1
 */
class TableCategoriesAbstract extends JTableNested
{
    /**
     * Get the parent asset id for the record
     *
     * @param   JTable $table Table
     * @param   int    $id    Asset ID
     *
     * @since 1.9.1
     *
     * @return    int
     */
    protected function _getAssetParentIdBase($table = null, $id = null)
    {
        // Initialise variables.
        $assetId = null;
        $db      = $this->getDbo();

        // This is a category under a category.
        if ($this->parent_id > 1) {
            // Build the query to get the asset id for the parent category.
            $query = $db->getQuery(true);
            $query->select('asset_id');
            $query->from('#__categories');
            $query->where('id = ' . (int)$this->parent_id);

            // Get the asset id from the database.
            $db->setQuery($query);
            if ($result = $db->loadResult()) {
                $assetId = (int)$result;
            }
        } elseif ($assetId === null) {
            // This is a category that needs to parent with the extension.

            // Build the query to get the asset id for the parent category.
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__assets');
            $query->where('name = ' . $db->quote($this->extension));

            // Get the asset id from the database.
            $db->setQuery($query);
            if ($result = $db->loadResult()) {
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

/**
 * Alias Class for TableCategoriesAbstract in Joomla! >= 3.0
 *
 * @since  1.9.1
 */
class TableCategoriesBase extends TableCategoriesAbstract
{
    /**
     * Get the parent asset id for the record.
     * Fix the PHP Strict Warning about different method signature
     *
     * @param   JTable $table Table
     * @param   int    $id    Asset ID
     *
     * @access    public
     * @since     1.9.1
     *
     * @return  int
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        return parent::_getAssetParentIdBase($table, $id);
    }
}


/**
 * Category table
 *
 * @package     Joomla.Framework
 * @subpackage  Table
 * @since       1.0.0
 */
class TableCategories extends TableCategoriesBase
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
        parent::__construct('#__categories', 'id', $db);

        $this->access = (int)JFactory::getConfig()->get('access');
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return    string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return $this->extension . '.category.' . (int)$this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @since    1.6
     *
     * @return    string
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * Override check function
     *
     * @see        JTable::check
     * @since      1.5
     *
     * @return    boolean
     */
    public function check()
    {
        // Check for a title.
        if (trim($this->title) == '') {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));

            return false;
        }

        $this->alias = trim($this->alias);

        if (empty($this->alias)) {
            $this->alias = $this->title;
        }

        $this->alias = JApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }

    /**
     * Overloaded bind function.
     *
     * @param   array  $array  Named array
     * @param   string $ignore To Ignore
     *
     * @see        JTable:bind
     * @since      1.5
     *
     * @return    null|string    null is operation was satisfactory, otherwise returns an error
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new Registry();
            $registry->loadArray($array['params']);
            $array['params'] = (string)$registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new JRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overriden JTable::store to set created/modified and user id.
     *
     * @param   boolean $updateNulls True to update fields even if they are null
     *
     * @since    1.6
     *
     * @return    boolean    True on success
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $date = $date->toSQL();

        if ($this->id) {
            // Existing category
            $this->modified_time    = $date;
            $this->modified_user_id = $user->get('id');
        } else {
            // New category
            $this->created_time    = $date;
            $this->created_user_id = $user->get('id');
        }

        // Verify that the alias is unique
        $table = JTable::getInstance('Category', 'JTable');

        $params = array(
            'alias'     => $this->alias,
            'parent_id' => $this->parent_id,
            'extension' => $this->extension
        );

        if ($table->load($params) && ($table->id != $this->id || $this->id == 0)) {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_CATEGORY_UNIQUE_ALIAS'));

            return false;
        }

        return parent::store($updateNulls);
    }
}
