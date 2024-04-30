<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2024 Joomlashack.com. All rights reserved
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

namespace Alledia\Oscontent\Model;

use Alledia\Framework\Factory;
use Alledia\Framework\Helper;
use ContentModelArticle;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Component\Content\Administrator\Model\ArticleModel;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class AbstractModelContent extends AbstractModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_CONTENT';

    /**
     * @var ContentModelArticle|ArticleModel
     */
    protected $contentModel = null;

    /**
     * @var string[]
     */
    protected $warnings = [];

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
     * @throws \Exception
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();

        $data = $app->getUserState('com_oscontent.edit.content.data', []);

        $data['access'] = $data['access'] ?? $app->get('access');

        return $data;
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
     * @throws \Exception
     */
    protected function getPostData(): array
    {
        $input = Factory::getApplication()->input->post;

        return $input->getArray([
            'article'          => 'array',
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
     * @throws \Exception
     */
    public function validate($form, $data, $group = null)
    {
        return $this->getPostData();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function save($data)
    {
        $model = $this->getModel();

        $this->warnings = [];
        $newArticles    = $this->getNewArticles($data);
        if ($this->warnings) {
            Factory::getApplication()->enqueueMessage(join('<br>', $this->warnings), 'error');
            $this->setError(Text::_('COM_OSCONTENT_CONTENT_ERROR_SAVE_FAILED'));

            return false;
        }

        foreach ($newArticles as $index => $newArticle) {
            try {
                $model->setState('article.id');

                if ($model->save($newArticle) == false) {
                    throw new \Exception(
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

            } catch (\Throwable $error) {
                $this->warnings[] = $error->getMessage();
            }
        }

        if ($this->warnings) {
            Factory::getApplication()->enqueueMessage('<br>' . join('<br>', $this->warnings), 'error');
            $this->setError(Text::_('COM_OSCONTENT_CONTENT_ERROR_SAVE_FAILED'));

            return false;
        }

        return true;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    protected function getNewArticles(array $data): array
    {
        $app   = Factory::getApplication();
        $model = $this->getModel();
        $table = $model->getTable();

        $commonData = [
            'title'            => '',
            'alias'            => '',
            'introtext'        => '',
            'fulltext'         => '',
            'images'           => '',
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
        ];

        $newArticles = [];
        foreach ($data['article'] as $index => $article) {
            if ($article['title']) {
                $article['alias'] = OutputFilter::stringURLSafe($article['alias'] ?: $article['title']);

                $article = array_merge($commonData, $article);
                if (
                    $table->load([
                        'alias' => $article['alias'],
                        'catid' => $article['catid']
                    ])
                ) {
                    $app->enqueueMessage(
                        sprintf(
                            '%02d. %s: %s',
                            $index + 1,
                            $article['alias'],
                            Text::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS')
                        ),
                        'warning'
                    );
                    continue;
                }

                $newArticles[] = $article;
            }
        }

        return $newArticles;
    }
}
