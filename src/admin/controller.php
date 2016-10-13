<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Extend the JController for J3.0 compatibility
 *
 */
if (version_compare(JVERSION, '3.0', '<')) {
    /**
     * Alias Class for JController in Joomla! < 3.0
     *
     * @since  1.9.1
     */
    class OSController extends JController
    {
    }
} else {
    /**
     * Alias Class for JControllerLegacy in Joomla! >= 3.0
     *
     * @since  1.9.1
     */
    class OSController extends JControllerLegacy
    {
    }
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
     * @param   bool  $cachable  Cachable
     * @param   array $urlparams URL Params
     *
     * @access    public
     * @return  OSContentController
     */
    public function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT . '/helpers/oscontent.php';

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $view = JRequest::getCmd('view', 'content');
        } else {
            $view = JFactory::getApplication()->input->getCmd('view');
        }

        OSContentHelper::addSubmenu($view);

        parent::display($cachable, $urlparams);

        return $this;
    }
}
