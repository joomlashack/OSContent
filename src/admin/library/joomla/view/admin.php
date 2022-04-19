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
use Joomla\CMS\Form\Form;
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
     * @var string[]
     */
    protected $options = null;

    /**
     * @var string
     */
    protected $sidebar = null;

    /**
     * @inheritDoc
     */
    protected function setup()
    {
        parent::setup();

        $this->params = ComponentHelper::getParams('com_oscontent');

        // Check and set defaults
        $configPath = OSCONTENT_ADMIN . '/config.xml';
        if (is_file($configPath)) {
            $config = Form::getInstance('config', $configPath, [], null, '/config');

            $fieldSets = $config->getFieldsets();
            foreach ($fieldSets as $fieldSet) {
                if ($fieldSet->name !== 'permissions') {
                    $fields = $config->getFieldset($fieldSet->name);
                    foreach ($fields as $field) {
                        if ($field->value) {
                            $key = join('.', array_filter([$field->group, $field->fieldname]));
                            $this->params->def($key, $field->value);
                        }
                    }
                }
            }
        }

        $this->setOptions();
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

    /**
     * @return void
     */
    protected function setOptions()
    {
        if ($this->options === null) {
            $this->options = [
                'contentRows'      => $this->params->get('nbOSContent'),
                'categoryRows'     => $this->params->get('nbOSCategories'),
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
