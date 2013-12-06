<?php
/*
* OSContent for Joomla 1.7.x, 2.5.x and 3.x
* @version 1.5
* @Date 04.10.2009
* @copyright (C) 2007-2009 Johann Eriksen
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Official website: http://www.baticore.com
*/

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

if (version_compare(JVERSION, '3.0', '<')) {
	class OSModel extends JModel { }
} else {
	class OSModel extends JModelLegacy { }
}

