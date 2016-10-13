<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>

<style>
    .radiobtn {
        float: left !important;
        clear: none;
        padding-right: 10px;
    }
</style>

<div class="row-fluid">
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
        <?php endif; ?>

        <form action="<?php echo JRoute::_('index.php?option=com_oscontent'); ?>" method="post" name="adminForm"
              id="categories-form" class="adminForm form-validate">
            <div class="row-fluid ost-oscontent-container">
                <div class="span8 ost-table-cell-left">
                        <fieldset>
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
                                            <input class="inputbox span11" type="text" size="25" maxlength="255"
                                                   id="title_<?php echo $i; ?>" name="title[]" value="">
                                        </td>
                                        <td class="ost-medium-sc">
                                            <div class="control-label"><label><?php echo JText::_("COM_OSCONTENT_ALIAS"); ?></label></div>
                                            <input class="inputbox span11" type="text" size="25" maxlength="255"
                                                   id="alias_<?php echo $i; ?>" name="alias[]" value=""
                                                   placeholder="<?php echo JText::_("COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER"); ?>">
                                        </td>
                                    </tr>
                                    <?php $k = 1 - $k; ?>
                                <?php endfor; ?>
                            </table>
                        </fieldset>
                </div>
                <div class="span4 ost-table-cell-right">
                        <div class="well">
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
                                            "COM_OSCONTENT_LINK_TO_MENU"
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
                        </div>
                    </div>
                </div>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>

        <script type="text/javascript">
            <?php // Joomla 3.x Backward Compatibility ?>
            <?php if (version_compare(JVERSION, '3.0', '<')): ?>
            function submitbutton(pressbutton) {
                var form = document.adminForm;

                if ((form.addMenu.checked) && (form.menuselect.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENUTYPE"); ?>");

                    return;
                }
                else if ((form.addMenu.checked) && (form.link_type.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENU"); ?>");

                    return;
                }
                else {
                    submitform(pressbutton);
                }
            }
            <?php else: ?>
            Joomla.submitbutton = function (task, type) {
                var form = document.adminForm;

                if ((form.addMenu.checked) && (form.menuselect.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENUTYPE"); ?>");

                    return;
                }
                else if ((form.addMenu.checked) && (form.link_type.value == '')) {
                    alert("<?php  echo  JText::_("COM_OSCONTENT_SELECT_MENU"); ?>");

                    return;
                }
                else {
                    Joomla.submitform(task, document.id('categories-form'));
                }
            };
            <?php endif; ?>

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
