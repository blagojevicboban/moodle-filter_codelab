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

use filter_codelab\external\get_my_work;
use filter_codelab\external\save_work;

/**
 * Unit tests of the reader's own copies on the server (another computer opens the same copy) and of the clean-up
 * when an activity or a user is deleted.
 *
 * @package    filter_codelab
 * @category   test
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \filter_codelab\external\get_my_work
 * @covers     \filter_codelab\observer
 */
final class my_work_test extends \advanced_testcase {
    public function test_own_copies_only(): void {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $page = $gen->create_module('page', ['course' => $course->id]);
        $context = \context_module::instance($page->cmid);
        $s1 = $gen->create_and_enrol($course, 'student');
        $s2 = $gen->create_and_enrol($course, 'student');
        $this->setUser($s1);
        save_work::execute($context->id, 'id_abcd1234_xyz', 'print(1)');
        save_work::execute($context->id, 'w0_xyz', 'print(2)');
        $this->setUser($s2);
        save_work::execute($context->id, 'id_abcd1234_xyz', 'print(3)');
        $this->setUser($s1);
        $copies = get_my_work::execute($context->id)['copies'];
        $this->assertCount(2, $copies);
        $codes = array_column($copies, 'code', 'workkey');
        $this->assertSame('print(1)', $codes['id_abcd1234_xyz']);
        $this->assertSame('print(2)', $codes['w0_xyz']);
        // A teacher has no sharework capability, so no copies of their own.
        $teacher = $gen->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);
        $this->expectException(\required_capability_exception::class);
        get_my_work::execute($context->id);
    }

    public function test_deleted_activity_and_user_take_their_copies(): void {
        global $DB;
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $page1 = $gen->create_module('page', ['course' => $course->id]);
        $page2 = $gen->create_module('page', ['course' => $course->id]);
        $s1 = $gen->create_and_enrol($course, 'student');
        $s2 = $gen->create_and_enrol($course, 'student');
        foreach ([$s1, $s2] as $student) {
            $this->setUser($student);
            foreach ([$page1, $page2] as $page) {
                save_work::execute(\context_module::instance($page->cmid)->id, 'w0_a', 'x = 1');
            }
        }
        $this->setAdminUser();
        $this->assertSame(4, $DB->count_records('filter_codelab_work'));
        if (method_exists(\core_courseformat\local\cmactions::class, 'delete')) {
            \core_courseformat\formatactions::cm($course->id)->delete($page1->cmid);
        } else {
            course_delete_module($page1->cmid);
        }
        $this->assertSame(2, $DB->count_records('filter_codelab_work'));
        delete_user($s1);
        $this->assertSame(1, $DB->count_records('filter_codelab_work'));
        delete_course($course, false);
        $this->assertSame(0, $DB->count_records('filter_codelab_work'));
    }
}
