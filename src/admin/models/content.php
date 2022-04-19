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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Content as ContentTable;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Version;

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
        return $this->loadForm(
            'com_oscontent.content',
            'content',
            ['load_data' => $loadData]
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function loadFormData()
    {
        return Factory::getApplication()->getUserState('com_oscontent.edit.content.data', []);
    }

    /**
     * @param int   $menuId
     * @param array $article
     * @param int   $index
     *
     * @return void
     * @throws Exception
     */
    protected function menuLink(int $menuId, array $article, int $index)
    {
        if ($menuId) {
            $articleId = $article['id'] ?? null;
            $title     = stripslashes(OutputFilter::ampReplace($article['title'] ?? null));
            $alias     = $article['alias'] ?? $title;

            if ($articleId && $title && $alias) {
                $menus      = AbstractMenu::getInstance('site');
                $parentMenu = $menus->getItem($menuId);

                $model = $this->getMenuModel();
                $model->setState('menu.id');

                $data = [
                    'menutype'     => $parentMenu->menutype,
                    'title'        => $title,
                    'alias'        => OutputFilter::stringURLSafe($alias),
                    'link'         => 'index.php?option=com_content&view=article&id=' . $articleId,
                    'type'         => 'component',
                    'component_id' => ComponentHelper::getComponent('com_content')->id,
                    'parent_id'    => $parentMenu->id,
                    'component'    => 'com_content',
                    'published'    => 1,
                    'language'     => '*'

                ];
                if ($model->save($data) == false) {
                    Factory::getApplication()->enqueueMessage(
                        sprintf(
                            '%s. %s (%s): %s',
                            $index,
                            $title,
                            $alias,
                            $model->getError()
                        ));
                }
            }
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPostData(): array
    {
        $input = Factory::getApplication()->input;

        return $input->getArray([
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
     * @throws Exception
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
                    '%02d. %s (%s): %s',
                    $index + 1,
                    $newArticle['title'],
                    $newArticle['alias'],
                    $model->getError()
                );

            } elseif (empty($data['menuselect']) == false) {
                $newArticle['id'] = $model->getState('article.id');
                $this->menuLink($data['menuselect'], $newArticle, $index);
            }
        }

        if ($errors) {
            Factory::getApplication()->enqueueMessage('<br>' . join('<br>', $errors), 'warning');
        }

        return true;
    }
}
