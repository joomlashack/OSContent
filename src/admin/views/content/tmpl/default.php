<?php defined('_JEXEC') or die('Restricted access');
		$hidden="";
		$editor = JFactory::getEditor();
		$createdate = JFactory::getDate();
		JHTML::_('behavior.calendar');
		$post = JRequest::get("post");
		error_reporting(0);
    ?>

	  <script language="javascript" type="text/javascript">

		function copyTitle(){

            if (document.getElementById("duplicateTitle").checked){
                for (i=1;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?>;i++){
                    if (document.getElementById("alias_"+i).value==""){
                        document.getElementById("alias_"+i).value= document.getElementById("title_"+i).value;
                    }
                }
            }
            else {
                for (i=1;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?>;i++){
                    if (document.getElementById("alias_"+i).value==document.getElementById("title_"+i).value){
                        document.getElementById("alias_"+i).value= "";
                    }
                }
            }
        }

        //copy meta
        function copyMeta( meta) {
            if (document.getElementById("duplicate"+meta).checked){
              for (i=2;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?> ;i++){
                  if (document.getElementById(meta+"_"+i).value==""){
                        document.getElementById(meta+"_"+i).value= document.getElementById(meta+"_1").value;
					}
                }
            }
            else {
                for (i=2;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?>;i++){
                    if (document.getElementById(meta+"_"+i).value==document.getElementById(meta+"_1").value){
                        document.getElementById(meta+"_"+i).value= "";
					}
                }
              }
            }

       //copy 1st introtext to all the other introtext
        function copyText(){

		<?php if (/*($editor->_name=="tinymce" || $editor->_name=="jce") && */$this->params->get('displayWysiwyg', 0)!="0") {?>
		//remove <br> of the iframe in firefox with tinymce
		tinyMCE.triggerSave(true, true);
		var re=new RegExp("<br>$");
		document.getElementById("introtext_1").value.replace(re,"");
			document.getElementById("fulltext_1").value.replace(re,"");
		tinyMCE.updateContent("introtext_1");
		tinyMCE.updateContent("fulltext_1");

		<?php } ?>



            if (document.getElementById("duplicateText").checked){
                for (i=2;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?> ;i++){
							//remove <br> of the iframe in firefox with tinymce
							var re=new RegExp("<br>$");
							document.getElementById("introtext_"+i).value.replace(re,"");
							document.getElementById("fulltext_"+i).value.replace(re,"");

                    if ((document.getElementById("introtext_"+i).value=="" || document.getElementById("introtext_"+i).value=="<br>" )&& document.getElementById("introtext_1").value!="<br>"){
                        document.getElementById("introtext_"+i).value= document.getElementById("introtext_1").value;
					}
                    if ((document.getElementById("fulltext_"+i).value=="" || document.getElementById("fulltext_"+i).value=="<br>"  )&& document.getElementById("fulltext_1").value!="<br>"){
                        document.getElementById("fulltext_"+i).value= document.getElementById("fulltext_1").value;
					}

                    if (document.getElementById("title_"+i).value==""){
                        document.getElementById("title_"+i).value= document.getElementById("title_1").value;
					}

					<?php if (/*($editor->_name=="tinymce" || $editor->_name=="jce") &&*/ $this->params->get('displayWysiwyg', 0)!="0") {?>
					tinyMCE.updateContent("introtext_"+i);
					tinyMCE.updateContent("fulltext_"+i);
					<?php } ?>

                }
            }
            else {
                for (i=2;i<<?php echo $this->params->get('nbOSContent', 10)+1; ?>;i++){
                    if (document.getElementById("introtext_"+i).value==document.getElementById("introtext_1").value){
                        document.getElementById("introtext_"+i).value= "";
					}
                    if (document.getElementById("fulltext_"+i).value==document.getElementById("fulltext_1").value){
                        document.getElementById("fulltext_"+i).value= "";
					}
                    if (document.getElementById("title_"+i).value==document.getElementById("title_1").value){
                        document.getElementById("title_"+i).value= "";
					}
					<?php if (/*($editor->_name=="tinymce" || $editor->_name=="jce") &&*/ $this->params->get('displayWysiwyg', 0)!="0") {?>
					tinyMCE.updateContent("introtext_"+i);
					tinyMCE.updateContent("fulltext_"+i);
					<?php } ?>
                }
            }
        }


		function submitbutton(pressbutton) {
			var form = document.adminForm;

             if (!document.getElementById("published").checked){
                document.getElementById("publish_up").value="";
                document.getElementById("state").value=0;
            }
            if ((form.addMenu.checked) && (form.menuselect.value == '')) {
				alert( "Please select a menu." );
            }
            else
                submitform( pressbutton );
		}
	  </script>
      <style>
	  #editor-xtd-buttons
	  {
		  clear:both;
	  }
	  </style>
      <h1>Mass content</h1>
	  <form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

           <script language="javascript" type="text/javascript">


    		var sectioncategories = new Array;
    		var menulist = new Array;
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
    				echo "menulist[".$i++."] = new Array( '".addslashes( $v->menutype )."','".addslashes( $v->id )."','".str_replace('&nbsp;',' ',addslashes(str_replace('&#160;', '-', $v->treename) ))."' );\t";
			}
			}
    		?>

            </script>

            <table border="0" cellpadding="3" cellspacing="0" >
            <tr>
                <td>
                <fieldset>
                    <legend><?php echo JText::_("CREATE UP TO")." ".$this->params->get('nbOSContent', 10)." ". JText::_("ARTICLES IN A ROW");?></legend>
                    <table border="0" cellpadding="3" cellspacing="0">

                    <?php $k = 0;
					for ($i=1;$i< $this->params->get('nbOSContent', 10)+1;$i++) {
					?>
                       <tr bgcolor="<?php echo($k==0)?"#f9f9f9":"#eeeeee";?>">
                            <td><?php echo JText::_("TITLE");?> (<?php echo JText::_("PAGE")." ".$i; ?>): </td>
							<td><input class="inputbox" type="text" size="50" maxlength="255"  id="title_<?php echo $i; ?>" name="title[]" value="<?php echo($post["title"][$i-1]);?>" ></td>

						<?php if ($this->params->get('displayAlias', 1)==1 ){ ?>
							<td><?php echo JText::_("ALIAS");?></td>
							<td><input class="inputbox" type="text" size="50" maxlength="255"  id="alias_<?php echo $i; ?>" name="alias[]" value="<?php echo($post["alias"][$i-1]);?>" ></td>
						<?php }
								else
								$hidden.='<input type="hidden"  id="alias_<?php echo $i; ?>" name="alias[]" value=""  >'; ?>
					    </tr>
						<?php if ($this->params->get('displayIntroText', 1)==1 ){ ?>
                         <tr bgcolor="<?php echo($k==0)?"#f9f9f9":"#eeeeee";?>">
                            <td><?php echo JText::_("INTRO_TEXT")." (".JText::_("PAGE")." ".$i; ?>): </td>
							<?php if (($i==1 && $this->params->get('displayWysiwyg')=="1") ||$this->params->get('displayWysiwyg')=="2" ) echo "<td colspan=\"4\">".$editor->display( 'introtext_'.$i, $post["introtext_".$i] ,'50%', '50', '20', '50' )."</td></tr><tr>" ;
								else {?>
	                            <td><textarea id="<?php echo 'introtext_'.$i;?>" name="<?php echo 'introtext_'.$i;?>" rows="4" cols="35"><?php echo($post["introtext_".($i)]);?></textarea></td>
								<?php }
							}
							else
								$hidden.='<input type="hidden" id="introtext_'.$i.'" name="introtext_'.$i.'" value=""  >'; ?>

							<?php if ($this->params->get('displayFullText', 1)==1){ ?>
	                            <td><?php echo JText::_("FULL_TEXT")." (".JText::_("PAGE")." ".$i; ?>): </td>
								<?php if (($i==1 && $this->params->get('displayWysiwyg')=="1") ||$this->params->get('displayWysiwyg')=="2" ) echo "<td colspan=\"4\">".$editor->display( 'fulltext_'.$i, $post["fulltext_".$i] ,'50%', '50', '20', '50' )."</td>" ; else {?>
                            <td><textarea id="<?php echo 'fulltext_'.$i;?>" name="<?php echo 'fulltext_'.$i;?>" rows="4" cols="35"><?php echo($post["fulltext_".$i]);?></textarea></td><?php }
							}
							else
								$hidden.='<input type="hidden" id="fulltext_'.$i.'" name="fulltext_'.$i.'" value=""  >'; ?>
                        </tr>
              <tr>
							<?php if ($this->params->get('displayMetaDescription')==1){ ?>
							<td ><?php echo JText::_("META_DESC");?></td>
                            <td><textarea id="metadesc_<?php echo $i; ?>" name="metadesc[]" rows="1" cols="35"><?php echo($post["metadesc"][$i-1]);?></textarea></td><?php }
							else
								$hidden.='<input type="hidden" id="metadesc_'.$i.'" name="metadesc[]" value=""  >'; ?>

							<?php if ($this->params->get('displayMetaKeywords')==1){ ?>
							<td ><?php echo JText::_("META_KEY");?></td>
                            <td><textarea id="metakey_<?php echo $i; ?>" name="metakey[]" rows="1" cols="35"><?php echo($post["metakey"][$i-1]);?></textarea></td><?php }
							else
								$hidden.='<input type="hidden" id="metakey_'.$i.'" name="metakey[]" value=""  >'; ?>
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
                            <td><?php echo JText::_("COPY_FIRST_TITLE");?></td>
                            <td><input type="checkbox"  id="duplicateText" name="duplicateText" onClick="javascript:copyText()" ></td>
                        </tr>
                        <?php if ($this->params->get('displayMetaDescription')==1): ?>
                         <tr>
                            <td><?php echo JText::_("COPY_FIRST_META");?></td>
                            <td><input type="checkbox"  id="duplicatemetadesc" name="duplicatemetadesc" onClick="javascript:copyMeta('metadesc')" ></td>
                        </tr>
                        <?php endif;?>
						<?php if ($this->params->get('displayMetaKeywords')==1): ?>
                         <tr>
                            <td><?php echo JText::_("COPY_FIRST_KEYWORDS");?></td>
                            <td><input type="checkbox"  id="duplicatemetakey" name="duplicatemetakey" onClick="javascript:copyMeta('metakey')" ></td>
                        </tr>
                        <?php endif;?>
                        <tr>
                            <td colspan="2"><?php echo JText::_("ONLY_PAGES_WITH_TITLE");?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("PUBLISHED");?></td>
                            <td><input type="checkbox" id="published" name="published" checked></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("FRONTPAGE");?></td>
                            <td><input type="checkbox" id="frontpage" name="frontpage"  ></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("ARCHIVED");?></td>
                            <td><input type="checkbox" id="state2" name="state2" ></td>
                        </tr>
                        <tr>
                             <td><?php echo JText::_("ACCESS_LEVEL");?></td>
                            <td><?php echo $this->lists['access']; ?>
                        <tr>
                             <td><?php echo JText::_("AUTHOR");?></td>
                            <td><?php echo $this->lists['created_by']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_("AUTHOR_ALIAS");?></td>
                            <td><input type="text" name="created_by_alias" id="created_by_alias" value=""   size="20" /></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_("CATEGORY");?></td>
                            <td> <?php echo $this->lists['catid']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="addMenu" ><?php echo JText::_("LINK TO MENU");?></td>
						 <tr>
						 <td colspan="2"><?php echo $this->lists['menuselect']; ?> <?php echo $this->lists['menuselect3']; ?></td>
                        </tr>
						<?php if ($this->params->get('displayMetaRobots')==1){ ?>
                        <tr>
                            <td><?php echo JText::_("ROBOTS");?></td>
                            <td><input type="text" name="robots" size="20"></td>
                        </tr>  		<?php }?>
						<?php if ($this->params->get('displayMetaAuthor')==1){ ?>
                        <tr>
                            <td><?php echo JText::_("AUTHOR");?></td>
                            <td><input type="text" name="author" size="20"></td>
                        </tr>  		<?php }?>
						<tr>
							<td><?php echo JText::_("CREATED_DATE");?></td>
							<td><?php echo JHTML::_( 'calendar',JHTML::_('date', $createdate->toMySQL(), 'Y-m-d H:i:s'),"created","created"); ?></td>
							<?php //echo JHTML::_( 'calendar',date( 'Y-m-d H:i:s' ),"publish_up","publish_up"); ?>
						</tr>
						<tr>
							<td><?php echo JText::_("START_PUBLISHING");?></td>
							<td><?php echo JHTML::_( 'calendar',JHTML::_('date', $createdate->toMySQL(), 'Y-m-d H:i:s'),"publish_up","publish_up"); ?></td>
							<?php //echo JHTML::_( 'calendar',date( 'Y-m-d H:i:s' ),"publish_up","publish_up"); ?>
						</tr>
						<tr>
							<td><?php echo JText::_("FINISH_PUBLISHING");?></td>
							<td><?php echo JHTML::_( 'calendar',"Never","publish_down","publish_down"); ?></td>
						</tr>

                    </table>
                </fieldset>
                </td>
            </tr>
            </table>
            <input type="hidden" name="task" value="" >
            <input type="hidden" id="state" name="state" value="1" >
            <input type="hidden" name="id" value="" >
            <input type="hidden" name="option" value="com_oscontent" >
			<?php echo $hidden;?>
        </form>

