<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2023 Joomlashack.com. All rights reserved
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
use Alledia\Oscontent\Model\AbstractModelAdmin;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Component\Categories\Administrator\Table\CategoryTable;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class OSContentModelCategories extends AbstractModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_CATEGORIES';

    /**
     * @var CategoriesModelCategory|CategoryTable
     */
    protected $categoryModel = null;

    /**
     * @var string[][]
     */
    protected $menuLayouts = [
        'category.list' => ['view' => 'category'],
        'category.blog' => ['view' => 'category', 'layout' => 'blog']
    ];

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
        $app = Factory::getApplication();

        $data = $app->getUserState('com_oscontent.edit.categories.data', []);

        $data['access'] = $data['access'] ?? $app->get('access');

        return $data;
    }

    /**
     * @return CategoriesModelCategory|CategoryTable
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
     * @throws Exception
     */
    public function save($data)
    {
        $model = Helper::getCategoryModel('Category', 'Administrator');

        $commonData = [
            'title'     => '',
            'alias'     => '',
            'parent_id' => (int)$data['parent'],
            'access'    => (int)$data['access'],
            'published' => (int)$data['published'],
            'extension' => 'com_content',
            'note'      => Text::_('COM_OSCONTENT_NOTE_CREATEDBY'),
            'language'  => '*'
        ];

        $menuType = $data['menutype'];
        $linkType = $data['link_type'] ?? null;
        if ($linkType) {
            $linkType = $this->menuLayouts[$linkType] ?? null;
        }

        $errors = [];
        foreach ($data['category'] as $index => $category) {
            $category = array_merge($commonData, $category);

            if ($category['title']) {
                try {
                    $category['alias'] = OutputFilter::stringURLSafe($category['alias'] ?: $category['title']);

                    $model->setState('category.id');
                    if ($model->save($category) == false) {
                        throw new Exception(
                            sprintf(
                                '%02d: %s (%s): %s',
                                $index + 1,
                                $category['title'],
                                $category['alias'],
                                $model->getError()
                            )
                        );

                    } elseif ($menuType && $linkType) {
                        $linkVars       = array_merge(
                            [
                                'option' => 'com_content',
                            ],
                            $linkType
                        );
                        $linkVars['id'] = $model->getState('category.id');

                        $this->menuLink(
                            $menuType,
                            $data['parent_id'] ?? null,
                            $linkVars,
                            $category['title'],
                            $category['alias'] ?? '',
                            $category['published'] ?? 0,
                            $index
                        );
                    }

                } catch (Throwable $error) {
                    $errors[] = $error->getMessage();
                }
            }
        }

        if ($errors) {
            Factory::getApplication()->enqueueMessage(join('<br>', $errors), 'error');
            $this->setError(Text::_('COM_OSCONTENT_CATEGORIES_ERROR_SAVE_FAILED'));

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
            'category'  => 'array',
            'parent'    => 'int',
            'access'    => 'int',
            'published' => 'int',
            'menutype'  => 'string',
            'parent_id' => 'int',
            'link_type' => 'string'
        ]);
    }
}
