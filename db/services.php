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
 * Web services of the CodeLab filter: the class view.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'filter_codelab_save_work' => [
        'classname' => \filter_codelab\external\save_work::class,
        'description' => 'Keep the student\'s copy of an example for the class view of the teacher.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'filter/codelab:sharework',
    ],
    'filter_codelab_get_class_work' => [
        'classname' => \filter_codelab\external\get_class_work::class,
        'description' => 'The copies of an example the students changed (class view).',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'filter/codelab:viewclasswork',
    ],
];
