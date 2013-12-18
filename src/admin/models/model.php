<?php
/**
 * OSContent
 *
 * Joomla 1.7.x, 2.5.x and 3.x
 *
 * OSContent is an extension for creating and deleting articles and categories in bulk/mass.
 * You can even create menu items for the newly created content.
 *
 * Forked from MassContent (http://www.baticore.com/index.php?option=com_content&view=article&id=1&Itemid=14)
 * because it was only available for Joomla 1.5.
 *
 * @category       Joomla Component
 * @package        OSContent
 * @author         Johann Eriksen
 * @copyright  (C) 2007-2009 Johann Eriksen
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version        1.9.1
 * @link           http://www.ostraining.com/downloads/joomla-extensions/oscontent/
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

// Joomla 3.x Backward Compatibility
if (version_compare(JVERSION, '3.0', '<')) {
    /**
     * Alias Class for JModel in Joomla! < 3.0
     *
     * @since  1.9.1
     */
    class OSModel extends JModel
    {
    }
} else {
    /**
     * Alias Class for JModelLegacy in Joomla! >= 3.0
     *
     * @since  1.9.1
     */
    class OSModel extends JModelLegacy
    {
    }
}

