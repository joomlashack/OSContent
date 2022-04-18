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

defined('_JEXEC') or die();

use Alledia\Framework\AutoLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

try {
    $frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';
    if (is_file($frameworkPath) == false || (include $frameworkPath) == false) {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            $app->enqueueMessage('[OSContent] Joomlashack framework not found', 'error');
        }

        return false;
    }

    if (defined('ALLEDIA_FRAMEWORK_LOADED') && !defined('OSCONTENT_LOADED')) {
        define('OSCONTENT_ADMIN', JPATH_ADMINISTRATOR . '/components/com_oscontent');
        define('OSCONTENT_LIBRARY', OSCONTENT_ADMIN . '/library');

        AutoLoader::registerCamelBase('Oscontent', OSCONTENT_LIBRARY . '/joomla');

        if (Version::MAJOR_VERSION < 4) {
            HTMLHelper::_(
                'stylesheet',
                'lib_allediaframework/fontawesome/css/all.min.css',
                ['relative' => true]
            );
        }

        Factory::getLanguage()->load('com_oscontent.sys', OSCONTENT_ADMIN);

        HTMLHelper::_('stylesheet', 'com_oscontent/admin.css', ['relative' => true]);
        HTMLHelper::_('script', 'com_oscontent/admin.min.js', ['relative' => true]);
        JLoader::register('OscontentHelper', OSCONTENT_ADMIN . '/helpers/oscontent.php');

        define('OSCONTENT_LOADED', 1);
    }

} catch (Throwable $error) {
    Factory::getApplication()
        ->enqueueMessage('[OSContent] Unable to initialize: ' . $error->getMessage(), 'error');

    return false;
}

return defined('ALLEDIA_FRAMEWORK_LOADED') && defined('OSCONTENT_LOADED');

