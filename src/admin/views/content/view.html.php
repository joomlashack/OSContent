<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2023 Joomlashack.com. All rights reserved
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

use Alledia\Oscontent\View\AbstractViewAdmin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class OSContentViewContent extends AbstractViewAdmin
{
    /**
     * @var Registry
     */
    protected $formData = null;

    /**
     * @inheritDoc
     */
    public function display($tpl = null)
    {
        $this->model    = $this->getModel();
        $this->form     = $this->model->getForm();
        $this->formData = new Registry($this->app->getUserState('com_oscontent.edit.content.data'));

        $this->addToolbar();

        OscontentHelper::addSubmenu('content');
        $this->sidebar = Sidebar::render();

        HTMLHelper::_('script', 'com_oscontent/admin.min.js', ['relative' => true]);

        parent::display($tpl);
    }

    /**
     * @inheritDoc
     */
    protected function addToolbar(?string $title = null, ?string $icon = 'file-2')
    {
        ToolbarHelper::apply('content.save');

        $title = $title ?: Text::_('COM_OSCONTENT_PAGE_CREATE_CONTENT');

        parent::addToolbar($title, $icon);
    }

    /**
     * @param int $index
     *
     * @return FormField[]
     */
    protected function getFields(int $index): array
    {
        $form = Form::getInstance('com_oscontent.article' . $index, $this->form->getXml()->asXML());

        $displayWysiwyg = $this->options['displayWysiwyg'];
        $wysiwyg        = $displayWysiwyg == 2 || ($index == 0 && $displayWysiwyg == 1);

        // Verify alias
        if ($this->options['displayAlias'] == false) {
            $form->removeField('alias', 'article');
        }

        // Verify infotext
        if ($this->options['displayIntroText']) {
            if ($wysiwyg) {
                $introtext            = $form->getXml()->xpath('//fieldset[@name="article"]//field[@name="introtext"]');
                $introtext[0]['type'] = 'editor';
            }

        } else {
            $form->removeField('introtext', 'article');
        }

        // Verify fulltext
        if ($this->options['displayFullText']) {
            if ($wysiwyg) {
                $fulltext            = $form->getXml()->xpath('//fieldset[@name="article"]//field[@name="fulltext"]');
                $fulltext[0]['type'] = 'editor';
            }

        } else {
            $form->removeField('fulltext', 'article');
        }

        $group = $form->getXml()->xpath('/form/fieldset[@name="article"]/fields');
        $group = array_shift($group);

        $group['name'] = "article[{$index}]";

        $fieldset = $form->getFieldset('article');

        $fields = [];
        foreach ($fieldset as $field) {
            $name          = $field->getAttribute('name');
            $fields[$name] = $field;
        }

        return $fields;
    }
}
