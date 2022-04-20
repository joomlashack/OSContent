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
;
jQuery(document).ready(function($) {
    let titles     = document.getElementsByName('title[]'),
        aliases    = document.getElementsByName('alias[]'),
        introTexts = document.getElementsByName('introtext[]'),
        fullTexts  = document.getElementsByName('fulltext[]');

    let editorValue = function(element, value) {
        let editor = Joomla.editors.instances[element.id] || null;
        if (editor) {
            if (typeof value === 'undefined') {
                return editor.getValue();
            } else {
                editor.setValue(value);
            }

        } else {
            if (typeof value === 'undefined') {
                return element.value;

            } else {
                element.value = value;
            }
        }

        return null;
    };

    let duplicateText = document.getElementById('duplicateText');
    if (duplicateText) {
        duplicateText.addEventListener('click', function(evt) {
            evt.preventDefault();

            let titleText = titles[0].value,
                aliasText = aliases.length ? aliases[0].value : null,
                introText = introTexts.length ? editorValue(introTexts[0]) : null,
                fullText  = fullTexts.length ? editorValue(fullTexts[0]) : null;

            for (let index = 1; index < titles.length; index++) {
                titles[index].value = titleText + ' ' + (index + 1);
                if (aliasText) {
                    aliases[index].value = aliasText + '-' + (index + 1);
                }
                if (introText) {
                    editorValue(introTexts[index], introText);
                }
                if (fullText) {
                    editorValue(fullTexts[index], fullText);
                }
            }
        });
    }

    let clearText = document.getElementById('clearText');
    if (clearText) {
        clearText.addEventListener('click', function(evt) {
            evt.preventDefault();

            for (let index = 1; index < titles.length; index++) {
                titles[index].value = '';
                if (aliases[index]) {
                    aliases[index].value = '';
                }
                if (introTexts[index]) {
                    editorValue(introTexts[index], '');
                }
                if (fullTexts[index]) {
                    editorValue(fullTexts[index], '');
                }
            }
        });
    }

    /* Manage menutype/menuitem selections when present */
    $.fn.addChangeListener = function(callback) {
        if (this.chosen) {
            this.chosen().on('change', callback);

        } else {
            this.on('change', callback);
        }

        return this;
    };

    let menutype = document.getElementById('menutype');
    if (menutype) {
        let parentId = document.getElementById('parent_id');

        let updateParentOptions = function(menuName) {
            parentId.querySelectorAll('optgroup').forEach(function(group) {
                let currentType = group.getAttribute('label');

                group.querySelectorAll('option').forEach(function(item) {
                    item.disabled = currentType !== menuName;
                });
                parentId.value = '';

                let $parentId = $(parentId);
                if ($parentId.chosen) {
                    $parentId
                        .val('')
                        .trigger('chosen:updated')
                        .trigger('liszt:updated');
                }
            });
        };

        $(menutype).addChangeListener(function(evt) {
            let $this    = $(this),
                menuName = this.options[this.selectedIndex].text;

            updateParentOptions(menuName);
        });

        menutype.dispatchEvent(new CustomEvent('change'));
    }
});
