<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>

<style>
.radiobtn
{
	float:left !important;
	clear:none;
	padding-right:10px;
}
</style>

<h1><?php echo JText::_("Mass Categories");?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_oscontent');?>" method="post" name="adminForm" id="categories-form" class="adminForm form-validate">
	<table border="0" cellpadding="3" cellspacing="0" >
		<tr valign="top">
			<td>
				<fieldset>
					<legend><?php echo  JText::_("CREATE UP TO")." ".$this->params->get('nbOSCategories', 10)." ".  JText::_("CATEGORIES IN A ROW"); ?></legend>
					<table border="0" cellpadding="3" cellspacing="0">
						<?php
						$k = 0;
						for ($i=1;$i< $this->params->get('nbOSCategories', 10)+1;$i++) { ?>
							<tr bgcolor="<?php echo($k==0)?"#f9f9f9":"#eeeeee";?>">
								<td><?php echo JText::_("CATEGORY")." ".$i; ?>: <?php echo JText::_("TITLE");?></td>
								<td><input class="inputbox" type="text" size="25" maxlength="255" id="title_<?php echo $i; ?>" name="title[]" value="" ></td>
								<td><?php echo JText::_("ALIAS");?></td>
								<td><input class="inputbox" type="text" size="25" maxlength="255" id="alias_<?php echo $i; ?>" name="alias[]" value="" ></td>
							</tr>
						<?php $k = 1 - $k;
						} ?>
					</table>
				</fieldset>
			</td>

			<td valign="top">
				<fieldset>
					<legend><?php echo JText::_("OPTIONS");?></legend>
					<table border="0" cellpadding="3" cellspacing="0">
						<tr>
							<td><?php echo JText::_("COPY TITLE TO ALIAS");?></td>
							<td><input type="checkbox"  id="duplicateTitle" name="duplicateTitle" onClick="javascript:copyTitle()" ></td>
						</tr>
						<tr>
							<td><?php echo JText::_("PARENT");?></td>
							<td colspan="2"><?php echo $this->lists['cate']; ?></td>
						</tr>
						<tr>
							<td valign="top" style="padding-top:10px;"><?php echo JText::_("ACCESS LEVEL");?></td>
							<td><?php echo $this->lists['access']; ?>
						<tr>
						<tr>
							<td><?php echo JText::_("PUBLISHED");?></td>
							<td><?php echo $this->lists['published']; ?></td>
						</tr>
						<tr>
							<td valign="top" style="padding-top:10px;"><input type="checkbox" name="addMenu" style="margin-top:0px;"><?php echo JText::_("LINK TO MENU");?></td>
							<td><?php echo $this->lists['menuselect']; ?><?php echo $this->lists['menuselect3']; ?></td>
						</tr>
						<tr>
							<td valign="top" style="padding-top:10px;" ><?php echo JText::_("SELECT MENU TYPE");?></td>
							<td><?php echo $this->lists['link_type']; ?></td>
						<tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>


	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>


<script type="text/javascript">
	<?php if (version_compare(JVERSION, '3.0', '<')) : ?>
		var form = document.adminForm;
		if ((form.addMenu.checked) && (form.menuselect.value == '')) {
			alert(  "<?php  echo  JText::_("PLEASE SELECT A MENU TYPE");?>" );
			return;
		}
		else if ((form.addMenu.checked) && (form.link_type.value == '')) {
			alert( "<?php  echo  JText::_("PLEASE SELECT A MENU");?>" );
			return;
		}
		else{
			submitform( pressbutton );
		}
	<?php else : ?>
		Joomla.submitbutton = function(task, type)
		{
			var form = document.adminForm;
			if ((form.addMenu.checked) && (form.menuselect.value == '')) {
				alert(  "<?php  echo  JText::_("PLEASE SELECT A MENU TYPE");?>" );
				return;
			}
			else if ((form.addMenu.checked) && (form.link_type.value == '')) {
				alert( "<?php  echo  JText::_("PLEASE SELECT A MENU");?>" );
				return;
			}
			else{
				Joomla.submitform(task, document.id('categories-form'));
			}
		};
	<?php endif; ?>


	var menulist = [];
	<?php
	//sub menus
	$i = 0;
	$top=0;
	foreach ( $this->lists['menulist']  as $k=>$items) {
		$top=0;
		foreach ($items as $v) {
			if ($top==0)
			{
				echo "menulist[".$i++."] = new Array( '".addslashes( $v->menutype)."','-1','Top' );\t";
				$top=1;
			}
			echo "menulist[".$i++."] = new Array( '".addslashes( $v->menutype )."','".addslashes( $v->id )."','".str_replace('&nbsp;',' ',addslashes(str_replace('&#160;', '-',  $v->treename) ))."' );\t";
		}
	}
	?>

	function copyTitle() {
		if (document.getElementById("duplicateTitle").checked){
			for (i=1;i<<?php echo $this->params->get('nbOSCategories', 10)+1; ?>;i++){
				if (document.getElementById("alias_"+i).value==""){
					document.getElementById("alias_"+i).value= document.getElementById("title_"+i).value;
				}
			}
		} else {
			for (i=1;i<<?php echo $this->params->get('nbOSCategories', 10)+1; ?>;i++){
				if (document.getElementById("alias_"+i).value==document.getElementById("title_"+i).value){
					document.getElementById("alias_"+i).value= "";
				}
			}
		}
	}
</script>
