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

/**
 * Unit tests for the CodeLab filter.
 *
 * @package    filter_codelab
 * @category   test
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \filter_codelab\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Does the page load the viewer module?
     *
     * @return bool
     */
    protected function viewer_loaded(): bool {
        global $PAGE;
        return strpos($PAGE->requires->get_end_code(), 'filter_codelab/viewer') !== false;
    }

    public function test_text_without_codelab(): void {
        $this->resetAfterTest();
        $filter = new text_filter(\context_system::instance(), []);
        $text = '<p>Text</p><pre class="language-c">int x;</pre><p>codelab</p>';
        $this->assertSame($text, $filter->filter($text));
        $this->assertSame('', $filter->filter(''));
        $this->assertFalse($this->viewer_loaded());
    }

    public function test_cleaned_codelab_block(): void {
        $this->resetAfterTest();
        $filter = new text_filter(\context_system::instance(), []);
        $text = purify_html('<pre class="codelab codelab-lang-c codelab-run">int main(void) { return 0; }</pre>' .
            '<pre class="codelab-stdin">5</pre>');
        $this->assertStringContainsString('codelab-lang-c', $text);
        $this->assertStringContainsString('<pre class="codelab-stdin">5</pre>', $text);
        $this->assertSame($text, $filter->filter($text));
        $this->assertTrue($this->viewer_loaded());
    }
}
