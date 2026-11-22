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
 * The language files: every language has the keys of English in the same order, the same placeholders and HTML tags
 * in each string, closed [[...]] marks of code (a word in them may be translated, as [[case vrednost:]]), and no empty
 * string.
 *
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync, readdirSync} from 'node:fs';
import {basename, dirname, join} from 'node:path';
import {fileURLToPath} from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..', '..');
const component = basename(readdirSync(join(root, 'lang', 'en')).find((f) => f.endsWith('.php')), '.php');

/** The languages the plugin ships besides English. */
const LANGUAGES = ['sr_lt', 'sr_cr', 'de', 'fr', 'es', 'it', 'pt', 'pl', 'hr', 'bs', 'sl', 'mk', 'sk', 'cs'];

/**
 * The strings of a language file in their order, from $string['key'] = '...'; (single quotes, \' and \\ escaped).
 *
 * @param {string} lang
 * @returns {Array<[string, string]>}
 */
const load = (lang) => {
    const text = readFileSync(join(root, 'lang', lang, `${component}.php`), 'utf8');
    const strings = [];
    const re = /^\$string\['([^']+)'\]\s*=\s*'((?:[^'\\]|\\.)*)';/gm;
    for (let m = re.exec(text); m; m = re.exec(text)) {
        strings.push([m[1], m[2].replace(/\\(['\\])/g, '$1')]);
    }
    // Every line that starts a string must have been read, so a broken quote does not hide a key.
    assert.equal(strings.length, (text.match(/^\$string\[/gm) || []).length, `${lang}: a string could not be read`);
    return strings;
};

/**
 * What must stay the same in a translation: placeholders and HTML tags (name only), outside the [[...]] marks of code.
 *
 * @param {string} s
 * @returns {string[]}
 */
const marks = (s) => {
    // Code marks first, so a < inside them ([[#include <stdio.h>]]) is not taken for a tag.
    const text = s.replace(/\[\[.*?\]\]/g, '');
    return [
        ...(text.match(/\{\$a(?:->\w+)?\}/g) || []),
        ...(text.match(/<\/?[a-z][a-z0-9]*/gi) || []).map((t) => t.toLowerCase()),
        // A mark of code left open.
        ...(text.match(/\[\[/g) || []),
    ].sort();
};

const english = load('en');

test('English has no empty string and no key twice', () => {
    assert.equal(new Set(english.map(([k]) => k)).size, english.length);
    for (const [key, value] of english) {
        assert.ok(value.trim(), `en: ${key} is empty`);
    }
});

for (const lang of LANGUAGES) {
    test(`${lang}: the keys of English in the same order, the same marks, no empty string`, () => {
        const strings = load(lang);
        assert.deepEqual(strings.map(([k]) => k), english.map(([k]) => k), `${lang}: keys differ from en`);
        const en = new Map(english);
        for (const [key, value] of strings) {
            assert.ok(value.trim(), `${lang}: ${key} is empty`);
            assert.deepEqual(marks(value), marks(en.get(key)),
                `${lang}: ${key} has other placeholders or tags: ${JSON.stringify(marks(value))}`);
        }
    });
}
