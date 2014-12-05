<?php
/**
 * @package   OSContent
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/models/model.php';

/**
 * Model Delete
 *
 * @since  1.0.0
 */
class OSContentModelDelete extends OSModelAbstract
{
    /**
     * @var    string  The prefix to use with controller messages.
     * @since  1.6
     */
    protected $text_prefix = 'COM_OSCONTENT_DELETE';

    /**
     * Model context string.
     *
     * @var  string
     */
    protected $_context = 'com_oscontent.delete';

    /**
     * Get the form
     *
     * @param   array $data     Data
     * @param   bool  $loadData Load data
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  Form
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_oscontent.delete',
            'delete',
            array('control' => 'jform', 'load_data' => $loadData)
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Get parent category
     *
     * @access    protected
     * @since     1.0.0
     *
     * @return  array
     */
    protected function getCategoryParent()
    {
        // Initialise variables.
        $options = array();

        try {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('a.id AS value, a.title AS text, a.level');
            $query->from('#__categories AS a');
            $query->join('LEFT', '`#__categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

            $extension = "com_content";
            $query->where('(a.extension = "com_content" OR a.parent_id = 0) AND a.id <> 1');

            /*
            // Prevent parenting to children of this item
            if ($id = $this->form->getValue('id'))
            {
                $query->join('LEFT', '`#__categories` AS p ON p.id = '.(int) $id);
                $query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

                $rowQuery   = $db->getQuery(true);
                $rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
                $rowQuery->from('#__categories AS a');
                $rowQuery->where('a.id = ' . (int) $id);
                $db->setQuery($rowQuery);
                $row = $db->loadObject();
            }
            */

            $query->where('a.published IN (0,1)');
            $query->group('a.id');
            $query->order('a.lft ASC');

            // Get the options.
            $db->setQuery($query);

            $options = $db->loadObjectList();

            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '<')) {
                // Check for a database error.
                if ($db->getErrorNum()) {
                    JError::raiseWarning(500, $db->getErrorMsg());
                }
            }
        } catch (Exception $e) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0, $n = count($options); $i < $n; $i++) {
            // Translate ROOT
            if ($options[$i]->level == 0) {
                $options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
            }

            $options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
        }

        // Initialise variables.
        $user = JFactory::getUser();

        if (empty($id)) {
            // New item, only have to check core.create.
            foreach ($options as $i => $option) {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.create', $extension . ' . category.' . $option->value)) {
                    unset($options[$i]);
                }
            }
        } else {
            // Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
            foreach ($options as $i => $option) {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise('core.edit', $extension . '.category.' . $option->value)) {
                    // As a backup, check core.edit.own
                    if (!$user->authorise('core.edit.own', $extension . '.category.' . $option->value)) {
                        // No core.edit nor core.edit.own - bounce this one
                        unset($options[$i]);
                    } else {
                        // TODO: I've got a funny feeling we need to check core.create here.
                        // Maybe you can only get the list of categories you are allowed to create in?
                        // Need to think about that. If so, this is the place to do the check.
                    }
                }
            }
        }

        if (isset($row) && !isset($options[0])) {
            if ($row->parent_id == '1') {
                $parent       = new stdClass;
                $parent->text = JText::_('JGLOBAL_ROOT_PARENT');
                array_unshift($options, $parent);
            }
        }

        return $options;
    }

    /**
     * Get Data
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function &getData()
    {
        $categories        = $this->getCategoryParent();
        $sectioncategories = 0;

        $lists['catid'] = JHTML::_('select.genericlist', $categories, 'catid', 'style="float: none;"', 'value', 'text');

        $lists['sectioncategories'] = $sectioncategories;

        return $lists;
    }

    public function deleteOSContent($option = null)
    {
        global $mainframe;
        $database = JFactory::getDBO();

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $catid             = JRequest::getVar('catid', '', 'POST');
            $deleteCategory    = JRequest::getVar('deleteCategory', '', 'POST');
            $deleteContentOnly = JRequest::getVar('deleteContentOnly', '', 'POST');
        } else {
            $input = JFactory::getApplication()->input;
            $catid = $input->getInt('catid');

            // TODO: Use int value for these fields
            $deleteCategory    = $input->getInt('deleteCategory') === 0;
            $deleteContentOnly = $input->getInt('deleteContentOnly') === 0;
        }

        $where = "";

        if ($catid > 0) {
            // A cat is selected
            if ($deleteCategory) {
                // Delete link menu-cat
                $query = "DELETE m FROM #__menu m "
                    . "\n WHERE m.component_id = " . $database->q($this->getExtensionId('com_content'))
                    . "\n AND LOCATE(\"category\", link) >0 AND LOCATE(\"com_content\", link) >0 AND LOCATE(\"id={$catid}\", link) >0";

                $database->setQuery($query);
                $database->query();

                // Delete cat
                $query = "DELETE FROM #__categories"
                    . "\n WHERE id=$catid"
                    . $where;

                $database->setQuery($query);
                $database->query();
            }

            if ($deleteContentOnly) {
                $query = "UPDATE #__content SET `introtext`='', `fulltext`='' "
                    . "\n WHERE catid=$catid"
                    . $where;
            } else {
                // Delete full content (article)
                $query = "DELETE co FROM #__content co "
                    . "\n WHERE co.catid=$catid"
                    . $where;
            }

            $database->setQuery($query);
            $database->query();
        }

        // Delete content
        return true;
    }
}
