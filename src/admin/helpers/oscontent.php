<?php
/**
 * @category  Joomla Component
 * @package   com_oscontent
 * @author    Johann Eriksen
 * @copyright 2007-2009 Johann Eriksen
 * @copyright 2011, 2014 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.9.1
 * @link      http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

/**
 * Content Helper
 *
 * @since  1.0.0
 */
class OSContentHelper
{
    /**
     * Link the content to the menu
     *
     * @param   string $vName View name
     *
     * @return void
     */
    public static function addSubmenu($vName)
    {
        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $subMenuClass = 'JSubMenuHelper';
        } else {
            $subMenuClass = 'JHtmlSidebar';
        }

        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_CREATE'),
            'index.php?option=com_oscontent&view=content',
            $vName == 'content'
        );

        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_CATEGORIES'),
            'index.php?option=com_oscontent&view=categories',
            $vName == 'categories'
        );
        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_DELETE'),
            'index.php?option=com_oscontent&view=delete',
            $vName == 'delete'
        );
    }
}
