<?php
/*
* OSContent for Joomla 1.7.x, 2.5.x and 3.x
* @version 1.9.1
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
<script language="javascript" type="text/javascript">

	<?php if (version_compare(JVERSION, '3.0', '<')) : ?>
		function submitbutton(pressbutton) {
			if (pressbutton=='delete'){
				if (confirm("<?php echo JText::_("DELETE_ALL");?>"))
					submitform(pressbutton);
				else return;
			}
			else 	submitform(pressbutton);
		}
	<?php else : ?>
		Joomla.submitbutton = function(task, type)
		{
			if (task == 'delete.delete'){
				if (confirm("<?php echo JText::_("DELETE_ALL");?>"))
					Joomla.submitform(task, document.id('delete-form'));
				else return;
			}
			else Joomla.submitform(task, document.id('delete-form'));
		};
	<?php endif; ?>
  </script>
  <h1><?php echo JText::_("DELETE MASS CONTENT");?></h1>
  <form action="<?php echo JRoute::_('index.php?option=com_oscontent');?>" method="post" name="adminForm" id="delete-form" class="adminForm form-validate">

	   <script language="javascript" type="text/javascript">


		// var sectioncategories = new Array;
		// <?php
		// $i = 0;
		// foreach ($this->lists['sectioncategories'] as $k=>$items) {
		// 	foreach ($items as $v) {
		// 		echo "sectioncategories[".$i++."] = new Array('$k','".addslashes($v->id)."','".addslashes($v->name)."');\t";
		// 	}
		// }
		// ?>

		</script>
			<fieldset>
				<legend><?php echo JText::_("DELETE SECTIONS AND CATEGORIES");?></legend>
				<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td colspan="2"><?php echo JText::_("DESTROY_ALL");?></td>
					</tr>
					<tr>
						<td><?php echo  JText::_("CATEGORY")." ".$this->lists['catid']; ?></td>
						<td><input type="checkbox"  id="deleteCategory" name="deleteCategory"><?php echo JText::_("DELETE_CATEGORIES");?></td>
					</tr>
					<tr>
						<td><?php echo JText::_("CONTENT");?></td>
						<td><input type="checkbox"  id="deleteContentOnly" name="deleteContentOnly"><?php echo JText::_("DELETE_ONLY_CONTENT");?></td>
					</tr>
				</table>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
