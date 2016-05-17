<?php
/**
 * @package   OSContent
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2016 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

            $extension = Alledia\Framework\Factory::getExtension('OSContent', 'component');
            $extension->loadLibrary();
            $this->assignRef('extension', $extension);

            parent::display($tpl);
        }
    }
}
