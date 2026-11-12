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
 * The copies of an example the students changed, newest first, for the class view of the teacher. In a course with
 * separate groups, a teacher who cannot see all groups gets only the students of their own groups.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_class_work extends external_api {
    /** Most copies returned. */
    const MAX_COPIES = 200;

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Context of the page with the example'),
            'workkey' => new external_value(PARAM_ALPHANUMEXT, 'Which example of the page'),
        ]);
    }

    /**
     * The copies.
     *
     * @param int $contextid
     * @param string $workkey
     * @return array
     */
    public static function execute(int $contextid, string $workkey): array {
        global $DB, $USER;
        $params = self::validate_parameters(self::execute_parameters(), ['contextid' => $contextid, 'workkey' => $workkey]);
        $context = \context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('filter/codelab:viewclasswork', $context);
        $records = $DB->get_records(
            'filter_codelab_work',
            ['contextid' => $context->id, 'workkey' => substr($params['workkey'], 0, 64)],
            'timemodified DESC',
            '*',
            0,
            self::MAX_COPIES
        );
        // Separate groups: only the students of the teacher's own groups, unless the teacher sees all groups.
        $coursecontext = $context->get_course_context(false);
        $allowed = null;
        if ($coursecontext) {
            $course = get_course($coursecontext->instanceid);
            if (
                groups_get_course_groupmode($course) == SEPARATEGROUPS
                    && !has_capability('moodle/site:accessallgroups', $context)
            ) {
                $allowed = [];
                foreach (groups_get_all_groups($course->id, $USER->id) as $group) {
                    foreach (groups_get_members($group->id, 'u.id') as $member) {
                        $allowed[$member->id] = true;
                    }
                }
            }
        }
        $fields = \core_user\fields::for_name()->get_sql('', false, '', '', false)->selects;
        $users = $records ? $DB->get_records_list(
            'user',
            'id',
            array_unique(array_column($records, 'userid')),
            '',
            'id, deleted' . ($fields ? ", {$fields}" : '')
        ) : [];
        $out = [];
        foreach ($records as $r) {
            $user = $users[$r->userid] ?? null;
            if (!$user || $user->deleted || ($allowed !== null && empty($allowed[$r->userid]))) {
                continue;
            }
            $out[] = ['userid' => (int) $r->userid, 'fullname' => fullname($users[$r->userid]), 'code' => $r->code,
                'status' => (string) $r->status, 'timemodified' => (int) $r->timemodified];
        }
        return ['copies' => $out];
    }

    /**
     * What is returned.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'copies' => new external_multiple_structure(new external_single_structure([
                'userid' => new external_value(PARAM_INT, 'The student'),
                'fullname' => new external_value(PARAM_TEXT, 'Name of the student'),
                'code' => new external_value(PARAM_RAW, 'The program'),
                'status' => new external_value(PARAM_ALPHA, 'Result of the last run: ok, error, or empty'),
                'timemodified' => new external_value(PARAM_INT, 'When it was saved'),
            ])),
        ]);
    }
}
