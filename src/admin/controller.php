<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2020 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSContent.
 *
 * OSContent is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSContent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSContent.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * OSContent component Controller
 *
 * @since  1.0.0
 */
class OSContentController extends JControllerLegacy
{
    /**
     * $default_view
     *
     * Default view for the controller
     *
     * @var     string
     * @access  protected
     */
    protected $default_view = 'content';

    /**
     * Method to display the controller's view
     *
     * @param   bool  $cachable  Cachable
     * @param   array $urlparams URL Params
     *
     * @access  public
     * @return  OSContentController
     */
    public function display($cachable = false, $urlparams = array())
    {
        // Joomla 3.x Backward Compatibility
        $view = JFactory::getApplication()->input->getCmd('view');

        parent::display($cachable, $urlparams);

        return $this;
    }
}
