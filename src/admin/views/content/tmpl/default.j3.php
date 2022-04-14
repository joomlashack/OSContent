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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

$contentRows = $this->params->get('nbOSContent', 10);

?>
<form action="<?php echo Route::_('index.php?option=com_oscontent'); ?>"
      method="post"
      name="adminForm"
      id="content-form"
      class="adminForm form-validate">

    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend><?php echo Text::sprintf('COM_OSCONTENT_CREATEUPTO', $contentRows); ?></legend>
                    <table class="table table-striped">
                        <?php
                        for ($row = 0; $row < $contentRows + 1; $row++) :
                            ?>
                            <tr>
                                <td class="ost-number">
                                    <strong><?php echo number_format($row + 1); ?></strong>
                                </td>
                                <td class="ost-medium-sc">
                                    <div class="control-label">
                                        <label><?php echo Text::_('COM_OSCONTENT_TITLE'); ?></label>
                                    </div>
                                    <input class="inputbox span11"
                                           type="text"
                                           maxlength="255"
                                           id="<?php echo 'title_' . $row; ?>"
                                           name="title[]"
                                           value="<?php echo(@$post["title"][$i - 1]); ?>">
                                    <?php if ($this->params->get('displayAlias', 1) == 1): ?>
                                        <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_ALIAS"); ?></label></div>
                                        <input class="inputbox span11" type="text" size="50" maxlength="255"
                                               id="alias_<?php echo $i; ?>" name="alias[]"
                                               value="<?php echo(@$post["alias"][$i - 1]); ?>"
                                               placeholder="<?php echo JText::_("COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER"); ?>">
                                    <?php else: ?>
                                        <?php $hidden .= '<input type="hidden" id="alias_<?php echo $i; ?>" name="alias[]" value =""  >'; ?>
                                    <?php endif; ?>
                                </td>
                                <?php if ($this->params->get('displayIntroText', 1) == 1): ?>
                                    <td class="ost-medium-sc">
                                        <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_INTRO_TEXT"); ?></label></div>
                                        <?php if (($i == 1 && $this->params->get(
                                                    'displayWysiwyg'
                                                ) == "1") || $this->params->get('displayWysiwyg') == "2"
                                        ): ?>
                                            <?php echo "<td colspan=\"4\">" . $editor->display(
                                                    'introtext_' . $i,
                                                    @$post["introtext_" . $i],
                                                    '50%',
                                                    '50',
                                                    '20',
                                                    '50'
                                                ) . "</td>"; ?>
                                        <?php else: ?>
                                            <textarea id="<?php echo 'introtext_' . $i; ?>"
                                                      name="<?php echo 'introtext_' . $i; ?>" rows="4"
                                                      cols="35"
                                                      class="span11"><?php echo(@$post["introtext_" . ($i)]); ?></textarea>
                                        <?php endif; ?>
                                    </td>
                                <?php
                                else:
                                    $hidden .= '<input type="hidden" id="introtext_' . $i . '" name="introtext_' . $i . '" value =""  >'; ?>
                                <?php endif; ?>

                                <?php if ($this->params->get('displayFullText', 1) == 1): ?>
                                    <td class="ost-medium-sc">
                                        <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_FULL_TEXT"); ?></label></div>
                                        <?php if (($i == 1 && $this->params->get(
                                                    'displayWysiwyg'
                                                ) == "1") || $this->params->get('displayWysiwyg') == "2"
                                        ): ?>
                                            <?php echo $editor->display(
                                                'fulltext_' . $i,
                                                @$post["fulltext_" . $i],
                                                '50%',
                                                '50',
                                                '20',
                                                '50'
                                            ); ?>
                                        <?php else: ?>
                                            <textarea id="<?php echo 'fulltext_' . $i; ?>"
                                                      name="<?php echo 'fulltext_' . $i; ?>" rows="4"
                                                      cols="35"
                                                      class="span11"><?php echo(@$post["fulltext_" . $i]); ?></textarea>

                                        <?php endif; ?>
                                    </td>
                                <?php else: ?>
                                    <?php $hidden .= '<input type="hidden" id="fulltext_' . $i . '" name="fulltext_' . $i . '" value =""  >'; ?>
                                <?php endif; ?>
                            </tr>

                            <?php $hidden .= '<input type="hidden" id="metadesc_' . $i . '" name="metadesc[]" value ="">'; ?>
                            <?php $hidden .= '<input type="hidden" id="metakey_' . $i . '" name="metakey[]" value ="">'; ?>

                            <?php $k = 1 - $k; ?>
                        <?php endfor; ?>
                    </table>
                </fieldset>

            </div>
            <div class="4">
                <p>options here</p>
            </div>
        </div>
    </div>
</form>
