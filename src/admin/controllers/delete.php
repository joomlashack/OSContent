<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

/**
 * Controller Delete
 *
 * @since  1.0.0
 */
class OSContentControllerDelete extends JControllerForm
{
    /**
     * Method to display the controller's view
     *
     * @param   bool  $cachable  Cachable
     * @param   array $urlparams URL Params
     *
     * @return  OSContentControllerDelete
     */
    public function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT . '/helpers/oscontent.php';

        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false));
        parent::display($cachable, $urlparams);

        return $this;
    }

    /**
     * Method to delete
     *
     * @param   string $key    Key
     * @param   string $urlVar URL var
     *
     * @access    public
     * @return  void
     */
    public function delete($key = null, $urlVar = null)
    {
        // TODO: Allow to delete multiple items

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('delete');

        if (!$model->deleteOSContent()) {
            $msg = JText::_("COM_OSCONTENT_ERROR_DELETE");
        } else {
            $msg = JText::_("COM_OSCONTENT_SUCCESS_DELETE");
        }

        $this->setMessage($msg);

        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false), $msg);
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
        $this->setRedirect(JRoute::_('index.php?option=com_oscontent&view=delete', false));

        return true;
    }
}
