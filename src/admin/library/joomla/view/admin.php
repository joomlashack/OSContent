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
use Alledia\Framework\Joomla\View\Admin\AbstractForm;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

abstract class OscontentViewAdmin extends AbstractForm
{
    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var string
     */
    protected $sidebar = null;

    protected function setup()
    {
        $this->params = ComponentHelper::getParams('com_oscontent');

        parent::setup();
    }

    /**
     * @param ?string $title
     * @param ?string $icon
     *
     * @return void
     */
    protected function addToolbar(?string $title = null, ?string $icon = null)
    {
        if ($title) {
            ToolbarHelper::title($title, $icon);
        }

        $user = Factory::getUser();
        if ($user->authorise('core.admin', 'com_oscontent')) {
            ToolbarHelper::preferences('com_oscontent');
        }
    }
}
