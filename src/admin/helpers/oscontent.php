<?php
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