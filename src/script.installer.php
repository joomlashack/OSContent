<?php
/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2011-2018 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die();

$includePath = __DIR__ . '/admin/library/Installer/include.php';
if (! file_exists($includePath)) {
    $includePath = __DIR__ . '/library/Installer/include.php';
}

require_once $includePath;

use Alledia\Installer\AbstractScript;

/**
 * OSContent Installer Script
 *
 * @since  1.0
 */
class Com_OSContentInstallerScript extends AbstractScript
{

}
