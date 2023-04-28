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

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

/**
 * @var OSContentViewContent $this
 * @var string               $template
 * @var string               $layout
 * @var string               $layoutTemplate
 * @var Language             $lang
 * @var string               $filetofind
 */

extract($this->options);
/**
 * @var string $contentRows
 * @var string $categoryRows
 * @var bool   $displayAlias
 * @var bool   $displayIntroText
 * @var bool   $displayFullText
 * @var int    $displayWysiwyg
 */

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$columnWidth = ($displayIntroText xor $displayFullText)
    ? 'span6'
    : ($displayFullText && $displayIntroText ? 'span4' : '');

$editor = Editor::getInstance($this->app->get('editor'));

?>
<form name="adminForm"
      id="adminForm"
      action="<?php echo Route::_('index.php?option=com_oscontent'); ?>"
      method="post"
      class="form-validate">

    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <div class="row-fluid form-vertical">
            <div class="span8">
                <fieldset>
                    <legend><?php echo Text::sprintf('COM_OSCONTENT_CREATEUPTO', $contentRows); ?></legend>
                    <table class="table table-striped">
                        <?php
                        for ($row = 0; $row < $contentRows; $row++) :
                            $titleId = 'title_' . $row;
                            $wysiwyg = $displayWysiwyg == 2 || ($row == 0 && $displayWysiwyg == 1);
                            ?>
                            <tr>
                                <td class="span1">
                                    <strong><?php echo number_format($row + 1); ?></strong>
                                </td>

                                <td class="<?php echo $columnWidth; ?>">
                                    <div class="control-group">
                                        <div class="control-label">
                                            <label for="<?php echo $titleId; ?>">
                                                <?php echo Text::_('COM_OSCONTENT_TITLE'); ?>
                                            </label>
                                        </div>
                                        <div class="controls">
                                            <input class="span12"
                                                   type="text"
                                                   size="50"
                                                   maxlength="255"
                                                   id="<?php echo $titleId; ?>"
                                                   name="title[]"
                                                   value="<?php echo $this->formData->get('title.' . $row); ?>">
                                        </div>
                                        <?php
                                        if ($displayAlias) :
                                            $aliasId = 'alias_' . $row;
                                            ?>
                                            <div class="control-group">
                                                <div class="control-label">
                                                    <label for="<?php echo $aliasId; ?>">
                                                        <?php echo Text::_('COM_OSCONTENT_ALIAS'); ?>
                                                    </label>
                                                </div>

                                                <div class="controls">
                                                    <input class="span12"
                                                           type="text"
                                                           maxlength="255"
                                                           id="<?php echo $aliasId; ?>"
                                                           name="alias[]"
                                                           value="<?php echo $this->formData->get('alias.' . $row); ?>"
                                                           placeholder="<?php echo Text::_('COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER'); ?>">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <?php
                                if ($displayIntroText) :
                                    $introTextId = 'introtext_' . $row;
                                    ?>
                                    <td class="<?php echo $columnWidth; ?>">
                                        <div class="control-group">
                                            <div class="control-label">
                                                <label for="<?php echo $introTextId; ?>">
                                                    <?php echo Text::_('COM_OSCONTENT_INTRO_TEXT'); ?>
                                                </label>
                                            </div>
                                            <div class="controls">
                                                <?php
                                                if ($wysiwyg) :
                                                    echo $editor->display(
                                                        'introtext[]',
                                                        $this->formData->get('introtext.' . $row),
                                                        null,
                                                        null,
                                                        0,
                                                        0,
                                                        true,
                                                        $introTextId
                                                    );
                                                else : ?>
                                                    <textarea id="<?php echo $introTextId ?>"
                                                              name="introtext[]"
                                                              rows="4"
                                                              cols="35"
                                                              class="span12"><?php echo $this->formData->get('introtext.' . $row); ?></textarea>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>

                                <?php
                                if ($displayFullText) :
                                    $fullTextId = 'fulltext_' . $row;
                                    ?>
                                    <td class="<?php echo $columnWidth; ?>">
                                        <div class="control-group">
                                            <div class="control-label">
                                                <label for="<?php echo $fullTextId; ?>">
                                                    <?php echo Text::_('COM_OSCONTENT_FULL_TEXT'); ?>
                                                </label>
                                            </div>
                                            <div class="controls">
                                                <?php
                                                if ($wysiwyg) :
                                                    echo $editor->display(
                                                        'fulltext[]',
                                                        $this->formData->get('fulltext.' . $row),
                                                        null,
                                                        null,
                                                        0,
                                                        0,
                                                        true,
                                                        $fullTextId
                                                    );
                                                else : ?>
                                                    <textarea id="<?php echo $fullTextId; ?>"
                                                              name="fulltext[]"
                                                              rows="4"
                                                              cols="35"
                                                              class="span12"><?php echo $this->formData->get('fulltext.' . $row); ?></textarea>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </fieldset>
            </div>
            <div class="row-fluid well span4">
                <fieldset>
                    <legend><?php echo Text::_('COM_OSCONTENT_OPTIONS'); ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <label id="duplicateText-lbl" class="hasTooltip"
                                   title="<?php echo Text::_('COM_OSCONTENT_COPY_FIRST_TITLE_TOOLTIP'); ?>">
                                <?php echo Text::_('COM_OSCONTENT_COPY_FIRST_TITLE'); ?>
                            </label>
                        </div>
                        <div class="controls">
                            <fieldset class="btn-group radio">
                                <a id="duplicateText" type="button" class="btn btn-success">Copy</a>
                                <a id="clearText" type="button" class="btn btn-danger">Clear</a>
                            </fieldset>
                        </div>
                    </div>

                    <?php echo $this->form->renderFieldset('options'); ?>
                </fieldset>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
