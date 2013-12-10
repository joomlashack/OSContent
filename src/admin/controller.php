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

jimport('joomla.application.component.controller');

/**
 * Extend the JController for J3.0 compatibility
 *
 */
if (version_compare(JVERSION, '3.0', '<'))
{
	/**
	 * Alias Class for JController in Joomla! < 3.0
	 *
	 * @since  1.9.1
	 */
	class OSController extends JController {}
}
else
{
	/**
	 * Alias Class for JControllerLegacy in Joomla! >= 3.0
	 *
	 * @since  1.9.1
	 */
	class OSController extends JControllerLegacy {}
}

/**
 * OSContent component Controller
 *
 * @since  1.0.0
 */
class OSContentController extends OSController
{
	/**
	 * $default_view
	 *
	 * Default view for the controller
	 *
	 * @var     string
	 * @access  protected
	 */
	protected $default_view = 'content';

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
			$view = JRequest::getCmd('view', 'content');
		}
		else
		{
			$view = JFactory::getApplication()->input->get('view');
		}

		OSContentHelper::addSubmenu($view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
?>
