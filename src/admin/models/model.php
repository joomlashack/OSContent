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

jimport('joomla.application.component.model');

abstract class OSModelAbstract extends JModelLegacy
{
    /**
     * Get the extension id
     *
     * @param string $extension
     * @return int
     */
    protected function getExtensionId($extension, $type = 'component')
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = ' . $db->q($extension))
            ->where('type = ' . $db->q($type));
        $db->setQuery($query, 0, 1);

        return (int) $db->loadResult();
    }
}
