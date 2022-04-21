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

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die();

class OSContentViewDelete extends OscontentViewAdmin
{
    /**
     * @inheritDoc
     */
    public function display($tpl = null)
    {
        $this->model = $this->getModel();
        $this->form  = $this->model->getForm();

        $this->addToolbar();

        OscontentHelper::addSubmenu('delete');
        $this->sidebar = Sidebar::render();

        Text::script('COM_OSCONTENT_DELETE_ALL');
        Text::script('COM_OSCONTENT_DELETE_CONTENT');
        Text::script('COM_OSCONTENT_DELETE_FAIL');
        HTMLHelper::_('script', 'com_oscontent/delete.min.js', ['relative' => true]);

        parent::display($tpl);
    }

    /**
     * @inheritDoc
     */
    protected function addToolbar(?string $title = null, ?string $icon = 'trash')
    {
        ToolbarHelper::custom('delete.delete', 'delete', 'delete', Text::_('COM_OSCONTENT_DELETE'), false);

        $title = $title ?: Text::_('COM_OSCONTENT_PAGE_DELETE');
        parent::addToolbar($title, $icon);
    }
}
