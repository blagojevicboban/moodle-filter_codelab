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

namespace filter_codelab\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * The reader's own changed copies of the examples of a page, so the same copy opens on another computer or phone.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_my_work extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Context of the page with the examples'),
        ]);
    }

    /**
     * The copies.
     *
     * @param int $contextid
     * @return array
     */
    public static function execute(int $contextid): array {
        global $DB, $USER;
        $params = self::validate_parameters(self::execute_parameters(), ['contextid' => $contextid]);
        $context = \context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('filter/codelab:sharework', $context);
        $records = $DB->get_records(
            'filter_codelab_work',
            ['contextid' => $context->id, 'userid' => $USER->id],
            'timemodified DESC',
            'id, workkey, code, timemodified'
        );
        $out = [];
        foreach ($records as $r) {
            $out[] = ['workkey' => $r->workkey, 'code' => $r->code, 'timemodified' => (int) $r->timemodified];
        }
        return ['copies' => $out];
    }

    /**
     * Result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'copies' => new external_multiple_structure(new external_single_structure([
                'workkey' => new external_value(PARAM_ALPHANUMEXT, 'Which example of the page'),
                'code' => new external_value(PARAM_RAW, 'The reader\'s program'),
                'timemodified' => new external_value(PARAM_INT, 'When it was saved'),
            ])),
        ]);
    }
}
