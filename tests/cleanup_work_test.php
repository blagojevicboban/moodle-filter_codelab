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

namespace filter_codelab;

use filter_codelab\task\cleanup_work;

/**
 * Unit tests of the daily cleanup of the class view copies.
 *
 * @package    filter_codelab
 * @category   test
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \filter_codelab\task\cleanup_work
 */
final class cleanup_work_test extends \advanced_testcase {
    /**
     * A saved copy.
     *
     * @param int $contextid
     * @param int $userid
     * @param int $days how many days ago it was saved
     * @param string $workkey which example of the page
     * @return int id of the copy
     */
    protected function copy(int $contextid, int $userid, int $days, string $workkey = 'wabc'): int {
        global $DB;
        return $DB->insert_record('filter_codelab_work', (object) ['contextid' => $contextid, 'workkey' => $workkey,
            'userid' => $userid, 'code' => 'print(1)', 'status' => '', 'timemodified' => time() - $days * DAYSECS]);
    }

    public function test_copies_of_pages_that_are_gone_and_old_copies(): void {
        global $DB;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $context = \context_module::instance($gen->create_module('page', ['course' => $course->id])->cmid);
        $student = $gen->create_and_enrol($course, 'student');
        $new = $this->copy($context->id, $student->id, 10);
        $this->copy($context->id, $student->id, cleanup_work::KEEPDAYS + 1, 'wold');
        // A copy left by a page that no longer exists (no event removed it).
        $this->copy($context->id + 100000, $student->id, 1);

        // Without the setting: the default number of days.
        (new cleanup_work())->execute();
        $this->assertEqualsCanonicalizing([$new], array_keys($DB->get_records('filter_codelab_work')));

        // A shorter setting removes newer copies too.
        set_config('keepdays', 5, 'filter_codelab');
        (new cleanup_work())->execute();
        $this->assertSame(0, $DB->count_records('filter_codelab_work'));
    }

    public function test_zero_keeps_copies_for_good(): void {
        global $DB;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $context = \context_module::instance($gen->create_module('page', ['course' => $course->id])->cmid);
        $student = $gen->create_and_enrol($course, 'student');
        $this->copy($context->id, $student->id, 5000);
        set_config('keepdays', 0, 'filter_codelab');
        (new cleanup_work())->execute();
        $this->assertSame(1, $DB->count_records('filter_codelab_work'));
    }
}
