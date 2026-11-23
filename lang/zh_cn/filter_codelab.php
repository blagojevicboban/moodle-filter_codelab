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
 * Strings for component 'filter_codelab', language 'zh_cn'.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['classwork'] = '班级';
$string['classworkall'] = '全部';
$string['classworkclose'] = '关闭';
$string['classworkcount'] = '学生副本：{$a}';
$string['classworkempty'] = '尚无学生修改过此示例。';
$string['classworkerror'] = '错误';
$string['classworkfilter'] = '显示副本';
$string['classworkfull'] = '放大查看';
$string['classworknone'] = '没有处于此状态的副本。';
$string['classworknotrun'] = '未运行';
$string['classworkok'] = '运行正常';
$string['classworkrefresh'] = '刷新';
$string['classworkuse'] = '在我的编辑器中打开';
$string['codelab:sharework'] = '将自己的示例副本发送给教师（班级视图）';
$string['codelab:viewclasswork'] = '查看学生修改过的示例副本（班级视图）';
$string['filtername'] = 'CodeLab';
$string['keepdays'] = '副本保留天数';
$string['keepdays_desc'] = '学生为班级视图保存的示例副本在经过指定天数未修改后将被删除（每日运行一次的计划任务）。设为 0 表示永久保留。';
$string['pluginname'] = 'CodeLab';
$string['privacy:metadata'] = 'CodeLab 过滤器仅在服务器上保留用于班级视图的副本（如下）；学生编辑的副本也会保留在浏览器中。';
$string['privacy:metadata:work'] = '每位学生最后一次修改的示例副本，便于教师实时查看全班的练习情况。';
$string['privacy:metadata:work:code'] = '学生的程序代码。';
$string['privacy:metadata:work:contextid'] = '示例所在的页面。';
$string['privacy:metadata:work:status'] = '副本最近一次运行是否正常结束。';
$string['privacy:metadata:work:timemodified'] = '副本保存时间。';
$string['privacy:metadata:work:userid'] = '学生。';
$string['privacy:path'] = 'CodeLab 示例';
$string['taskcleanupwork'] = '删除旧的 CodeLab 示例副本';
