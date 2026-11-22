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
 * Tests of filter_codelab/classwork: the counts and the filters of the Class panel.
 *
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';

// The module imports Moodle modules, which Node cannot load. The functions tested here use none of them and nothing
// runs when the module loads, so the module is loaded without its import lines.
const source = readFileSync(new URL('../../amd/src/classwork.js', import.meta.url), 'utf8').replace(/^import .*;$/gm, '');
const {FILTERS, countByStatus, filterCopies} =
    await import('data:text/javascript;base64,' + Buffer.from(source).toString('base64'));

const copies = [
    {userid: 1, status: 'ok'},
    {userid: 2, status: 'error'},
    {userid: 3, status: ''},
    {userid: 4, status: 'error'},
    {userid: 5, status: 'weird'},
];

test('every filter has a count, an unknown status counts as not run', () => {
    assert.deepEqual(countByStatus(copies), {all: 5, ok: 1, error: 2, '': 2});
    assert.deepEqual(countByStatus([]), {all: 0, ok: 0, error: 0, '': 0});
    assert.deepEqual(Object.keys(countByStatus([])).sort(), [...FILTERS].sort());
});

test('a filter shows the copies with its status, in their order', () => {
    const ids = (filter) => filterCopies(copies, filter).map((c) => c.userid);
    assert.deepEqual(ids('all'), [1, 2, 3, 4, 5]);
    assert.deepEqual(ids('ok'), [1]);
    assert.deepEqual(ids('error'), [2, 4]);
    assert.deepEqual(ids(''), [3, 5]);
});

test('the counts match the filters', () => {
    const counts = countByStatus(copies);
    for (const filter of FILTERS) {
        assert.equal(filterCopies(copies, filter).length, counts[filter], `filter '${filter}'`);
    }
});
