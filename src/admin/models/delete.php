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

use Alledia\Framework\Factory;
use Alledia\Framework\Helper;
use Alledia\Oscontent\Model\AbstractModelAdmin;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Content\Administrator\Model\ArticleModel;
use Joomla\Component\Menus\Administrator\Model\ItemModel;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class OSContentModelDelete extends AbstractModelAdmin
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
     * @throws Exception
     */
    public function customDelete(): bool
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

        Factory::getApplication()->enqueueMessage($errorMessage ?? 'Unknown problem', 'error');

        return false;
    }

    /**
     * Clear only introtext and fulltext in articles of selected category
     *
     * @return bool
     * @throws Exception
     */
    protected function clearContent(): bool
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

        Factory::getApplication()->enqueueMessage('Nothing to delete', 'warning');

        return false;
    }

    /**
     * Remove all content related to the category
     *
     * @return false
     * @throws Exception
     */
    protected function clearAll()
    {
        $categoryId = (int)$this->getState('category.id');
        if ($categoryId) {
            if (
                ($articles = $this->deleteArticles($categoryId))
                && $this->deleteMenus($categoryId, $articles)
            ) {
                /** @var CategoriesModelCategory $categoryModel */
                $categoryModel = Helper::getCategoryModel('Category', 'administrator');

                $pks = [$categoryId];
                $categoryModel->publish($pks, -2);
                if ($categoryModel->delete($pks)) {
                    $this->app->enqueueMessage(Text::_('COM_OSCONTENT_DELETE_CATEGORY'));

                    return true;
                }

                Factory::getApplication()->enqueueMessage($categoryModel->getError(), 'error');
            }
        }

        return true;
    }

    /**
     * @param int $categoryId
     *
     * @return int[]
     * @throws Exception
     */
    protected function deleteArticles(int $categoryId): array
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
                $this->app->enqueueMessage(Text::plural('COM_OSCONTENT_DELETE_N_ARTICLES', count($articles)));

            } else {
                Factory::getApplication()->enqueueMessage(Text::_('COM_OSCONTENT_DELETE_ERROR_ARTICLES'), 'error');

                return [];
            }
        }

        return $articles;
    }

    /**
     * @param int   $categoryId
     * @param int[] $articleIds
     *
     * @return bool
     * @throws Exception
     */
    protected function deleteMenus(int $categoryId, array $articleIds): bool
    {
        $db = $this->getDbo();

        $quotedLink = $db->quoteName('link');
        $ors        = [];
        if ($articleIds) {
            $ors[] = sprintf(
                '(%s)',
                join(
                    ' AND ',
                    [
                        sprintf('LOCATE(%s, %s) > 0', $db->quote('view=article'), $quotedLink),
                        sprintf('%s RLIKE %s', $quotedLink, $db->quote(sprintf('id=(%s)', join('|', $articleIds))))
                    ]
                )
            );
        }
        $ors[] = sprintf(
            '(%s)',
            join(
                ' AND ',
                [
                    sprintf('LOCATE(%s, %s) > 0', $db->quote('view=category'), $quotedLink),
                    sprintf('LOCATE(%s, %s) > 0', $db->quote('id=' . $categoryId), $quotedLink)
                ]
            )
        );

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where([
                $db->quoteName('type') . ' = ' . $db->quote('component'),
                $db->quoteName('client_id') . ' = 0',
                $db->quoteName('component_id') . ' = ' . ComponentHelper::getComponent('com_content')->id,
                sprintf('(%s)', join(' OR ', $ors))
            ]);

        if ($menuIds = $db->setQuery($query)->loadColumn()) {
            /** @var MenusModelItem|ItemModel $menuModel */
            $menuModel = $this->getMenuModel();

            $menuModel->publish($menuIds, -2);

            $success = $menuModel->delete($menuIds);
            if ($success) {
                $this->app->enqueueMessage(Text::plural('COM_OSCONTENT_DELETE_N_MENUS', count($menuIds)));
            } else {
                $this->setError('Menu Problem');
            }

            return $success;
        }

        return true;
    }
}
