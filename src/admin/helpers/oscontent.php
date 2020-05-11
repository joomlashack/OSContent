<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2020 Joomlashack.com. All rights reserved
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

defined('_JEXEC') or die();

/**
 * Content Helper
 *
 * @since  1.0.0
 */
class OSContentHelper
{
    /**
     * Link the content to the menu
     *
     * @param   string $vName View name
     *
     * @return void
     */
    public static function addSubmenu($vName)
    {
        $subMenuClass = 'JHtmlSidebar';

        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_CREATE'),
            'index.php?option=com_oscontent&view=content',
            $vName == 'content' || empty($vName)
        );

        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_CATEGORIES'),
            'index.php?option=com_oscontent&view=categories',
            $vName == 'categories'
        );
        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_DELETE'),
            'index.php?option=com_oscontent&view=delete',
            $vName == 'delete'
        );

        // Load CSS
        JHtml::stylesheet( Juri::base() . 'components/com_oscontent/media/css/style.css' );

        JHtml::stylesheet( Juri::base() . 'components/com_oscontent/media/css/responsive.css' );
    }
}
