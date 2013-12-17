<?php
/**
 * OSContent
 *
 * Joomla 1.7.x, 2.5.x and 3.x
 *
 * OSContent is an extension for creating and deleting articles and categories in bulk/mass.
 * You can even create menu items for the newly created content.
 *
 * Forked from MassContent (http://www.baticore.com/index.php?option=com_content&view=article&id=1&Itemid=14)
 * because it was only available for Joomla 1.5.
 *
 * @category   Joomla Component
 * @package    OSContent
 * @author     Johann Eriksen
 * @copyright  (C) 2007-2009 Johann Eriksen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.9.1
 * @link       http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleDeclaration('
.icon-48-oscontent {
    background-image: url("components/com_oscontent/media/images/icon-48-oscontent.png");
}
');

require_once 'controller.php';

if (!JFactory::getUser()->authorise('core.manage', 'com_oscontent'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = OSController::getInstance('OSContent');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<'))
{
	$task = JRequest::getCmd('task', 'display');
}
else
{
	$task = JFactory::getApplication()->input->get('task');
}

$controller->execute($task);
$controller->redirect();

if (defined('JDEBUG'))
{
	JProfiler::getInstance('Application')->mark('com_oscontent');
}
