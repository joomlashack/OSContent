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

use Joomla\CMS\MVC\Controller\FormController;

defined('_JEXEC') or die();

class OscontentControllerCategories extends FormController
{
    /**
     * @inheritDoc
     */
    public function save($key = null, $urlVar = null)
    {
        $result = parent::save($key, $urlVar);

        $this->setRedirect('index.php?option=com_oscontent&view=categories');

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function cancel($key = null)
    {
        $result = parent::cancel($key);

        $this->setRedirect('index.php?option=com_oscontent&view=categories');

        return $result;
    }
}
