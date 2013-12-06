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

jimport('joomla.application.component.controllerform');

class OSContentControllerDelete extends JControllerForm
{
	/**
	 * display the form
	 * @return void
	 */
	public function display($cachable = false, $urlparams = array())
	{
		require_once JPATH_COMPONENT . '/helpers/oscontent.php';
		OSContentHelper::addSubmenu(JRequest::getCmd('view', 'delete'));

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false));
		parent::display($cachable, $urlparams);
	}

	/**
	 * delete
	 */
	public function delete($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('delete');
		if(!$model->deleteOSContent()) {
			$msg = JText::_("ERROR_DELETE");
		} else {
			$msg = JText::_("SUCCESS_DELETE");
		}

		$this->setMessage($msg);

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false), $msg);
	}

	public function cancel($key = null, $urlVar = null)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false));

		return true;
	}
}
