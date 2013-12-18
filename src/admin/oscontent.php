<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.1
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
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
    $task = JFactory::getApplication()->input->get('task');
}

$controller->execute($task);
$controller->redirect();

if (defined('JDEBUG')) {
    JProfiler::getInstance('Application')->mark('com_oscontent');
}
