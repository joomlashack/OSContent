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

use Alledia\Framework\Factory;
use Alledia\Framework\Helper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Content as ContentTable;
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
     * @inheritDoc
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_oscontent.edit.content.data', []);

        return $data;
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

        $params                          = [];
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
        $input = Factory::getApplication()->input;

        $post = $input->getArray([
            'title'            => 'array',
            'alias'            => 'array',
            'introtext'        => 'array',
            'fulltext'         => 'array',
            'state'            => 'int',
            'access'           => 'int',
            'created_by'       => 'int',
            'created_by_alias' => 'string',
            'created'          => 'date',
            'catid'            => 'int',
            'publish_up'       => 'date',
            'publish_down'     => 'date',
            'metadesc'         => 'array',
            'metakey'          => 'array',
            'addmenu'          => 'int',
            'menuselect'       => 'int',
            'featured'         => 'int'
        ]);

        return $post;
    }

    /**
     * @inheritDoc
     */
    public function validate($form, $data, $group = null)
    {
        return $this->getPostData();
    }

    /**
     * @inheritDoc
     */
    public function save($data)
    {
        /**
         * @var ContentModelArticle $model
         * @var ContentTable        $table
         */
        $model = Helper::getContentModel('Article', 'Administrator');
        $table = $model->getTable();

        $metadata = array_filter([
            empty($post['robots']) ? '' : 'robots=' . $post['robots'],
            empty($author) ? '' : 'author=' . $author
        ]);

        if (empty($metadata)) {
            $metadata = [
                'robots=',
                'author='
            ];
        }
        $metadata = join("\n", $metadata);

        $commonData = array_filter([
            'featured'         => (int)$data['featured'],
            'created_by'       => $data['created_by'] ?? '',
            'created_by_alias' => $data['created_by_alias'] ?? '',
            'state'            => (int)$data['state'],
            'catid'            => (int)$data['catid'] ?: 0,
            'access'           => (int)$data['access'] ?: '',
            'language'         => '*',
            'metadata'         => $metadata,
            'created'          => empty($data['created'])
                ? ''
                : Factory::getDate($data['created'])->toSql(),
            'publish_up'       => empty($data['publish_up'])
                ? ''
                : Factory::getDate($data['publish_up'])->toSql(),
            'publish_down'     => empty($data['publish_down'])
                ? ''
                : Factory::getDate($data['publish_down'])->toSql(),
        ]);

        $newArticles = [];
        $errors      = [];
        foreach ($data['title'] as $index => $title) {
            if (empty($title)) {
                continue;
            }

            $alias = $data['alias'][$index] ?: $title;

            $currentArticle = [
                'title'     => $title,
                'alias'     => OutputFilter::stringURLSafe($alias),
                'introtext' => $data['introtext'][$index] ?? '',
                'fulltext'  => $data['fulltext'][$index] ?? '',
            ];

            foreach ($commonData as $property => $value) {
                $currentArticle[$property] = $value;
            }

            if (
                $table->load([
                    'alias' => $currentArticle['alias'],
                    'catid' => $currentArticle['catid']
                ])
            ) {
                $errors[] = sprintf(
                    '%02d. %s: %s',
                    $index + 1,
                    $currentArticle['alias'],
                    Text::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS')
                );

                continue;
            }

            $newArticles[$index] = $currentArticle;
        }

        if ($errors) {
            $this->setError('<br>' . join('<br>', $errors));
            return false;
        }

        foreach ($newArticles as $index => $newArticle) {
            $model->setState('article.id', null);

            if ($model->save($newArticle) == false) {
                $errors[] = sprintf(
                    '%02d. %s/%s: %s',
                    $index + 1,
                    $newArticle['title'],
                    $newArticle['alias'],
                    $model->getError()
                );
            }
        }

        if ($errors) {
            Factory::getApplication()->enqueueMessage('<br>' . join('<br>', $errors), 'warning');
        }

        /*
        for ($i = 0; $i < count($post["title"]); $i++) {
            if (@$post["addMenu"] === 1 || @$post['addMenu'] === 'on') {
                $type = "content_item_link";
                $this->menuLink($row->id, $row->title, @$post["menuselect"], $type, @$post["menuselect3"], $row->alias);
            }
        }
        */

        return true;
    }
}
