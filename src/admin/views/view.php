<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

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
        $this->sidebar = JHtmlSidebar::render();

        $extension = Alledia\Framework\Factory::getExtension('OSContent', 'component');
        $extension->loadLibrary();
        $this->assignRef('extension', $extension);

        parent::display($tpl);
    }
}
