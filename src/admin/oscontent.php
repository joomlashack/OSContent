<?php

// no direct access
defined('_JEXEC') or die;

require_once 'controller.php';

if (!JFactory::getUser()->authorise('core.manage', 'com_oscontent') ) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = OSController::getInstance('OSContent');
$controller->execute(JRequest::getCmd('task', 'display'));
$controller->redirect();
