<?php
/*
* OSContent for Joomla 1.6
* @version 1.5
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

jimport('joomla.application.component.controller');

/**
 * OSContent component Controller
 *
 */
class OSContentController extends JController
{
	protected $default_view = 'content'; 
	
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		require_once JPATH_COMPONENT.'/helpers/oscontent.php';
		OSContentHelper::addSubmenu(JRequest::getCmd('view', 'content')); 		
		parent::display();
	}

}
?> 