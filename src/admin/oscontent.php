<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

if (!JFactory::getUser()->authorise('core.manage', 'com_oscontent') ) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
} 
$controller = JController::getInstance('OSContent');
$controller->execute(JRequest::getCmd('task', 'display'));
$controller->redirect(); 
?>