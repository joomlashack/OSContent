<?php

/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2026 Joomlashack.com. All rights reserved
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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Component\Menus\Administrator\Model\ItemModel;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class AbstractModelAdmin extends AdminModel
{
    /**
     * @return \MenusModelItem|ItemModel
     * @throws \Exception
     */
    protected function getMenuModel()
    {
        return Helper::getJoomlaModel(
            'Item',
            'MenusModel',
            'com_menus',
            'Administrator'
        );
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
     * @throws \Exception
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
                        throw new \Exception($model->getError());
                    }
                }
            } catch (\Throwable $error) {
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
