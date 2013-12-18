<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.1
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

/**
 * Controller Content
 *
 * @since  1.0.0
 */
class OSContentControllerContent extends JControllerForm
{
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
            $view = JFactory::getApplication()->input->get('view', 'content');
        }

        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=content', false));
        parent::display($cachable, $urlparams);
    }

    /**
     * Method to Save
     *
     * @param   string $key    Key
     * @param   string $urlVar URL var
     *
     * @access    public
     * @return  void
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('content');

        if (!$model->saveOSContent()) {
            $msg = JText::_("COM_OSCONTENT_ERROR_CONTENT");
        } else {
            $msg = JText::_("COM_OSCONTENT_SUCCESS_CONTENT");
        }

        $this->setMessage($msg);

        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=content', false), $msg);
    }

    /**
     * Method to Cancel
     *
     * @param   string $key    Key
     * @param   string $urlVar URL var
     *
     * @access    public
     * @return  void
     */
    public function cancel($key = null, $urlVar = null)
    {
        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=content', false));

        return true;
    }
}
