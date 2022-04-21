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
use Joomla\Component\Content\Site\Model\ArticleModel;

defined('_JEXEC') or die();

class OSContentModelContent extends OscontentModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_CONTENT';

    /**
     * @var ContentModelArticle
     */
    protected $contentModel = null;

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
     * @return ContentModelArticle|ArticleModel
     */
    protected function getModel()
    {
        if ($this->contentModel === null) {
            $this->contentModel = Helper::getContentModel('Article', 'Administrator');
        }

        return $this->contentModel;
    }

    /**
     * @inheritDoc
     */
    public function getTable($name = '', $prefix = 'Table', $options = [])
    {
        return $this->getModel()->getTable();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPostData(): array
    {
        $input = Factory::getApplication()->input->post;

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
            'menutype'         => 'string',
            'parent_id'        => 'int',
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
        $model = $this->getModel();
        $table = $model->getTable();

        $commonData = array_filter([
            'featured'         => (int)$data['featured'],
            'created_by'       => $data['created_by'] ?: null,
            'created_by_alias' => $data['created_by_alias'] ?: null,
            'state'            => (int)$data['state'],
            'catid'            => (int)$data['catid'],
            'access'           => (int)$data['access'],
            'language'         => '*',
            'note'             => Text::_('COM_OSCONTENT_NOTE_CREATEDBY'),
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

            $currentArticle = array_merge(
                $commonData,
                [
                    'title'     => $title,
                    'alias'     => OutputFilter::stringURLSafe($alias),
                    'introtext' => $data['introtext'][$index] ?? '',
                    'fulltext'  => $data['fulltext'][$index] ?? '',
                ]);

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
            try {
                $model->setState('article.id', null);

                if ($model->save($newArticle) == false) {
                    throw new Exception(
                        sprintf(
                            '%02d. %s (%s): %s',
                            $index + 1,
                            $newArticle['title'],
                            $newArticle['alias'],
                            $model->getError()
                        )
                    );

                } elseif (empty($data['menutype']) == false) {
                    $newArticle['id'] = $model->getState('article.id');
                    $this->menuLink(
                        $data['menutype'],
                        $data['parent_id'] ?? 0,
                        [
                            'option' => 'com_content',
                            'view'   => 'article',
                            'id'     => $newArticle['id']
                        ],
                        $newArticle['title'],
                        $newArticle['alias'] ?? '',
                        $newArticle['state'] ?? 0,
                        $index
                    );
                }

            } catch (Throwable $error) {
                $errors[] = $error->getMessage();
            }
        }

        if ($errors) {
            Factory::getApplication()->enqueueMessage('<br>' . join('<br>', $errors), 'warning');
        }

        return true;
    }
}
