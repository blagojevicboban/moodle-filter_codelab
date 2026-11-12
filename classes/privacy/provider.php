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

namespace filter_codelab\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider: the copies of examples the students changed, kept for the class view of the teacher.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * The data kept.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('filter_codelab_work', [
            'contextid' => 'privacy:metadata:work:contextid',
            'userid' => 'privacy:metadata:work:userid',
            'code' => 'privacy:metadata:work:code',
            'status' => 'privacy:metadata:work:status',
            'timemodified' => 'privacy:metadata:work:timemodified',
        ], 'privacy:metadata:work');
        return $collection;
    }

    /**
     * Contexts with copies of a user.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql(
            'SELECT DISTINCT contextid FROM {filter_codelab_work} WHERE userid = :userid',
            ['userid' => $userid]
        );
        return $contextlist;
    }

    /**
     * Users with copies in a context.
     *
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        $userlist->add_from_sql(
            'userid',
            'SELECT userid FROM {filter_codelab_work} WHERE contextid = :contextid',
            ['contextid' => $userlist->get_context()->id]
        );
    }

    /**
     * Export the copies of a user.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $records = $DB->get_records(
                'filter_codelab_work',
                ['contextid' => $context->id, 'userid' => $userid],
                'timemodified ASC'
            );
            if (!$records) {
                continue;
            }
            $copies = array_map(fn($r) => (object) [
                'example' => $r->workkey,
                'code' => $r->code,
                'status' => $r->status,
                'timemodified' => transform::datetime($r->timemodified),
            ], array_values($records));
            writer::with_context($context)->export_data(
                [get_string('privacy:path', 'filter_codelab')],
                (object) ['copies' => $copies]
            );
        }
    }

    /**
     * Delete all copies in a context.
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        $DB->delete_records('filter_codelab_work', ['contextid' => $context->id]);
    }

    /**
     * Delete the copies of a user in some contexts.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $DB->delete_records('filter_codelab_work', ['contextid' => $context->id, 'userid' => $userid]);
        }
    }

    /**
     * Delete the copies of some users in a context.
     *
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $userids = $userlist->get_userids();
        if (!$userids) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['contextid'] = $userlist->get_context()->id;
        $DB->delete_records_select('filter_codelab_work', "contextid = :contextid AND userid {$insql}", $params);
    }
}
