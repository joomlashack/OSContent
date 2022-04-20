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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

abstract class OscontentModelAdmin extends AdminModel
{
    /**
     * @return MenusModelItem
     */
    protected function getMenuModel()
    {
        if (Version::MAJOR_VERSION < 4) {
            $path = JPATH_ADMINISTRATOR . '/components/com_menus';
            BaseDatabaseModel::addIncludePath($path . '/models');
            Table::addIncludePath($path . '/tables');

            /** @var MenusModelItem $model */
            $model = BaseDatabaseModel::getInstance('Item', 'MenusModel');

        } else {
            /*
            $model = Factory::getApplication()->bootComponent('com_content')
                ->getMVCFactory()->createModel($name, $appName, $options);
            */
        }

        return $model;
    }

    /**
     * @param string   $menutype
     * @param int|null $menuId
     * @param array    $linkVars
     * @param string   $title
     * @param string   $alias
     * @param int      $published
     * @param int      $index
     *
     * @return void
     * @throws Exception
     */
    protected function menuLink(
        string $menutype,
        int $menuId,
        array $linkVars,
        string $title,
        string $alias,
        int $published,
        int $index
    ) {
        if ($menutype) {
            try {
                $title = stripslashes(OutputFilter::ampReplace($title));
                $alias = $alias ?: $title;

                $option    = $linkVars['option'] ?? null;
                $component = $option ? ComponentHelper::getComponent($option) : null;

                if ($component && $title && $alias) {
                    $menus      = AbstractMenu::getInstance('site');
                    $parentMenu = $menuId ? $menus->getItem($menuId) : null;

                    $model = $this->getMenuModel();
                    $model->setState('menu.id');

                    $data = [
                        'menutype'     => $menutype,
                        'title'        => $title,
                        'alias'        => OutputFilter::stringURLSafe($alias),
                        'link'         => 'index.php?' . http_build_query($linkVars),
                        'type'         => 'component',
                        'component_id' => $component->id,
                        'parent_id'    => $parentMenu->id ?? null,
                        'component'    => $option,
                        'published'    => $published,
                        'language'     => '*'

                    ];
                    if ($model->save($data) == false) {
                        throw new Exception($model->getError());
                    }
                }
            } catch (Throwable $error) {
                Factory::getApplication()->enqueueMessage(
                    sprintf(
                        '%s. %s (%s): %s',
                        $index,
                        $title,
                        $alias,
                        $error->getMessage()
                    ),
                    'warning'
                );
            }
        }
    }
}
