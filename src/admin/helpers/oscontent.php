<?php
/*
* OSContent for Joomla 1.7.x, 2.5.x and 3.x
* @version 1.5
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

class OSContentHelper
{
	public static function addSubmenu($vName)
	{

		JSubMenuHelper::addEntry(
			JText::_('MASS CONTENT'),
			'index.php?option=com_oscontent&view=content',
			$vName == 'content'
		);

		JSubMenuHelper::addEntry(
			JText::_('MASS CATEGORIES'),
			'index.php?option=com_oscontent&view=categories',
			$vName == 'categories'
		);
		JSubMenuHelper::addEntry(
			JText::_('MASS DELETE'),
			'index.php?option=com_oscontent&view=delete',
			$vName == 'delete'
		);

	}
}
?>
