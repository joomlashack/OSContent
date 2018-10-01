<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
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
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

$controller = OSController::getInstance('OSContent');

$task = JFactory::getApplication()->input->getCmd('task');

$controller->execute($task);
$controller->redirect();

if (defined('JDEBUG')) {
    JProfiler::getInstance('Application')->mark('com_oscontent');
}
