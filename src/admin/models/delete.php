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

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/models/model.php';

class OSContentModelDelete extends OSModel
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
		$query->where('(a.extension = "com_content" OR a.parent_id = 0) AND a.id <> 1');

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

		$categories 	= $this->getCategoryParent();
		$lists['catid']     = JHTML::_('select.genericlist',  $categories, 'catid', 'class="inputbox" size="1"', 'value', 'text', intval( $row->catid ) );

		return $lists;
	}


	function deleteOSContent( $option=null ) {
		global $mainframe;
		$database = & JFactory::getDBO();

		$catid = JRequest::getVar(   'catid', '','POST' );
		$deleteCategory = JRequest::getVar(   'deleteCategory', '' ,'POST');
		$deleteContentOnly = JRequest::getVar(   'deleteContentOnly', '','POST' );
		$where="";



		if ($catid>0) //a cat is selected
		{

			if ($deleteCategory) {
				//delete link menu-cat
				$query = "DELETE m FROM #__menu m "
				. "\n WHERE m.component_id = 22 "
				. "\n AND LOCATE( \"category\", link ) >0 AND LOCATE( \"com_content\", link ) >0 AND LOCATE( \"id={$catid}\", link ) >0"
				;
				$database->setQuery( $query );
				$database->query();

				//delete cat
				$query = "DELETE FROM #__categories"
					. "\n WHERE id=$catid"
					. $where;
					;
				$database->setQuery( $query );
				$database->query();
			}

			if ($deleteContentOnly) {
			$query = "UPDATE #__content SET `introtext`='', `fulltext`='' "
				. "\n WHERE catid=$catid"
				. $where ;
			}
			else {
				//delete full content (article)
				$query = "DELETE co FROM #__content co "
				. "\n WHERE co.catid=$catid"
				. $where ;
			}
			$database->setQuery( $query );
			$database->query();

		}

		//delete content
		return true;
	}

}
