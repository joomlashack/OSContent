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

require_once 'controller.php';

if (!JFactory::getUser()->authorise('core.manage', 'com_oscontent') ) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = OSController::getInstance('OSContent');

if (version_compare(JVERSION, '3.0', '<')) {
	$task = JRequest::getCmd('task', 'display');
} else {
	$task = JFactory::getApplication()->input->get('task');
}
$controller->execute($task);
$controller->redirect();
