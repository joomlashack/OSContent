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

defined('_JEXEC') or die();

?>

<style>
    .radiobtn {
        float: left !important;
        clear: none;
        padding-right: 10px;
    }
</style>

<div class="row-fluid">
    <div id="j-main-container">

        <form action="<?php echo JRoute::_('index.php?option=com_oscontent'); ?>" method="post" name="adminForm"
              id="categories-form" class="adminForm form-validate">
            <div class="row ost-oscontent-container">
                <div class="col-lg-8 ost-table-cell-left">
                        <fieldset class="options-form">
                            <legend><?php echo JText::_("COM_OSCONTENT_CREATEUPTO") . " " . $this->params->get(
                                        'nbOSCategories',
                                        10
                                    ) . " " . JText::_("COM_OSCONTENT_CATEGORIES_ROW"); ?></legend>

                            <table class="table table-striped">
                                <?php $k = 0; ?>
                                <?php for ($i = 1; $i < $this->params->get('nbOSCategories', 10) + 1; $i++): ?>
                                    <tr>
                                        <td class="ost-number">
                                            <strong><?php echo $i; ?></strong>
                                        </td>
                                        <td class="ost-medium-sc">
                                            <div class="control-label"><label>
                                                <?php echo JText::_("COM_OSCONTENT_CATEGORY"); ?>
                                                <?php echo JText::_("COM_OSCONTENT_TITLE"); ?>
                                            </label></div>
                                            <input class="inputbox  form-control" type="text" size="25" maxlength="255"
                                                   id="title_<?php echo $i; ?>" name="title[]" value="">
                                        </td>
                                        <td class="ost-medium-sc">
                                            <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_ALIAS"); ?></label></div>
                                            <input class="inputbox form-control" type="text" size="25" maxlength="255"
                                                   id="alias_<?php echo $i; ?>" name="alias[]" value=""
                                                   placeholder="<?php echo JText::_("COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER"); ?>">
                                        </td>
                                    </tr>
                                    <?php $k = 1 - $k; ?>
                                <?php endfor; ?>
                            </table>
                        </fieldset>
                </div>
                <div class="col-lg-4 ost-table-cell-right">
                        <fieldset class="options-form">
                            <legend><?php echo JText::_("COM_OSCONTENT_OPTIONS"); ?></legend>

                            <table border="0" cellpadding="3" cellspacing="0">

                                <tr>
                                    <td>
                                        <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_PARENT"); ?></label></div>
                                        <?php echo $this->lists['cate']; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="control-label"><label><?php echo JText::_(
                                            "COM_OSCONTENT_ACCESS_LEVEL"
                                        ); ?></label></div>
                                        <?php echo $this->lists['access']; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_PUBLISHED"); ?></label></div>
                                        <?php echo $this->lists['published']; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="control-label"><label><input type="checkbox" name="addMenu"
                                               style="margin-top:0px;" value="1"><?php echo JText::_(
                                            "COM_OSCONTENT_ADD_TO_MENU"
                                        ); ?></label></div>
                                        <?php echo $this->lists['menuselect']; ?><?php echo $this->lists['menuselect3']; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="control-label"><label><?php echo JText::_(
                                            "COM_OSCONTENT_SELECT_MENUTYPE"
                                        ); ?></label></div>
                                        <?php echo $this->lists['link_type']; ?>
                                    </td>
                                </tr>
                            </table>
                                    </fieldset>
                    </div>
                </div>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>

        <script type="text/javascript">
            Joomla.submitbutton = function (task, type) {
                var form = document.adminForm;

                if ((form.addMenu.checked) && (form.menuselect.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENUTYPE"); ?>");
                }
                else if ((form.addMenu.checked) && (form.link_type.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENU"); ?>");
                }
                else {
                    Joomla.submitform(task, document.getElementById('categories-form'));
                }
            };

            var menulist = [];

            <?php
            // Sub menus
            $i = 0;
            $top = 0;

            foreach ($this->lists['menulist'] as $k => $items):
                $top = 0;

                foreach ($items as $v):
                    if ($top == 0):
                        echo "menulist[" . $i++ . "] = new Array('" . addslashes($v->menutype) . "', '-1', 'Top');\t";
                        $top = 1;
                    endif;

                    echo "menulist[" . $i++ . "] = new Array('" . addslashes($v->menutype) . "','" . addslashes($v->id) . "','" .
                        str_replace('&nbsp;', ' ', addslashes(str_replace('&#160;', '-', $v->treename))) . "');\t";
                endforeach;
            endforeach;
            ?>

        </script>
    </div>
    <!-- #j-main-container -->
</div>

<?php echo $this->extension->getFooterMarkup(); ?>
