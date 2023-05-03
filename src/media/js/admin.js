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
    /**
     * @param {HTMLElement} element
     * @param {string} value
     *
     * @returns {?string}
     */
    let editorValue = (element, value) => {
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

    /**
     *
     * @param {HTMLElement} article
     *
     * @returns {
     *    {title    : HTMLElement},
     *    {alias    : HTMLElement},
     *    {introText: HTMLElement},
     *    {fullText : HTMLElement}
     * }
     */
    let getFields = (article) => {
        return {
            title    : article.querySelector('[name$="[title]"]'),
            alias    : article.querySelector('[name$="[alias]"]'),
            introText: article.querySelector('[name$="[introtext]"]'),
            fullText : article.querySelector('[name$="[fulltext]"]')
        };
    };

    let articles      = document.querySelectorAll('table.articles.table tr'),
        duplicateText = document.getElementById('duplicateText');

    if (duplicateText) {
        duplicateText.addEventListener('click', function(evt) {
            evt.preventDefault();

            let values = {};
            articles.forEach((article, index) => {
                let fields = getFields(article);

                for (let field in fields) {
                    if (fields[field]) {
                        if (index === 0) {
                            values[field] = editorValue(fields[field]);

                        } else if (values[field] || null) {
                            switch (field) {
                                case 'title':
                                case 'alias':
                                    editorValue(fields[field], values[field] + ' ' + (index + 1));
                                    break;

                                default:
                                    editorValue(fields[field], values[field]);
                                    break;
                            }
                        }
                    }
                }
            });
        });
    }

    let clearText = document.getElementById('clearText');
    if (clearText) {
        clearText.addEventListener('click', function(evt) {
            evt.preventDefault();

            articles.forEach((article, index) => {
                if (index > 0) {
                    let fields = getFields(article);
                    for (field in fields) {
                        if (fields[field]) {
                            editorValue(fields[field], '');
                        }
                    }
                }
            });
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
