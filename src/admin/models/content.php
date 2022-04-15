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

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

class OSContentModelContent extends OscontentModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_CONTENT';

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_oscontent.content',
            'content',
            ['load_data' => $loadData]
        );

        return $form;
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
    public function menuLink($id, $title, $menuselect, $contentType, $parent, $alias = '')
    {
        $menu = strval($menuselect);
        $link = strval($title);

        $link = stripslashes(OutputFilter::ampReplace($link));

        switch ($contentType) {
            case 'content_section':
                $taskLink = 'section';
                break;

            case 'content_blog_section':
                $taskLink = 'section&layout=blog';
                break;

            case 'content_category':
                $taskLink = 'category';
                break;

            case 'content_blog_category':
                $taskLink = 'category&layout=blog';
                break;

            default:
                $taskLink = 'article';
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
        $row->language  = '*';

        $row->component_id = $this->getExtensionId('com_content');

        $params                          = array();
        $params['display_num']           = 10;
        $params['show_headings']         = 1;
        $params['show_date']             = 0;
        $params['date_format']           = '';
        $params['filter']                = 1;
        $params['filter_type']           = 'title';
        $params['orderby_sec']           = '';
        $params['show_pagination']       = 1;
        $params['show_pagination_limit'] = 1;
        $params['show_noauth']           = '';
        $params['show_title']            = '';
        $params['link_titles']           = '';
        $params['show_intro']            = '';
        $params['show_section']          = '';
        $params['link_section']          = '';
        $params['show_category']         = '';
        $params['link_category']         = '';
        $params['show_author']           = '';
        $params['show_create_date']      = '';
        $params['show_modify_date']      = '';
        $params['show_item_navigation']  = '';
        $params['show_readmore']         = '';
        $params['show_vote']             = '';
        $params['show_icons']            = '';
        $params['show_pdf_icon']         = '';
        $params['show_print_icon']       = '';
        $params['show_email_icon']       = '';
        $params['show_hits']             = '';
        $params['feed_summary']          = '';
        $params['page_title']            = '';
        $params['show_page_title']       = 1;
        $params['pageclass_sfx']         = '';
        $params['menu_image']            = '';
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

    /**
     * @return array
     */
    public function getPostData(): array
    {
        $input  = Factory::getApplication()->input;

        $post = $input->getArray([
            'title'            => 'ARRAY',
            'alias'            => 'ARRAY',
            'published'        => 'STRING',
            'access'           => 'INT',
            'created_by'       => 'INT',
            'created_by_alias' => 'STRING',
            'created'          => 'DATE',
            'catid'            => 'INT',
            'publish_up'       => 'DATE',
            'publish_down'     => 'DATE',
            'metadesc'         => 'ARRAY',
            'metakey'          => 'ARRAY',
            'addMenu'          => 'INT',
            'menuselect'       => 'STRING',
            'menuselect3'      => 'STRING',
            'featured'         => 'INT'
        ]);

        if (empty($post['title']) == false) {
            for ($i = 0; $i < count($post["title"]); $i++) {
                $index                       = $i + 1;
                $post['introtext_' . $index] = $input->get('introtext_' . $index, '', 'raw');
                $post['fulltext_' . $index]  = $input->get('fulltext_' . $index, '', 'raw');
            }
        }

        return $post;
    }

    /**
     * Save the content
     *
     * @param array $option Options
     *
     * @access    public
     * @return  array
     * @since     1.0.0
     *
     */
    public function saveOSContent($option = null)
    {

        $post = $this->getPostData();

        for ($i = 0; $i < count($post["title"]); $i++) {
            $index = $i + 1;

            if ($post["title"][$i] == "") {
                continue;
            }
            $row = $this->getTable();

            $row->title = $post["title"][$i];

            if ($post["alias"][$i]) {
                $row->alias = JFilterOutput::stringURLSafe($post["alias"][$i]);
            } else {
                $row->alias = JFilterOutput::stringURLSafe($row->title);
            }

            if (trim(str_replace('-', '', $row->alias)) == '') {
                $row->alias = JFactory::getDate()->format('Y-m-d-H-i-s') . "-" . $i;
            }

            $intro_text      = $post['introtext_' . $index];
            $full_text       = $post['fulltext_' . $index];
            $row->introtext  = ($intro_text != "" ? $intro_text : $full_text);
            $row->fulltext   = ($intro_text != "" ? $full_text : "");
            $row->metakey    = $post["metakey"][$i];
            $row->metadesc   = $post["metadesc"][$i];
            $row->catid      = $post["catid"];
            $row->access     = $post["access"];
            $row->language   = "*";
            $row->created_by = $post["created_by"];
            if (Version::MAJOR_VERSION == 4) {
                $row->images      = '{"image_intro":"","image_intro_alt":"","float_intro":"","image_intro_caption":"","image_fulltext":"","image_fulltext_alt":"","float_fulltext":"","image_fulltext_caption":""}';
                $row->urls        = '{"urla":"","urlatext":"","targeta":"","urlb":"","urlbtext":"","targetb":"","urlc":"","urlctext":"","targetc":""}';
                $row->attribs     = '{"article_layout":"","show_title":"","link_titles":"","show_tags":"","show_intro":"","info_block_position":"","info_block_show_title":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_page_title":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
                $row->modified    = JFactory::getDate()->format('Y-m-d-H-i-s');
                $row->modified_by = $post['created_by'];
            }

            if (isset($post["created_by_alias"])) {
                $row->created_by_alias = $post["created_by_alias"];
            }

            $robots = isset($post["robots"]) ? $post["robots"] : "";
            $author = $post["created_by"];

            // TODO: implement the metadata/robots
            $row->metadata = "";
            if ($robots != "") {
                $row->metadata = "robots=" . $robots . "\n";
            }
            // TODO: implement the author_alias
            if ($author != "") {
                $row->metadata .= "author=" . $author;
            }

            if ($row->metadata == "") {
                $row->metadata = "robots=
                                  author=";
            }

            if ($post["created"]) {
                $row->created = JFactory::getDate($post["created"]);

                $row->created = $row->created->toSQL();
            }

            if ($post["publish_up"]) {
                $row->publish_up = JFactory::getDate($post["publish_up"]);

                $row->publish_up = $row->publish_up->toSQL();
            }

            if ($post["publish_down"] && trim($post["publish_down"]) != JText::_('COM_OSCONTENT_NEVER')) {
                $row->publish_down = JFactory::getDate($post["publish_down"]);

                $row->publish_down = $row->publish_down->toSQL();

            } elseif (trim($post["publish_down"]) == JText::_('COM_OSCONTENT_NEVER')) {
                $post["publish_down"] = JFactory::getDBO()->getNullDate();

                $row->publish_down = JFactory::getDate($post["publish_down"]);

                $row->publish_down = $row->publish_down->toSQL();
            }

            // Handle state
            if (isset($post["published"]) && $post["published"]) {
                $row->state = 1;
            } else {
                $row->state = 0;
            }

            $table = JTable::getInstance('Content', 'Table');

            $params = array(
                'alias' => $row->alias,
                'catid' => $row->catid
            );

            if ((bool)$post['featured']) {
                $row->featured = 1;
            }

            if ($table->load($params) && ($table->id != $row->id || $row->id == 0)) {
                throw new Exception(JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS') . ": " . $row->alias, 500);
            }

            $articleData = $row->getProperties();

            $model = \Alledia\Framework\Helper::getContentModel('article', 'Administrator');

            if (!$model->save((array)$row)) {
                return false;
            }
            $row->id = $model->getState('article.id');

            // $row->reorder('catid = ' . (int)$row->catid . ' AND state >= 0');

            if (@$post["addMenu"] === 1 || @$post['addMenu'] === 'on') {
                $type = "content_item_link";
                $this->menuLink($row->id, $row->title, @$post["menuselect"], $type, @$post["menuselect3"], $row->alias);
            }

            if ((bool)$post['featured']) {
                $db = JFactory::getDBO();
                $fp = new ContentTableFeatured($db);

                // Is the item already viewable on the frontpage?
                // Insert the new entry
                $query = 'INSERT INTO #__content_frontpage' .
                    ' VALUES (' . (int)$row->id . ', 1)';
                $db->setQuery($query);

                if (!$db->query()) {
                    throw new Exception($db->stderr(), 500);
                }

                $fp->ordering = 1;
                $fp->reorder();
            }
        }

        return true;
    }
}
