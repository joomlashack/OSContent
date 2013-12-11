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

class OSContentHelper
{
	public static function addSubmenu($vName)
	{
		if (version_compare(JVERSION, '3.0', '<')) {
			$subMenuClass = 'JSubMenuHelper';
		} else {
			$subMenuClass = 'JHtmlSidebar';
		}

		$subMenuClass::addEntry(
			JText::_('MASS CONTENT'),
			'index.php?option=com_oscontent&view=content',
			$vName == 'content'
		);

		$subMenuClass::addEntry(
			JText::_('MASS CATEGORIES'),
			'index.php?option=com_oscontent&view=categories',
			$vName == 'categories'
		);
		$subMenuClass::addEntry(
			JText::_('MASS DELETE'),
			'index.php?option=com_oscontent&view=delete',
			$vName == 'delete'
		);

	}
}
?>
