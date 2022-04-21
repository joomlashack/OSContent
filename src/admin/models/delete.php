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
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class OSContentModelDelete extends OscontentModelAdmin
{
    /**
     * @inheritdoc
     */
    protected $text_prefix = 'COM_OSCONTENT_DELETE';

    /**
     * @var ContentModelArticle
     */
    protected $contentModel = null;

    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function populateState()
    {
        $this->app = Factory::getApplication();

        $input = $this->app->input;

        $this->setState('category.id', $input->get('categoryId'));
        $this->setState('delete', $input->get('delete'));
    }

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_oscontent.delete',
            'delete',
            ['load_data' => $loadData]
        );
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
     * @return bool
     */
    public function customDelete()
    {
        try {
            switch ($this->getState('delete')) {
                case 'content':
                    return $this->clearContent();

                case 'all':
                    return $this->clearAll();
            }

            $errorMessage = '<pre>' . print_r($this->getState()->getProperties(), 1) . '</pre>';

        } catch (Throwable $error) {
            $errorMessage = (sprintf('%s:%s<br>%s', $error->getFile(), $error->getLine(), $error->getMessage()));
        }

        $this->setError($errorMessage ?? 'Unknown problem');
        return false;
    }

    /**
     * Clear only introtext and fulltext in articles of selected category
     *
     * @return bool
     */
    protected function clearContent()
    {
        $categoryId = (int)$this->getState('category.id');
        if ($categoryId) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->update('#__content')
                ->set([
                    $db->quoteName('introtext') . ' = ' . $db->quote(''),
                    $db->quoteName('fulltext') . ' = ' . $db->quote('')
                ])
                ->where('catid = ' . $categoryId);

            $success = (bool)$db->setQuery($query)->execute();
            if ($success) {
                $this->app->enqueueMessage(sprintf('Cleared contents of %s articles', $db->getAffectedRows()));
            }

            return $success;
        }

        $this->setError('Nothing to delete');
        return false;
    }

    /**
     * Remove all content related to the category
     *
     * @return false
     */
    protected function clearAll()
    {
        $categoryId = (int)$this->getState('category.id');
        if ($categoryId) {
            if (
                $this->deleteArticles($categoryId)
                && $this->deleteMenus($categoryId)
            ) {
                /** @var CategoriesModelCategory $categoryModel */
                $categoryModel = Helper::getCategoryModel('Category', 'administrator');

                $pks = [$categoryId];
                $categoryModel->publish($pks, -2);
                if ($categoryModel->delete($pks)) {
                    $this->app->enqueueMessage(Text::_('COM_OSCONTENT_DELETE_CATEGORY'));

                    return true;
                }

                $this->setError($categoryModel->getError());
            }
        }

        return true;
    }

    protected function deleteArticles(int $categoryId): bool
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__content')
            ->where('catid = ' . $categoryId);

        if ($articles = $db->setQuery($query)->loadColumn()) {
            $contentModel = $this->getModel();

            // First we need to trash the articles
            $contentModel->publish($articles, -2);

            // Then we can delete
            $success = $contentModel->delete($articles);
            if ($success) {
                $this->app->enqueueMessage(Text::sprintf('COM_OSCONTENT_DELETE_N_ARTICLES', count($articles)));

            } else {
                $this->setError(Text::_('COM_OSCONTENT_ERROR_DELETE_ARTICLES'));
                return false;
            }
        }

        return true;
    }

    protected function deleteMenus(int $categoryId): bool
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where([
                'component_id = ' . ComponentHelper::getComponent('com_content')->id,
                sprintf('LOCATE(%s, link) > 0', $db->quote('category')),
                sprintf('LOCATE(%s, link) > 0', $db->quote('com_content')),
                sprintf('LOCATE(%s, link) > 0', $db->quote('id=' . $categoryId))
            ]);

        if ($menuIds = $db->setQuery($query)->loadColumn()) {
            /** @var MenusModelItem $menuModel */
            $menuModel = Helper::getJoomlaModel('Item', 'MenusModel', 'com_menus', 'administrator');

            $menuModel->publish($menuIds, -2);

            $success = $menuModel->delete($menuIds);
            if ($success) {
                $this->app->enqueueMessage(Text::sprintf('COM_OSCONTENT_DELETE_N_MENUS', count($menuIds)));
            } else {
                $this->setError('Menu Problem');
            }

            return $success;
        }

        return true;
    }
}
