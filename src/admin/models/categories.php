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

        $menuType = $data['menutype'];
        $linkType = $data['link_type'] ?? null;
        if ($linkType) {
            $linkType = $this->menuLayouts[$linkType] ?? null;
        }

        $errors = [];
        foreach ($data['title'] as $index => $title) {

            if (empty($title)) {
                continue;
            }

            try {
                $alias = $data['alias'][$index] ?: $title;

                $newCategory = array_merge([
                    'title' => $title,
                    'alias' => OutputFilter::stringURLSafe($alias)
                ],
                    $commonData
                );

                $model->setState('category.id');
                if ($model->save($newCategory) == false) {
                    throw new Exception(
                        sprintf(
                            '%02d: %s (%s): %s',
                            $index + 1,
                            $title,
                            $alias,
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
                        $newCategory['title'],
                        $newCategory['alias'] ?? '',
                        $newCategory['published'] ?? 0,
                        $index
                    );
                }
            } catch (Throwable $error) {
                $errors[] = $error->getMessage();
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
            'title'     => 'ARRAY',
            'alias'     => 'ARRAY',
            'parent'    => 'INT',
            'access'    => 'INT',
            'published' => 'INT',
            'menutype'  => 'string',
            'parent_id' => 'INT',
            'link_type' => 'STRING'
        ]);
    }
}
