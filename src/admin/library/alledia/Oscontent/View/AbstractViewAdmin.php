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

namespace Alledia\Oscontent\View;

use Alledia\Framework\Factory;
use Alledia\Framework\Joomla\View\Admin\AbstractForm;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class AbstractViewAdmin extends AbstractForm
{
    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var string[]
     */
    protected $options = [];

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
    protected function setOptions(): void
    {
        // For subclasses as needed
    }
}
