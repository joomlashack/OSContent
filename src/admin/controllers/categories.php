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

class OSContentControllerCategories extends JControllerForm
{
	/**
	 * display the form
	 * @return void
	 */
	public function display($cachable = false, $urlparams = array())
	{
		require_once JPATH_COMPONENT . '/helpers/oscontent.php';
		OSContentHelper::addSubmenu(JRequest::getCmd('view', 'categories'));

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false));
		parent::display($cachable, $urlparams);
	}

	/**
	 * save categories
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('categories');
		if(!$model->saveOSCategories()) {
			$msg = JText::_( "ERROR_CATEGORIES" );
		} else {
			$msg = JText::_( "SUCCESS_CATEGORIES" );
		}

		$this->setMessage($msg);

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false), $msg);
	}

	public function cancel($key = null, $urlVar = null)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false));

		return true;
	}
}
