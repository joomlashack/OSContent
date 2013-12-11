 <?php
/*
* OSContent for Joomla 1.7.X
* @version 1.5
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class OSContentModelCategories extends JModel
{
	protected function getCategoryParent()
	{
		// Initialise variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__categories AS a');
		$query->join('LEFT', '`#__categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		$extension = "com_content";
		$query->where('(a.extension = "com_content" OR a.parent_id = 0)');

		/*
		// Prevent parenting to children of this item.
		if ($id = $this->form->getValue('id')) {
			$query->join('LEFT', '`#__categories` AS p ON p.id = '.(int) $id);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

			$rowQuery	= $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
			$rowQuery->from('#__categories AS a');
			$rowQuery->where('a.id = ' . (int) $id);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}
		*/

		$query->where('a.published IN (0,1)');
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if ($options[$i]->level == 0) {
				$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
			}

			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		// Initialise variables.
		$user = JFactory::getUser();

		if (empty($id)) {
			// New item, only have to check core.create.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.create', $extension.'.category.'.$option->value)) {
					unset($options[$i]);
				}
			}
		}
		else {
			// Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.edit', $extension.'.category.'.$option->value)) {
					// As a backup, check core.edit.own
					if (!$user->authorise('core.edit.own', $extension.'.category.'.$option->value)) {
						// No core.edit nor core.edit.own - bounce this one
						unset($options[$i]);
					}
					else {
						// TODO I've got a funny feeling we need to check core.create here.
						// Maybe you can only get the list of categories you are allowed to create in?
						// Need to think about that. If so, this is the place to do the check.
					}
				}
			}
		}


		if (isset($row) && !isset($options[0])) {
			if ($row->parent_id == '1') {
				$parent = new stdClass();
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
		}

		return $options;
	} 	

	function &getData(){
		global  $my, $mainframe;
		$database = & JFactory::getDBO();
		
		$uid=0;
		$scope 		= "content";
		$option 	= "com_oscontent";
		
		$row =& $this->getTable();
		// load the row from the db table
		$row->load( (int)$uid );
		
		$row->scope 		= $scope;
		$row->published 	= 1;
		$menus 				= array();
		
		$javascript2 = "onchange=\"changeDynaList( 'menuselect3', menulist, document.adminForm.menuselect.options[document.adminForm.menuselect.selectedIndex].value, 0, 0);\"";
		$categoriesparent 	= $this->getCategoryParent();		
		$lists['cate'] 		= JHTML::_('select.genericlist',   $categoriesparent, 'parent_id', 'class="inputbox" size="1"', 'value', 'text', null );
		
		
		// build the html select list for section types
		$types[] = JHTML::_('select.option', '', 'Select Type' );
		$types[] = JHTML::_('select.option', 'content_category', 'Category List Layout' );
		$types[] = JHTML::_('select.option', 'content_blog_category', 'Category Blog Layout' );
		$lists['link_type'] 		= JHTMLSelect::genericList( $types, 'link_type', 'class="inputbox" size="1"', 'value', 'text' );

				
		
		$menuTypes 	= $this->getMenuTypes(); 
		foreach ( $menuTypes as $menuType ) 
		{
			$menu[] = JHTML::_('select.option',  $menuType, $menuType );
		}
		
		
		// build the html select list for the group access
		$lists['access'] 			= JHTML::_('list.accesslevel', $row );
		// build the html radio buttons for published
		$lists['published'] 		= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published );
		
		$stop_notice = array();
		$lists['menuselect3'] = JHTML::_('select.genericlist',   $stop_notice, 'menuselect3', 'class="inputbox" size="10"', 'value', 'text', null );
		
		$lists['menulist']=  $this->createSubMenu();
		
		$lists['menuselect'] = JHTML::_('select.genericlist',   $menu, 'menuselect', 'class="inputbox" size="10" '.  $javascript2, 'value', 'text', null );
		return $lists;
	
	}
	/**
	 * Get a list of the menutypes
	 * @return array An array of menu type names
	 */
	function getMenuTypes()
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT menutype' .
				' FROM #__menu_types';
		$db->setQuery( $query );
		return $db->loadResultArray();
	} 
	
	function createSubMenu ()
	{
		// build the html select list for menu selection
		
		$menulist = array();
		$database = & JFactory::getDBO();
		$menuTypes 	= $this->getMenuTypes(); 
		foreach ( $menuTypes as $menuType ) {
			//$menu = JHTML::_('select.option',  $menuType, $menuType );
			///////////////////////////////////////////////////
			//Create tje tree of menus
			//http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,references:joomla.framework:html:jhtmlmenu-treerecurse/
			$query = 'SELECT id, parent_id, title, menutype' .
				' FROM #__menu' .
				' WHERE menutype = "'.$menuType .'" AND published = 1'.
				' ORDER BY menutype, parent_id, ordering'
				;
			
			$database->setQuery($query);
			$menuItems4 = $database->loadObjectList();
			
			$children = array();
			
			if ($menuItems4) {
				// first pass - collect children
				foreach ($menuItems4 as $v) {	// iterate through the menu items
					$pt 	= $v->parent_id;		// we use parent as our array index
					
					// if an array entry for the parent doesn't exist, we create a new array
					$list 	= @$children[$pt] ? $children[$pt] : array();
					
					// we push our item onto the array (either the existing one for the specified parent or the new one
					array_push( $list, $v );
					// We put out updated list into the array
					$children[$pt] = $list;
				}
			} 
			// second pass - get an indent list of the items
			$list = JHTML::_('menu.treerecurse', @$menuItems4[0]->parent_id, '-', array(), $children, 9999, 0, 0 );
			$menulist[] = $list ;
			
		}
		return $menulist;
		///////////////////////////////////////////////////
	} 	
	
	function saveOSCategories( $option=null ){
		$post		= JRequest::get("post");
		
		for ($i = 0; $i < count($post["title"]); $i++)
		{
			if ($post["title"][$i] == "")
				continue;
			$table				= $this->getTable(); 		
			$table->id 			= 0;
			$table->title 		= $post["title"][$i];
			$table->alias		= JFilterOutput::stringURLSafe($post["alias"][$i]);  
			if (trim(str_replace('-', '', $table->alias)) == '')
			{
				$table->alias = JFactory::getDate()->format('Y-m-d-H-i-s') ."-".$i;
			} 
			$table->extension	= "com_content";
			$table->published	= $post["published"];
			$table->access		= $post["access"];
			$table->language	= "*";
			$table->setLocation($post["parent_id"], 'last-child');
			if (!$table->store())
				return false;
			if ($post["addMenu"]) {  
            	$this->menuLink($table->id, $table->title,$post["menuselect"],$post["link_type"], $post["menuselect3"] , $table->alias); 
			}
		}
		return true;  
	}
	
	function menuLink( $id, $title,$menuselect,$contentType,$parent , $alias = "" ) {
		global $mainframe;
		$database = & JFactory::getDBO();
		
		
		$menu = strval( $menuselect );
		$link = strval( $title );
		
		$link	= stripslashes( JFilterOutput::ampReplace($link) );
		
		//find what kind of link needs to be created in $row->link
		switch ($contentType){
			case "content_section":
				$taskLink = "section";
				break;
			case "content_blog_section":
				$taskLink = "section&layout=blog";
				break;            ;    
			case "content_category":
				$taskLink = "category";
				break;
			case "content_blog_category":
				$taskLink = "category&layout=blog";
				break;                         
			default:
				$taskLink = "article";
		}
		
		
		$row  =& JTable::getInstance('menu');
		$row->menutype 		= $menu;
		$row->title			= $link;
		$row->alias         = $alias ? JFilterOutput::stringURLSafe($alias) : JFilterOutput::stringURLSafe($link); 
		$row->parent_id		= ($parent==-1)?1:$parent;
		$row->type 			= 'component';
		$row->link			= 'index.php?option=com_content&view='.$taskLink.'&id='. $id;	
		$row->published		= 1;
		$row->language		= "*";
		
		//$row->componentid	= $id;
		$row->component_id	= 22;
		//$row->ordering =    9999;

		
		$params = array();
		$params['display_num'] = 10;
		$params['show_headings'] = 1;
		$params['show_date'] = 0;
		$params['date_format'] = "";
		$params['filter'] = 1;
		$params['filter_type'] = "title";
		$params['orderby_sec'] = "";
		$params['show_pagination'] = 1;
		$params['show_pagination_limit'] = 1;
		$params['show_noauth'] = "";
		$params['show_title'] = "";
		$params['link_titles'] = "";
		$params['show_intro'] = "";
		$params['show_section'] = "";
		$params['link_section'] = "";
		$params['show_category'] = "";
		$params['link_category'] = "";
		$params['show_author'] = "";
		$params['show_create_date'] = "";
		$params['show_modify_date'] = "";
		$params['show_item_navigation'] = "";
		$params['show_readmore'] = "";
		$params['show_vote'] = "";
		$params['show_icons'] = "";
		$params['show_pdf_icon'] = "";
		$params['show_print_icon'] = "";
		$params['show_email_icon'] = "";
		$params['show_hits'] = "";
		$params['feed_summary'] = "";
		$params['page_title'] = "";
		$params['show_page_title'] = 1;
		$params['pageclass_sfx'] = "";
		$params['menu_image'] = "";
		$params['secure'] = 0;

		$registry = new JRegistry;
		$registry->loadArray($params);
		$row->params = (string)$registry; 
		
		$row->setLocation($row->parent_id, 'last-child');
		
		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$row->reorder( "menutype = " . $database->Quote( $row->menutype ) . " AND parent = " . (int) $row->parent );
		
		// clean any existing cache files
		//mosCache::cleanCache( 'com_content' );	
	}
	
}
