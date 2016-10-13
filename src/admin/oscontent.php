<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration(
    '
    .icon-48-oscontent {
        background-image: url("components/com_oscontent/media/images/icon-48-oscontent.png");
    }
    '
);

require_once 'controller.php';

if (!JFactory::getUser()->authorise('core.manage', 'com_oscontent')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = OSController::getInstance('OSContent');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    $task = JRequest::getCmd('task', 'display');
} else {
    $task = JFactory::getApplication()->input->getCmd('task');
}

$controller->execute($task);
$controller->redirect();

if (defined('JDEBUG')) {
    JProfiler::getInstance('Application')->mark('com_oscontent');
}
