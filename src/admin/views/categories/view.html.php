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

require_once JPATH_ADMINISTRATOR . '/components/com_oscontent/views/view.php';

/**
 * Mass categories view
 */
class OSContentViewCategories extends OSView
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Mass Categories' ), 'generic.png' );
		JToolBarHelper::apply("categories.save");
        JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::preferences( 'com_oscontent' );

		//get params
		$params = JComponentHelper::getParams('com_oscontent');
		$this->assignRef('params',		$params);
		//get data
		$lists= $this->get('Data');
		$this->assignRef('lists',		$lists);

		parent::display($tpl);
	}

}
