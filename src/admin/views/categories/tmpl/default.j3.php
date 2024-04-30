<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2024 Joomlashack.com. All rights reserved
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

/**
 * @var OSContentViewCategories $this
 * @var string                  $template
 * @var string                  $layout
 * @var string                  $layoutTemplate
 * @var Language                $lang
 * @var string                  $filetofind
 */

extract($this->options);
/**
 * @var int $categoryRows
 */

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
?>
<form name="adminForm"
      id="adminForm"
      action="<?php echo Route::_('index.php?option=com_oscontent&view=categories'); ?>"
      method="post"
      class="form-validate">

    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <div class="row-fluid form-vertical">
            <div class="span8">
                <fieldset>
                    <legend><?php echo Text::sprintf('COM_OSCONTENT_CATEGORIES_CREATEUPTO', $categoryRows); ?></legend>

                    <table class="table table-striped">
                        <?php
                        for ($row = 0; $row < $categoryRows; $row++) :
                            $fields = $this->getFields($row);
                            ?>
                            <tr>
                                <td class="span1">
                                    <strong><?php echo number_format($row + 1); ?></strong>
                                </td>

                                <td class="span5">
                                    <?php echo $fields['title']->renderField(); ?>
                                </td>

                                <td class="span5">
                                    <?php echo $fields['alias']->renderField(); ?>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </fieldset>
            </div>
            <div class="row-fluid well span4">
                <fieldset>
                    <legend><?php echo Text::_('COM_OSCONTENT_OPTIONS'); ?></legend>
                    <?php echo $this->form->renderFieldset('options'); ?>
                </fieldset>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

