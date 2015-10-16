<?php
/**
 * @package   OSContent
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2015 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

abstract class OSModelAbstract extends OSModel
{
    /**
     * Get the extension id
     *
     * @param string $extension
     * @return int
     */
    protected function getExtensionId($extension, $type = 'component')
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = ' . $db->q($extension))
            ->where('type = ' . $db->q($type))
            ->limit(1);
        $db->setQuery($query);

        return (int) $db->loadResult();
    }
}
