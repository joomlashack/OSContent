<?php
/**
* OSContent for Joomla 1.7.x, 2.5.x and 3.x
* @version 1.9.1
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Extend the JController for J3.0 compatibility
 *
 */
if (version_compare(JVERSION, '3.0', '<')) {
	class OSController extends JController { }
} else {
	class OSController extends JControllerLegacy { }
}

/**
 * OSContent component Controller
 *
 */
class OSContentController extends OSController
{
	protected $default_view = 'content';

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function display($cachable = false, $urlparams = array())
	{
		require_once JPATH_COMPONENT . '/helpers/oscontent.php';

		if (version_compare(JVERSION, '3.0', '<')) {
			$view = JRequest::getCmd('view', 'content');
		} else {
			$view = JFactory::getApplication()->input->get('view');
		}
		OSContentHelper::addSubmenu($view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
?>
