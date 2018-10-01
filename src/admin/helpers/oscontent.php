<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
        $subMenuClass = 'JHtmlSidebar';

        $subMenuClass::addEntry(
            JText::_('COM_OSCONTENT_SUBMENU_CREATE'),
            'index.php?option=com_oscontent&view=content',
            $vName == 'content' || empty($vName)
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

        // Load CSS
        JHtml::stylesheet( Juri::base() . 'components/com_oscontent/media/css/style.css' );

        JHtml::stylesheet( Juri::base() . 'components/com_oscontent/media/css/responsive.css' );
    }
}
