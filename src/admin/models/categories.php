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

use Alledia\Framework\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

class OSContentModelCategories extends OscontentModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_CATEGORIES';

    /**
     * @var CategoriesModelCategory
     */
    protected $categoryModel = null;

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_oscontent.categories',
            'categories',
            ['load_data' => $loadData]
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function loadFormData()
    {
        return Factory::getApplication()->getUserState('com_oscontent.edit.categories.data', []);
    }

    /**
     * @return CategoriesModelCategory
     */
    protected function getCategoryModel()
    {
        if ($this->categoryModel === null) {
            $this->categoryModel = Helper::getCategoryModel('Category', 'Administrator');
        }

        return $this->categoryModel;
    }

    /**
     * @inheritDoc
     */
    public function getTable($name = '', $prefix = 'Table', $options = [])
    {
        return $this->getCategoryModel()->getTable();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function validate($form, $data, $group = null)
    {
        return $this->getPostData();
    }

    /**
     * @inheritDoc
     *
     */
    public function save($data)
    {
        /**
         * @var CategoriesModelCategory $model
         */
        $model = Helper::getCategoryModel('Category', 'Administrator');

        $commonData = array_filter([
            'parent_id' => (int)$data['parent'],
            'access'    => (int)$data['access'],
            'published' => (int)$data['published'],
            'extension' => 'com_content',
            'note'      => Text::_('COM_OSCONTENT_NOTE_CREATEDBY'),
            'language'  => '*'
        ]);

        $errors = [];
        foreach ($data['title'] as $index => $title) {
            if (empty($title)) {
                continue;
            }

            $alias = $data['alias'][$index] ?: $title;

            $newCategory = array_merge([
                'title' => $title,
                'alias' => OutputFilter::stringURLSafe($alias)
            ],
                $commonData
            );

            $model->setState('category.id');
            if ($model->save($newCategory) == false) {
                $errors[] = sprintf(
                    '%02d: %s (%s): %s',
                    $index + 1,
                    $title,
                    $alias,
                    $model->getError()
                );
            }
        }

        if ($errors) {
            $this->setError('<br>' . join('<br>', $errors));
            return false;
        }

        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getPostData(): array
    {
        $input = Factory::getApplication()->input->post;

        return $input->getArray([
            'title'      => 'ARRAY',
            'alias'      => 'ARRAY',
            'parent'     => 'INT',
            'access'     => 'INT',
            'published'  => 'INT',
            'menuselect' => 'INT',
            'link_type'  => 'STRING'
        ]);
    }

    /**
     * Link the content to the menu
     *
     * @param int    $id          The id of the content to insert
     * @param string $title       The  title of the menu element
     * @param string $menuselect  The menu where to create the link
     * @param string $contentType to know the kind of content (static content or not)
     * @param int    $parent      Parent
     * @param string $alias       Alias
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
