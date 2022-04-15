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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

class OSContentViewContent extends OscontentViewAdmin
{
    /**
     * @var string[]
     */
    protected $options = null;

    /**
     * @inheritDoc
     */
    public function display($tpl = null)
    {
        $this->model = $this->getModel();
        $this->form = $this->model->getForm();

        $this->setOptions();

        $this->addToolbar();

        OscontentHelper::addSubmenu('content');
        $this->sidebar = Sidebar::render();

        parent::display($tpl);
    }

    /**
     * @inheritDoc
     */
    protected function addToolbar(?string $title = null, ?string $icon = 'file-2')
    {
        ToolbarHelper::apply('content.save');

        $title = $title ?: Text::_('COM_OSCONTENT_CREATE_CONTENT');

        parent::addToolbar($title, $icon);
    }

    /**
     * @return void
     */
    protected function setOptions()
    {
        if ($this->options === null) {
            $this->options = [
                'contentRows'      => $this->params->get('nbOSContent'),
                'displayAlias'     => (bool)$this->params->get('displayAlias'),
                'displayIntroText' => (bool)$this->params->get('displayIntroText'),
                'displayFullText'  => (bool)$this->params->get('displayFullText'),
                'displayWysiwyg'   => (int)$this->params->get('displayWysiwyg')
            ];
            foreach ($this->options as $key => $value) {
                $this->document->addScriptOptions('oscontent.' . $key, $value);
            }
        }
    }
}
