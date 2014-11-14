<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2014 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.4
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    /**
     * Alias Class for JView in Joomla! < 3.0
     *
     * @since  1.9.1
     */
    class OSView extends JView
    {
    }
} else {
    /**
     * Alias Class for JViewLegacy in Joomla! >= 3.0
     *
     * @since  1.9.1
     */
    class OSView extends JViewLegacy
    {
        /**
         * Method to display the view
         *
         * @param   string $tpl Template file
         *
         * @access    public
         * @return  void
         */
        public function display($tpl = null)
        {
            // Joomla 3.x Backward Compatibility
            if (version_compare(JVERSION, '3.0', '>=')) {
                $this->sidebar = JHtmlSidebar::render();
            }

            parent::display($tpl);
        }
    }
}
