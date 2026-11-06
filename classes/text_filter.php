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
 * CodeLab filter.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_codelab;

use tiny_codelab\local\runtime;

/**
 * Turns CodeLab blocks (pre elements with the codelab class) into code editors that readers can run.
 *
 * @package    filter_codelab
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    /**
     * Load the viewer module when the text contains a CodeLab block; the text itself is left unchanged.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter($text, array $options = []) {
        global $PAGE;
        // The settings are classes (codelab-lang-c, codelab-run), which Moodle's HTML cleaning keeps.
        if (!is_string($text) || !preg_match('/<pre[^>]*\bclass="[^"]*\bcodelab\b/', $text)) {
            return $text;
        }
        $PAGE->requires->js_call_amd('filter_codelab/viewer', 'init', [runtime::config()]);
        return $text;
    }
}
