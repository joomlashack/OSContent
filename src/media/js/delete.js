/**
 * @package   OSContent
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2022 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSContent.  If not, see <https://www.gnu.org/licenses/>.
 */

Joomla.submitform = function(task) {
    let adminForm = document.getElementById('adminForm');
    if (document.formvalidator.isValid(adminForm)) {
        let message = null;

        switch (adminForm.delete.value) {
            case 'all':
                message = 'COM_OSCONTENT_DELETE_ALL';
                break;

            case 'content':
                message = 'COM_OSCONTENT_DELETE_CONTENT';
                break;

            default:
                alert(Joomla.JText._('COM_OSCONTENT_DELETE_FAIL'));
                return;
        }
        if (confirm(Joomla.JText._(message))) {
            adminForm.task.value = task;
            adminForm.submit();
        }
    }
}
