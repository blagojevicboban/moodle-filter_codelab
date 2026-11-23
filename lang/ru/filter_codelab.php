<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'filter_codelab', language 'ru'.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['classwork'] = 'Класс';
$string['classworkall'] = 'Все';
$string['classworkclose'] = 'Закрыть';
$string['classworkcount'] = 'Копий учеников: {$a}';
$string['classworkempty'] = 'Ни один ученик ещё не изменял этот пример.';
$string['classworkerror'] = 'ошибка';
$string['classworkfilter'] = 'Показать копии';
$string['classworkfull'] = 'Развернуть';
$string['classworknone'] = 'Нет копий с таким статусом.';
$string['classworknotrun'] = 'не запускалось';
$string['classworkok'] = 'работает';
$string['classworkrefresh'] = 'Обновить';
$string['classworkuse'] = 'Открыть в моём редакторе';
$string['codelab:sharework'] = 'Отправлять свою копию примера преподавателю (просмотр класса)';
$string['codelab:viewclasswork'] = 'Просматривать изменённые учениками копии примера (просмотр класса)';
$string['filtername'] = 'CodeLab';
$string['keepdays'] = 'Хранить копии (дней)';
$string['keepdays_desc'] = 'Копии примеров, сохранённые учениками для просмотра класса, удаляются, если не изменялись указанное число дней (запланированная задача, раз в сутки). Значение 0 хранит их бессрочно.';
$string['pluginname'] = 'CodeLab';
$string['privacy:metadata'] = 'Фильтр CodeLab хранит на сервере только копии для просмотра класса (ниже); отредактированная копия ученика также сохраняется в браузере.';
$string['privacy:metadata:work'] = 'Последняя копия примера, изменённая каждым учеником, чтобы преподаватель мог видеть работу класса в реальном времени.';
$string['privacy:metadata:work:code'] = 'Программа ученика.';
$string['privacy:metadata:work:contextid'] = 'Страница с примером.';
$string['privacy:metadata:work:status'] = 'Завершился ли последний запуск копии успешно.';
$string['privacy:metadata:work:timemodified'] = 'Время сохранения копии.';
$string['privacy:metadata:work:userid'] = 'Ученик.';
$string['privacy:path'] = 'Примеры CodeLab';
$string['taskcleanupwork'] = 'Удаление старых копий примеров CodeLab';
