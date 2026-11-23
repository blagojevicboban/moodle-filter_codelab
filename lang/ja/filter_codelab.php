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
 * Strings for component 'filter_codelab', language 'ja'.
 *
 * @package    filter_codelab
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['classwork'] = 'クラス';
$string['classworkall'] = 'すべて';
$string['classworkclose'] = '閉じる';
$string['classworkcount'] = '学生のコピー: {$a}';
$string['classworkempty'] = 'このサンプルを変更した学生はまだいません。';
$string['classworkerror'] = 'エラー';
$string['classworkfilter'] = 'コピーを表示';
$string['classworkfull'] = '拡大表示';
$string['classworknone'] = 'この状態のコピーはありません。';
$string['classworknotrun'] = '未実行';
$string['classworkok'] = '実行成功';
$string['classworkrefresh'] = '更新';
$string['classworkuse'] = '自分のエディタで開く';
$string['codelab:sharework'] = 'サンプルの自分のコピーを教師に送信する（クラスビュー）';
$string['codelab:viewclasswork'] = '学生が変更したサンプルのコピーを閲覧する（クラスビュー）';
$string['filtername'] = 'CodeLab';
$string['keepdays'] = 'コピーの保存期間（日数）';
$string['keepdays_desc'] = 'クラスビュー用に学生が保存したサンプルのコピーは、この日数のあいだ変更がないと削除されます（1日1回のスケジュールタスクで実行）。0を指定すると無期限に保存されます。';
$string['pluginname'] = 'CodeLab';
$string['privacy:metadata'] = 'CodeLabフィルタがサーバーに保存するのはクラスビュー用のコピーのみです（下記参照）。学生が編集したコピーはブラウザ内にも保持されます。';
$string['privacy:metadata:work'] = '授業中に教師がクラスの作業状況を確認できるよう、各学生が変更したサンプルの最新のコピーを保存します。';
$string['privacy:metadata:work:code'] = '学生のプログラム。';
$string['privacy:metadata:work:contextid'] = 'サンプルがあるページ。';
$string['privacy:metadata:work:status'] = 'コピーの最後の実行が正常に終了したかどうか。';
$string['privacy:metadata:work:timemodified'] = 'コピーが保存された日時。';
$string['privacy:metadata:work:userid'] = '学生。';
$string['privacy:path'] = 'CodeLabサンプル';
$string['taskcleanupwork'] = '古いCodeLabサンプルのコピーを削除';
