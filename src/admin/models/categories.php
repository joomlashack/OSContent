<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/models/model.php';

/**
 * Model Categories
 *
 * @since  1.0.0
 */
class OSContentModelCategories extends OSModelAbstract
{
    /**
     * @var    string  The prefix to use with controller messages.
     * @since  1.6
     */
    protected $text_prefix = 'COM_OSCONTENT_CATEGORIES';

    /**
     * Model context string.
     *
     * @var  string
     */
    protected $_context = 'com_oscontent.categories';

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
            'com_oscontent.categories',
            'categories',
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
            $query->where('(a.extension = "com_content" OR a.parent_id = 0)');

            /*
            // Prevent parenting to children of this item.
            if ($id = $this->form->getValue('id')) {
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
                if (!$user->authorise('core.create', $extension . '.category.' . $option->value)) {
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
        global $my;
        $mainframe = JFactory::getApplication();
        $database  = JFactory::getDBO();

        $uid    = 0;
        $scope  = "content";
        $option = "com_oscontent";

        $row = $this->getTable();

        // Load the row from the db table
        $row->load((int)$uid);

        $row->scope     = $scope;
        $row->published = 1;
        $menus          = array();

        $javascript2      = "onchange=\"changeDynaList('menuselect3', menulist, document.adminForm.menuselect.options[document.adminForm.menuselect.selectedIndex].value, 0, 0);\"";
        $categoriesparent = $this->getCategoryParent();
        $lists['cate']    = JHTML::_(
            'select.genericlist',
            $categoriesparent,
            'parent_id',
            'class="inputbox" size="1"',
            'value',
            'text',
            null
        );


        // Build the html select list for section types
        $types[]            = JHTML::_('select.option', '', 'Select Type');
        $types[]            = JHTML::_('select.option', 'content_category', 'Category List Layout');
        $types[]            = JHTML::_('select.option', 'content_blog_category', 'Category Blog Layout');
        $lists['link_type'] = JHTMLSelect::genericList(
            $types,
            'link_type',
            'class="inputbox" size="1"',
            'value',
            'text'
        );


        $menuTypes = $this->getMenuTypes();

        foreach ($menuTypes as $menuType) {
            $menu[] = JHTML::_('select.option', $menuType->menutype, $menuType->title);
        }

        // Build the html select list for the group access
        $lists['access'] = JHtml::_('access.assetgrouplist', 'access', $row->access);

        // Vuild the html radio buttons for published
        $lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);

        $stop_notice          = array();
        $lists['menuselect3'] = JHTML::_(
            'select.genericlist',
            $stop_notice,
            'menuselect3',
            'class="inputbox" size="10"',
            'value',
            'text',
            null
        );

        $lists['menulist'] = $this->createSubMenu();

        $lists['menuselect'] = JHTML::_(
            'select.genericlist',
            $menu,
            'menuselect',
            'class="inputbox" size="10" ' . $javascript2,
            'value',
            'text',
            null
        );

        return $lists;
    }

    /**
     * Get a list of the menutypes
     *
     * @return array An array of menu type names
     */
    public function getMenuTypes()
    {
        $db    = JFactory::getDBO();
        $query = 'SELECT a.menutype, a.title' .
            ' FROM #__menu_types AS a';
        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }

    /**
     * Create submenu
     *
     * @access    public
     * @since     1.0.0
     *
     * @return  array
     */
    public function createSubMenu()
    {
        // Build the html select list for menu selection
        $menulist  = array();
        $database  = JFactory::getDBO();
        $menuTypes = $this->getMenuTypes();

        foreach ($menuTypes as $menuType) {
            $menuType = $menuType->menutype;

            $query = 'SELECT id, parent_id, title, menutype' .
                ' FROM #__menu' .
                ' WHERE menutype = "' . $menuType . '" AND published = 1' .
                ' ORDER BY menutype, parent_id,mlft';
            $database->setQuery($query);
            $menuItems4 = $database->loadObjectList();

            $children = array();

            if ($menuItems4) {
                // First pass - collect children
                // Iterate through the menu items
                foreach ($menuItems4 as $v) {
                    // We use parent as our array index
                    $pt = $v->parent_id;

                    // If an array entry for the parent doesn't exist, we create a new array
                    $list = @$children[$pt] ? $children[$pt] : array();

                    // We push our item onto the array (either the existing one for the specified parent or the new one
                    array_push($list, $v);

                    // We put out updated list into the array
                    $children[$pt] = $list;
                }
            }

            // Second pass - get an indent list of the items
            $list       = JHTML::_('menu.treerecurse', @$menuItems4[0]->parent_id, '-', array(), $children, 9999, 0, 0);
            $menulist[] = $list;
            ///////////////////////////////////////////////////
        }

        return $menulist;
    }

    /**
     * Save the category
     *
     * @param array $option Options
     *
     * @since     1.0.0
     *
     * @return  bool
     */
    public function saveOSCategories($option = null)
    {
        $post = JFactory::getApplication()->input->getArray(
            array(
                'title'       => 'ARRAY',
                'alias'       => 'ARRAY',
                'published'   => 'INT',
                'access'      => 'INT',
                'parent_id'   => 'INT',
                'menuselect'  => 'STRING',
                'link_type'   => 'STRING',
                'menuselect3' => 'STRING',
                'addMenu'     => 'INT'
            )
        );

        for ($i = 0; $i < count($post["title"]); $i++) {
            if ($post["title"][$i] == "") {
                continue;
            }

            $table        = $this->getTable();
            $table->id    = 0;
            $table->title = $post["title"][$i];
            $table->alias = JFilterOutput::stringURLSafe($post["alias"][$i]);

            if (trim(str_replace('-', '', $table->alias)) == '') {
                $table->alias = str_replace(' ', '-', strtolower($post["title"][$i]));
            }

            $table->extension = "com_content";
            $table->published = $post["published"];
            $table->access    = $post["access"];
            $table->language  = "*";
            $table->setLocation($post["parent_id"], 'last-child');

            if (!$table->store()) {
                return false;
            }

            if (@$post["addMenu"] === 1 || @$post['addMenu'] === 'on') {
                $this->menuLink(
                    $table->id,
                    $table->title,
                    $post["menuselect"],
                    $post["link_type"],
                    $post["menuselect3"],
                    $table->alias
                );
            }
        }

        return true;
    }

    /**
     * Link the content to the menu
     *
     * @param   int    $id          The id of the content to insert
     * @param   string $title       The  title of the menu element
     * @param   string $menuselect  The menu where to create the link
     * @param   string $contentType to know the kind of content (static content or not)
     * @param   int    $parent      Parent
     * @param   string $alias       Alias
     *
     * @return void
     */
    public function menuLink($id, $title, $menuselect, $contentType, $parent, $alias = "")
    {
        $mainframe = JFactory::getApplication();
        $database  = JFactory::getDBO();

        $menu = strval($menuselect);
        $link = strval($title);

        $link = stripslashes(JFilterOutput::ampReplace($link));

        // Find what kind of link needs to be created in $row->link
        switch ($contentType) {
            case "content_section":
                $taskLink = "section";
                break;

            case "content_blog_section":
                $taskLink = "section&layout=blog";
                break;

            case "content_category":
                $taskLink = "category";
                break;

            case "content_blog_category":
                $taskLink = "category&layout=blog";
                break;

            default:
                $taskLink = "article";
        }

        $row           = JTable::getInstance('menu');
        $row->menutype = $menu;
        $row->title    = $link;
        $row->alias    = $alias ? JFilterOutput::stringURLSafe($alias) : JFilterOutput::stringURLSafe($link);

        if (trim(str_replace('-', '', $row->alias)) == '') {
            $row->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        $row->parent_id = ($parent == -1) ? 1 : $parent;
        $row->type      = 'component';
        $row->link      = 'index.php?option=com_content&view=' . $taskLink . '&id=' . $id;
        $row->published = 1;
        $row->language  = "*";

        $row->component_id = $this->getExtensionId('com_content');

        $params                          = array();
        $params['display_num']           = 10;
        $params['show_headings']         = 1;
        $params['show_date']             = 0;
        $params['date_format']           = "";
        $params['filter']                = 1;
        $params['filter_type']           = "title";
        $params['orderby_sec']           = "";
        $params['show_pagination']       = 1;
        $params['show_pagination_limit'] = 1;
        $params['show_noauth']           = "";
        $params['show_title']            = "";
        $params['link_titles']           = "";
        $params['show_intro']            = "";
        $params['show_section']          = "";
        $params['link_section']          = "";
        $params['show_category']         = "";
        $params['link_category']         = "";
        $params['show_author']           = "";
        $params['show_create_date']      = "";
        $params['show_modify_date']      = "";
        $params['show_item_navigation']  = "";
        $params['show_readmore']         = "";
        $params['show_vote']             = "";
        $params['show_icons']            = "";
        $params['show_pdf_icon']         = "";
        $params['show_print_icon']       = "";
        $params['show_email_icon']       = "";
        $params['show_hits']             = "";
        $params['feed_summary']          = "";
        $params['page_title']            = "";
        $params['show_page_title']       = 1;
        $params['pageclass_sfx']         = "";
        $params['menu_image']            = "";
        $params['secure']                = 0;

        $registry = new Registry();
        $registry->loadArray($params);
        $row->params = (string)$registry;

        $row->setLocation($row->parent_id, 'last-child');

        if (!$row->check()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        if (!$row->store()) {
            echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
            exit();
        }
    }
}
