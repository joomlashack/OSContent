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
 * @var OSContentViewContent $this
 * @var stdClass             $template
 * @var string               $layout
 * @var string               $layoutTemplate
 * @var Language             $lang
 * @var string               $filetofind
 */

extract($this->options);
/**
 * @var int  $contentRows
 * @var bool $displayIntro
 * @var bool $displayFullText
 */

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$this->document->getWebAssetManager()->addInlineStyle('td .form-control { width: 100%; }');

$columnWidth = ($displayIntro xor $displayFullText)
    ? 'col-lg-6'
    : ($displayFullText && $displayIntro ? 'col-lg-3' : 'col-lg-12');

?>
<div class="row-fluid">
    <div id="j-main-container">
        <form name="adminForm"
              id="adminForm"
              action="<?php echo Route::_('index.php?option=com_oscontent'); ?>"
              method="post"
              class="form-validate">

            <div class="row">
                <div class="col-lg-9">
                    <fieldset class="form-vertical options-form">
                        <legend><?php echo Text::sprintf('COM_OSCONTENT_CREATEUPTO', $contentRows); ?></legend>
                        <table class="articles table table-striped">
                            <?php
                            for ($row = 0; $row < $contentRows; $row++) :
                                $fields = $this->getFields($row);
                                ?>
                                <tr>
                                    <td class="col-lg-1">
                                        <strong><?php echo number_format($row + 1); ?></strong>
                                    </td>

                                    <td class="<?php echo $columnWidth; ?>">
                                        <?php
                                        echo $fields['title']->renderField();
                                        if (empty($fields['alias']) == false) :
                                            echo $fields['alias']->renderField();
                                        endif;
                                        ?>
                                    </td>

                                    <?php
                                    $intro         = $fields['introtext'] ?? null;
                                    $imageIntro    = $fields['image_intro'] ?? null;
                                    $imageIntroAlt = $fields['image_intro_alt'] ?? null;
                                    if ($intro || $imageIntro) : ?>
                                        <td class="<?php echo $columnWidth; ?>">
                                            <?php
                                            echo $intro ? $intro->renderField() : '';
                                            echo $imageIntro ? $imageIntro->renderField() : '';
                                            echo $imageIntroAlt ? $imageIntroAlt->renderField() : '';
                                            ?>
                                        </td>
                                    <?php endif;
                                    $fullText         = $fields['fulltext'] ?? null;
                                    $imageFullText    = $fields['image_fulltext'] ?? null;
                                    $imageFullTextAlt = $fields['image_fulltext_alt'] ?? null;

                                    if ($fullText || $imageFullText) :?>
                                        <td class="<?php echo $columnWidth; ?>">
                                            <?php
                                            echo $fullText ? $fullText->renderField() : '';
                                            echo $imageFullText ? $imageFullText->renderField() : '';
                                            echo $imageFullTextAlt ? $imageFullTextAlt->renderField() : '';
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </fieldset>
                </div>

                <div class="col-lg-3">
                    <fieldset class="options-form form-vertical">
                        <legend><?php echo Text::_('COM_OSCONTENT_OPTIONS'); ?></legend>
                        <div class="control-group">
                            <div class="control-label">
                                <label id="duplicateText-lbl" class="hasTooltip"
                                       title="<?php echo Text::_('COM_OSCONTENT_COPY_FIRST_ARTICLE_TOOLTIP'); ?>">
                                    <?php echo Text::_('COM_OSCONTENT_COPY_FIRST_ARTICLE'); ?>
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

            <input type="hidden" name="task" value=""/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>
