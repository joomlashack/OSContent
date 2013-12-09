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

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/views/view.php';

/**
 * Mass categories view
 */
class OSContentViewCategories extends OSView
{
	public function display($tpl = null)
	{

		JToolBarHelper::title(  JText::_('Mass Categories'), 'generic.png');
		JToolBarHelper::apply("categories.save");
		JToolbarHelper::cancel('categories.cancel');
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::preferences('com_oscontent');

		//get params
		$params = JComponentHelper::getParams('com_oscontent');

		//get data
		$lists = $this->get('Data');

		if (version_compare(JVERSION, '3.0', '<')) {
			$this->assignRef('params',		$params);
			$this->assignRef('lists',		$lists);
		} else {
			$this->params = $params;
			$this->lists = $lists;
		}

		parent::display($tpl);
	}

}
