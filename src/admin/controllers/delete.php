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

		if (version_compare(JVERSION, '3.0', '<')) {
			$view = JRequest::getCmd('view', 'delete');
		} else {
			$view = JFactory::getApplication()->input->get('view', 'delete');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false));
		parent::display($cachable, $urlparams);
	}

	/**
	 * delete
	 */
	public function delete($key = null, $urlVar = null)
	{
		// TODO: Allow to delete multiple items

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
