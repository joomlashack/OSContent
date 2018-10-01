<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * OSContent component Controller
 *
 * @since  1.0.0
 */
class OSContentController extends JControllerLegacy
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
     * @access  public
     * @return  OSContentController
     */
    public function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT . '/helpers/oscontent.php';

        // Joomla 3.x Backward Compatibility
        $view = JFactory::getApplication()->input->getCmd('view');

        OSContentHelper::addSubmenu($view);

        parent::display($cachable, $urlparams);

        return $this;
    }
}
