<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2014 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.3
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
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
            <table border="0" cellpadding="3" cellspacing="0">
                <tr valign="top">
                    <td>
                        <fieldset>
                            <legend><?php echo JText::_("COM_OSCONTENT_CREATEUPTO") . " " . $this->params->get(
                                        'nbOSCategories',
                                        10
                                    ) . " " . JText::_("COM_OSCONTENT_CATEGORIES_ROW"); ?></legend>

                            <table border="0" cellpadding="3" cellspacing="0">
                                <?php $k = 0; ?>
                                <?php for ($i = 1; $i < $this->params->get('nbOSCategories', 10) + 1; $i++): ?>
                                    <tr bgcolor="<?php echo ($k == 0) ? "#f9f9f9" : "#eeeeee"; ?>">
                                        <td><?php echo JText::_("COM_OSCONTENT_CATEGORY") . " " . $i; ?>
                                            : <?php echo JText::_("COM_OSCONTENT_TITLE"); ?></td>
                                        <td><input class="inputbox" type="text" size="25" maxlength="255"
                                                   id="title_<?php echo $i; ?>" name="title[]" value=""></td>
                                        <td><?php echo JText::_("COM_OSCONTENT_ALIAS"); ?></td>
                                        <td><input class="inputbox" type="text" size="25" maxlength="255"
                                                   id="alias_<?php echo $i; ?>" name="alias[]" value="" 
                                                   placeholder="<?php echo JText::_("COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER"); ?>"></td>
                                    </tr>
                                    <?php $k = 1 - $k; ?>
                                <?php endfor; ?>
                            </table>
                        </fieldset>
                    </td>

                    <td valign="top">
                        <fieldset>
                            <legend><?php echo JText::_("COM_OSCONTENT_OPTIONS"); ?></legend>

                            <table border="0" cellpadding="3" cellspacing="0">
                                
                                <tr>
                                    <td><?php echo JText::_("COM_OSCONTENT_PARENT"); ?></td>
                                    <td colspan="2"><?php echo $this->lists['cate']; ?></td>
                                </tr>

                                <tr>
                                    <td valign="top" style="padding-top:10px;"><?php echo JText::_(
                                            "COM_OSCONTENT_ACCESS_LEVEL"
                                        ); ?></td>
                                    <td><?php echo $this->lists['access']; ?>
                                </tr>

                                <tr>
                                    <td><?php echo JText::_("COM_OSCONTENT_PUBLISHED"); ?></td>
                                    <td><?php echo $this->lists['published']; ?></td>
                                </tr>

                                <tr>
                                    <td valign="top" style="padding-top:10px;">
                                        <input type="checkbox" name="addMenu"
                                               style="margin-top:0px;"><?php echo JText::_(
                                            "COM_OSCONTENT_LINK_TO_MENU"
                                        ); ?>
                                    </td>
                                    <td><?php echo $this->lists['menuselect']; ?><?php echo $this->lists['menuselect3']; ?></td>
                                </tr>

                                <tr>
                                    <td valign="top" style="padding-top:10px;"><?php echo JText::_(
                                            "COM_OSCONTENT_SELECT_MENUTYPE"
                                        ); ?></td>
                                    <td><?php echo $this->lists['link_type']; ?></td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>

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
