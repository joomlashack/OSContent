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

