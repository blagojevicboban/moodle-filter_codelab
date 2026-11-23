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
 * Strings for component 'filter_codelab', language 'ko'.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['classwork'] = '학급';
$string['classworkall'] = '전체';
$string['classworkclose'] = '닫기';
$string['classworkcount'] = '학생 사본: {$a}';
$string['classworkempty'] = '아직 이 예제를 수정한 학생이 없습니다.';
$string['classworkerror'] = '오류';
$string['classworkfilter'] = '사본 표시';
$string['classworkfull'] = '크게 보기';
$string['classworknone'] = '이 상태에 해당하는 사본이 없습니다.';
$string['classworknotrun'] = '미실행';
$string['classworkok'] = '실행됨';
$string['classworkrefresh'] = '새로고침';
$string['classworkuse'] = '내 편집기에서 열기';
$string['codelab:sharework'] = '자신의 예제 사본을 교수자에게 전송 (학급 보기)';
$string['codelab:viewclasswork'] = '학생들이 수정한 예제 사본 보기 (학급 보기)';
$string['filtername'] = 'CodeLab';
$string['keepdays'] = '사본 보관 기간 (일)';
$string['keepdays_desc'] = '학급 보기를 위해 저장된 학생의 예제 사본은 이 기간(일) 동안 변경되지 않으면 삭제됩니다(하루 한 번 예약 작업 실행). 0으로 설정하면 영구 보관됩니다.';
$string['pluginname'] = 'CodeLab';
$string['privacy:metadata'] = 'CodeLab 필터는 서버에 학급 보기용 사본(아래 항목)만 저장하며, 학생이 수정한 사본은 브라우저에도 유지됩니다.';
$string['privacy:metadata:work'] = '교수자가 수업 중 학생들의 작업을 확인할 수 있도록 각 학생이 마지막으로 수정한 예제 사본입니다.';
$string['privacy:metadata:work:code'] = '학생의 프로그램입니다.';
$string['privacy:metadata:work:contextid'] = '예제가 포함된 페이지입니다.';
$string['privacy:metadata:work:status'] = '사본의 마지막 실행이 정상 종료되었는지 여부입니다.';
$string['privacy:metadata:work:timemodified'] = '사본이 저장된 시각입니다.';
$string['privacy:metadata:work:userid'] = '학생입니다.';
$string['privacy:path'] = 'CodeLab 예제';
$string['taskcleanupwork'] = '오래된 CodeLab 예제 사본 삭제';
