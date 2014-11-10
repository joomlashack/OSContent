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
JHTML::_('behavior.calendar');

$hidden = "";
$editor = JFactory::getEditor();

// TODO: Check if this variable is really needed
$post = $this->post;

$createdate = JFactory::getDate();

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    $createdate = $createdate->toMySQL();
} else {
    $createdate = $createdate->toSql();
}
?>

<?php if (!empty($this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>

</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif; ?>

<script language="javascript" type="text/javascript">
    // Execute when page is loaded
    window.onload = setDateTime;
    
    // Set date/time
    function setDateTime() {
        document.getElementById("created").value = "<?php echo $createdate; ?>";
        document.getElementById("publish_up").value = "<?php echo $createdate; ?>";
        
        return false;
    }

    // Copy 1st introtext to all the other introtext
    function copyText() {
        <?php if (/*($editor->_name == "tinymce" || $editor->_name == "jce") && */$this->params->get('displayWysiwyg', 0) != "0"): ?>
        // Remove <br> of the iframe in firefox with tinymce
        tinyMCE.triggerSave(true, true);
        var re = new RegExp("<br>$");
        document.getElementById("introtext_1").value.replace(re, "");
        document.getElementById("fulltext_1").value.replace(re, "");
        tinyMCE.updateContent("introtext_1");
        tinyMCE.updateContent("fulltext_1");
        <?php endif; ?>

        if (document.getElementById("duplicateText").checked) {
            for (i = 2; i < <?php echo $this->params->get('nbOSContent', 10) + 1; ?>; i++) {
                // Remove <br> of the iframe in firefox with tinymce
                var re = new RegExp("<br>$");
                document.getElementById("introtext_" + i).value.replace(re, "");
                document.getElementById("fulltext_" + i).value.replace(re, "");

                if ((document.getElementById("introtext_" + i).value == "" || document.getElementById("introtext_" + i).value == "<br>") && document.getElementById("introtext_1").value != "<br>") {
                    document.getElementById("introtext_" + i).value = document.getElementById("introtext_1").value;
                }
                if ((document.getElementById("fulltext_" + i).value == "" || document.getElementById("fulltext_" + i).value == "<br>" ) && document.getElementById("fulltext_1").value != "<br>") {
                    document.getElementById("fulltext_" + i).value = document.getElementById("fulltext_1").value;
                }

                if (document.getElementById("title_" + i).value == "") {
                    document.getElementById("title_" + i).value = document.getElementById("title_1").value + " " + i;
                }

                <?php if (/*($editor->_name == "tinymce" || $editor->_name == "jce") &&*/ $this->params->get('displayWysiwyg', 0) != "0"): ?>
                tinyMCE.updateContent("introtext_" + i);
                tinyMCE.updateContent("fulltext_" + i);
                <?php endif; ?>

            }
        }
        else {
            for (i = 2; i < <?php echo $this->params->get('nbOSContent', 10) + 1; ?>; i++) {
                if (document.getElementById("introtext_" + i).value == document.getElementById("introtext_1").value) {
                    document.getElementById("introtext_" + i).value = "";
                }
                if (document.getElementById("fulltext_" + i).value == document.getElementById("fulltext_1").value) {
                    document.getElementById("fulltext_" + i).value = "";
                }
                if (document.getElementById("title_" + i).value == document.getElementById("title_1").value) {
                    document.getElementById("title_" + i).value = "";
                }
                <?php if (/*($editor->_name == "tinymce" || $editor->_name == "jce") &&*/ $this->params->get('displayWysiwyg', 0) != "0"): ?>
                tinyMCE.updateContent("introtext_" + i);
                tinyMCE.updateContent("fulltext_" + i);
                <?php endif; ?>
            }
        }
    }

    <?php // Joomla 3.x Backward Compatibility ?>
    <?php if (version_compare(JVERSION, '3.0', '<')) : ?>
    function submitbutton(pressbutton) {
        var form = document.adminForm;

        if (!document.getElementById("published").checked) {
            document.getElementById("publish_up").value = "";
            document.getElementById("published").value = 0;
        }

        if ((form.addMenu.checked) && (form.menuselect.value == '')) {
            alert("Please select a menu.");
        }
        else {
            submitform(pressbutton);
        }
    }
    <?php else : ?>
    Joomla.submitbutton = function (task, type) {
        var form = document.adminForm;

        if (!document.getElementById("published").checked) {
            document.getElementById("publish_up").value = "";
            document.getElementById("published").value = 0;
        }

        if ((form.addMenu.checked) && (form.menuselect.value == '')) {
            alert("Please select a menu.");
        }
        else {
            Joomla.submitform(task, document.id('content-form'));
        }
    }
    <?php endif; ?>
</script>

<style>
    #editor-xtd-buttons {
        clear: both;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_oscontent'); ?>" method="post" name="adminForm"
      id="content-form" class="adminForm form-validate">
<script language="javascript" type="text/javascript">
    var sectioncategories = new Array;
    var menulist = new Array;
    <?php

    // Sub menus
    $i = 0;
    $top = 0;

    foreach ($this->lists['menulist'] as $k => $items)
    {
        $top = 0;

        foreach ($items as $v)
        {
            if ($top == 0)
            {
                echo "menulist[" . ($i++) . "] = new Array('" . addslashes($v->menutype) . "','-1','Top');\t";
                $top = 1;
            }

            echo "menulist[" . ($i++) . "] = new Array('" . addslashes($v->menutype) . "','" . addslashes($v->id) . "','" .
                str_replace('&nbsp;', ' ', addslashes(str_replace('&#160;', '-', $v->treename))) . "');\t";
        }
    }
    ?>
</script>

<table border="0" cellpadding="3" cellspacing="0">
    <tr>
        <td class="ost-table-cell-left">
            <fieldset>
                <legend><?php echo JText::_("COM_OSCONTENT_CREATEUPTO") . " " . $this->params->get(
                            'nbOSContent',
                            10
                        ) . " " . JText::_("COM_OSCONTENT_ARTICLESINAROW"); ?></legend>
                <table class="table table-striped">

                    <?php $k = 0; ?>
                    <?php for ($i = 1; $i < $this->params->get('nbOSContent', 10) + 1; $i++): ?>
                        <tr>
                            <td><?php echo JText::_("COM_OSCONTENT_TITLE"); ?> (<?php echo JText::_(
                                        "COM_OSCONTENT_PAGE"
                                    ) . " " . $i; ?>):
                            </td>
                            <td><input class="inputbox" type="text" size="50" maxlength="255"
                                       id="title_<?php echo $i; ?>" name="title[]"
                                       value="<?php echo(@$post["title"][$i - 1]); ?>"></td>

                            <?php if ($this->params->get('displayAlias', 1) == 1): ?>
                                <td><?php echo JText::_("COM_OSCONTENT_ALIAS"); ?></td>
                                <td><input class="inputbox" type="text" size="50" maxlength="255"
                                           id="alias_<?php echo $i; ?>" name="alias[]"
                                           value="<?php echo(@$post["alias"][$i - 1]); ?>" 
                                           placeholder="<?php echo JText::_("COM_OSCONTENT_ALIAS_DESCRIPTION_PLACEHOLDER"); ?>"></td>
                            <?php else: ?>
                                <?php $hidden .= '<input type="hidden" id="alias_<?php echo $i; ?>" name="alias[]" value =""  >'; ?>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <?php if ($this->params->get('displayIntroText', 1) == 1): ?>
                                <td><?php echo JText::_("COM_OSCONTENT_INTRO_TEXT") . " (" . JText::_(
                                            "COM_OSCONTENT_PAGE"
                                        ) . " " . $i; ?>):
                                </td>
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
                                        ) . "</td></tr><tr>"; ?>
                                <?php else: ?>
                                    <td>
                                        <textarea id="<?php echo 'introtext_' . $i; ?>"
                                                  name="<?php echo 'introtext_' . $i; ?>" rows="4"
                                                  cols="35"><?php echo(@$post["introtext_" . ($i)]); ?></textarea>
                                    </td>
                                <?php endif; ?>
                            <?php
                            else:
                                $hidden .= '<input type="hidden" id="introtext_' . $i . '" name="introtext_' . $i . '" value =""  >'; ?>
                            <?php endif; ?>

                            <?php if ($this->params->get('displayFullText', 1) == 1): ?>
                                <td><?php echo JText::_("COM_OSCONTENT_FULL_TEXT") . " (" . JText::_(
                                            "COM_OSCONTENT_PAGE"
                                        ) . " " . $i; ?>):
                                </td>
                                <?php if (($i == 1 && $this->params->get(
                                            'displayWysiwyg'
                                        ) == "1") || $this->params->get('displayWysiwyg') == "2"
                                ): ?>
                                    <?php echo "<td colspan=\"4\">" . $editor->display(
                                            'fulltext_' . $i,
                                            @$post["fulltext_" . $i],
                                            '50%',
                                            '50',
                                            '20',
                                            '50'
                                        ) . "</td>"; ?>
                                <?php else: ?>
                                    <td>
                                        <textarea id="<?php echo 'fulltext_' . $i; ?>"
                                                  name="<?php echo 'fulltext_' . $i; ?>" rows="4"
                                                  cols="35"><?php echo(@$post["fulltext_" . $i]); ?></textarea>
                                    </td>
                                <?php endif; ?>
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
        </td>

        <td valign="top" class="ost-table-cell-right">
            <fieldset>
                <legend><?php echo JText::_("COM_OSCONTENT_OPTIONS"); ?></legend>

                <table border="0" cellpadding="3" cellspacing="0">

                    <tr>
                        <td>
                            <?php echo JText::_("COM_OSCONTENT_COPY_FIRST_TITLE"); ?>
                            <?php
                            echo JHTML::tooltip(JText::_("COM_OSCONTENT_COPY_FIRST_TITLE_TOOLTIP"),
                            '',
                            'tooltip.png',
                            '',
                            '');
                            ?>
                        </td>
                        <td><input type="checkbox" id="duplicateText" name="duplicateText"
                                   onClick="javascript:copyText()"></td>
                    </tr>

                    <tr>
                        <td colspan="2"><?php echo JText::_("COM_OSCONTENT_PAGES_TITLE_ONLY"); ?></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_PUBLISHED"); ?></td>
                        <td><input type="checkbox" id="published" name="published" checked></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_FEATURED"); ?></td>
                        <td><input type="checkbox" id="featured" name="featured"></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_ACCESS_LEVEL"); ?></td>
                        <td><?php echo $this->lists['access']; ?></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_AUTHOR"); ?></td>
                        <td><?php echo $this->lists['created_by']; ?></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_AUTHOR_ALIAS"); ?></td>
                        <td><input type="text" name="created_by_alias" id="created_by_alias" value="" size="20"/></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_CATEGORY"); ?></td>
                        <td> <?php echo $this->lists['catid']; ?></td>
                    </tr>

                    <tr>
                        <td colspan="2"><input type="checkbox" name="addMenu"><?php echo JText::_(
                                "COM_OSCONTENT_LINK_TO_MENU"
                            ); ?></td>
                    </tr>

                    <tr>
                        <td colspan="2"><?php echo $this->lists['menuselect']; ?> <?php echo $this->lists['menuselect3']; ?></td>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_CREATED_DATE"); ?></td>
                        <td><?php echo JHTML::_(
                                'calendar',
                                JHTML::_('date', $createdate, 'Y-m-d H:i:s'),
                                "created",
                                "created"
                            ); ?></td>
                        <?php // echo JHTML::_('calendar', date('Y-m-d H:i:s'), "publish_up", "publish_up"); ?>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_PUBLISH_UP"); ?></td>
                        <td><?php echo JHTML::_(
                                'calendar',
                                JHTML::_('date', $createdate, 'Y-m-d H:i:s'),
                                "publish_up",
                                "publish_up"
                            ); ?></td>
                        <?php // echo JHTML::_('calendar', date('Y-m-d H:i:s'), "publish_up", "publish_up"); ?>
                    </tr>

                    <tr>
                        <td><?php echo JText::_("COM_OSCONTENT_PUBLISH_DOWN"); ?></td>
                        <td><?php echo JHTML::_('calendar', "Never", "publish_down", "publish_down"); ?></td>
                    </tr>
                </table>
            </fieldset>
        </td>
    </tr>
</table>

<input type="hidden" name="task" value=""/>
<?php echo JHtml::_('form.token'); ?>
<?php echo $hidden; ?>
</form>

</div>
<!-- #j-main-container -->
