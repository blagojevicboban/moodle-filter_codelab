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

namespace filter_codelab\task;

/**
 * Daily cleanup of the copies of the class view: copies whose page no longer exists (left when no event removed them),
 * and copies older than the setting keepdays.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_work extends \core\task\scheduled_task {
    /** Days a copy is kept when the setting was never saved. */
    const KEEPDAYS = 365;

    /**
     * Name of the task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskcleanupwork', 'filter_codelab');
    }

    /**
     * Delete the copies of pages that are gone, then the old copies.
     */
    public function execute(): void {
        global $DB;
        $DB->delete_records_select(
            'filter_codelab_work',
            'NOT EXISTS (SELECT 1 FROM {context} c WHERE c.id = {filter_codelab_work}.contextid)'
        );
        $setting = get_config('filter_codelab', 'keepdays');
        $days = $setting === false ? self::KEEPDAYS : (int) $setting;
        if ($days > 0) {
            $DB->delete_records_select('filter_codelab_work', 'timemodified < :before', ['before' => time() - $days * DAYSECS]);
        }
    }
}
