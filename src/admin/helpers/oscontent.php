<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2022 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSContent.  If not, see <https://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class OscontentHelper extends ContentHelper
{
    /**
     * @inheritDoc
     */
    public static function addSubmenu($vName)
    {
        Sidebar::addEntry(
            Text::_('COM_OSCONTENT_ADMINMENU_CREATE'),
            'index.php?option=com_oscontent&view=content',
            $vName == 'content'
        );

        Sidebar::addEntry(
            Text::_('COM_OSCONTENT_ADMINMENU_CATEGORIES'),
            'index.php?option=com_oscontent&view=categories',
            $vName == 'categories'
        );

        Sidebar::addEntry(
            Text::_('COM_OSCONTENT_ADMINMENU_DELETE'),
            'index.php?option=com_oscontent&view=delete',
            $vName == 'delete'
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function filePathToText(string $path): string
    {
        if ($path) {
            $pathInfo = pathinfo($path);

            return ucwords(strtolower(str_replace(['_', '-'], ' ', $pathInfo['filename'])));
        }

        return '';
    }
}
