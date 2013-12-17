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

/**
 * Controller Categories
 *
 * @since  1.0.0
 */
class OSContentControllerCategories extends JControllerForm
{
	/**
	 * Method to display the controller's view
	 *
	 * @param   bool   $cachable   Cachable
	 * @param   array  $urlparams  URL Params
	 *
	 * @access	public
	 * @return  OSContentController
	 */
	public function display($cachable = false, $urlparams = array())
	{
		require_once JPATH_COMPONENT . '/helpers/oscontent.php';

		// Joomla 3.x Backward Compatibility
		if (version_compare(JVERSION, '3.0', '<'))
		{
			$view = JRequest::getCmd('view', 'categories');
		}
		else
		{
			$view = JFactory::getApplication()->input->get('view', 'categories');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false));
		parent::display($cachable, $urlparams);
	}

	/**
	 * Method to Save
	 *
	 * @param   string  $key     Key
	 * @param   string  $urlVar  URL var
	 *
	 * @access	public
	 * @return  void
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('categories');

		if(!$model->saveOSCategories())
		{
			$msg = JText::_("COM_OSCONTENT_ERROR_CATEGORIES");
		}
		else
		{
			$msg = JText::_("COM_OSCONTENT_SUCCESS_CATEGORIES");
		}

		$this->setMessage($msg);

		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false), $msg);
	}

	/**
	 * Method to Cancel
	 *
	 * @param   string  $key     Key
	 * @param   string  $urlVar  URL var
	 *
	 * @access	public
	 * @return  void
	 */
	public function cancel($key = null, $urlVar = null)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=categories', false));

		return true;
	}
}
