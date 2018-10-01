<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

abstract class OSModelAbstract extends JModelLegacy
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
            ->where('type = ' . $db->q($type));
        $db->setQuery($query, 0, 1);

        return (int) $db->loadResult();
    }
}
