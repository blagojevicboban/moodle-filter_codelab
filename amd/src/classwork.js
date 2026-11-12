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
 * The class view of an example: a student's changed copy and the result of its last run are sent to the server
 * (filter_codelab_save_work); the teacher opens the Class panel and sees the copies of the students as cards (name,
 * runs or error, the code), refreshed while it is open, shows one large on the projector or opens it in their own
 * editor to run it. As the class view of the algorithm plugins.
 *
 * @module     filter_codelab/classwork
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Ajax from 'core/ajax';
import {getStrings} from 'core/str';

/** Seconds between two refreshes of an open Class panel. */
const REFRESH_SECONDS = 20;

/** Lines of the code shown on a card. */
const CARD_LINES = 12;

const KEYS = ['classwork', 'classworkclose', 'classworkcount', 'classworkempty', 'classworkerror', 'classworkfull',
    'classworknotrun', 'classworkok', 'classworkrefresh', 'classworkuse'];
let strings = null;

/**
 * Strings, loaded once.
 *
 * @returns {Promise<Object<string, string>>}
 */
export const loadClassStrings = async() => {
    if (!strings) {
        const values = await getStrings(KEYS.map((key) => ({key, component: 'filter_codelab'})));
        strings = Object.fromEntries(KEYS.map((key, i) => [key, values[i]]));
    }
    return strings;
};

/**
 * The result of the last run, read from the status line of a console: ok, error, or '' while it runs or never ran.
 *
 * @param {?HTMLElement} panel
 * @returns {string}
 */
export const statusOf = (panel) => {
    const status = panel?.querySelector('.tiny_codelab-console-status');
    if (!status || /status-running/.test(status.className)) {
        return '';
    }
    return /status-ok/.test(status.className) ? 'ok' : 'error';
};

/**
 * What a student sends: the copy after a change (with no result yet) and after each run (with its result); the
 * original code removes the copy. Sends are spaced a little, so typing does not send every key.
 *
 * @param {{contextid: number}} work
 * @param {string} workkey which example of the page
 * @param {string} original the code of the example
 * @returns {function(string, string=): void} send(code, status)
 */
export const sharer = (work, workkey, original) => {
    let timer = null;
    let last = null;
    return (code, status = '') => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const text = code === original ? '' : code;
            const key = `${status}\n${text}`;
            if (key === last) {
                return;
            }
            last = key;
            Ajax.call([{methodname: 'filter_codelab_save_work',
                args: {contextid: work.contextid, workkey, code: text, status}}])[0].catch(() => {
                last = null;
            });
        }, status ? 200 : 2500);
    };
};

/**
 * An element with a class and optional text.
 *
 * @param {string} tag
 * @param {string} className
 * @param {string} [text]
 * @returns {HTMLElement}
 */
const el = (tag, className, text) => {
    const e = document.createElement(tag);
    e.className = className;
    if (text !== undefined) {
        e.textContent = text;
    }
    return e;
};

/**
 * A small button.
 *
 * @param {string} text
 * @param {string} action
 * @returns {HTMLButtonElement}
 */
const button = (text, action) => {
    const b = el('button', 'btn btn-outline-secondary btn-sm', text);
    b.type = 'button';
    b.dataset.action = action;
    return b;
};

/**
 * One copy shown large over the page (for the projector); Escape or Close ends it.
 *
 * @param {Object} copy
 * @param {Object<string, string>} s strings
 * @param {HTMLElement} back the element that gets the focus again
 */
const showLarge = (copy, s, back) => {
    const overlay = el('div', 'filter_codelab-class-full');
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', copy.fullname);
    const head = el('div', 'filter_codelab-class-fullhead');
    const close = button(s.classworkclose, 'classfullclose');
    head.append(el('strong', '', copy.fullname), close);
    const pre = el('pre', 'filter_codelab-class-fullcode', copy.code);
    overlay.append(head, pre);
    document.body.append(overlay);
    const end = () => {
        overlay.remove();
        document.removeEventListener('keydown', onKey, true);
        back.focus();
    };
    const onKey = (e) => {
        if (e.key === 'Escape') {
            e.preventDefault();
            end();
        }
    };
    close.addEventListener('click', end);
    document.addEventListener('keydown', onKey, true);
    close.focus();
};

/**
 * The Class panel of a teacher: opened and closed by its button, put after an element.
 *
 * @param {HTMLButtonElement} toggle
 * @param {HTMLElement} after
 * @param {{contextid: number}} work
 * @param {string} workkey
 * @param {function(string): void} use opens a copy in the teacher's editor
 */
export const classPanel = async(toggle, after, work, workkey, use) => {
    const s = await loadClassStrings();
    const panel = el('div', 'filter_codelab-class');
    panel.hidden = true;
    after.after(panel);
    toggle.setAttribute('aria-expanded', 'false');
    let timer = null;
    const status = {ok: s.classworkok, error: s.classworkerror, '': s.classworknotrun};

    const draw = (copies) => {
        panel.replaceChildren();
        const head = el('div', 'filter_codelab-class-head');
        const count = el('span', 'fw-bold', s.classworkcount.replace('{$a}', String(copies.length)));
        count.setAttribute('role', 'status');
        const refresh = button(s.classworkrefresh, 'classrefresh');
        refresh.addEventListener('click', load);
        const close = button(s.classworkclose, 'classclose');
        close.addEventListener('click', () => toggle.click());
        head.append(count, refresh, close);
        panel.append(head);
        if (!copies.length) {
            panel.append(el('p', 'filter_codelab-class-empty mb-0', s.classworkempty));
            return;
        }
        const grid = el('div', 'filter_codelab-class-grid');
        copies.forEach((copy) => {
            const card = el('div', 'filter_codelab-class-card');
            card.dataset.userid = String(copy.userid);
            const top = el('div', 'filter_codelab-class-cardhead');
            const badge = el('span', `badge filter_codelab-class-${copy.status || 'notrun'}`, status[copy.status] ?? status['']);
            const time = el('span', 'filter_codelab-class-time',
                new Date(copy.timemodified * 1000).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}));
            top.append(el('strong', '', copy.fullname), badge, time);
            const lines = copy.code.split('\n');
            const code = el('pre', 'filter_codelab-class-code',
                lines.slice(0, CARD_LINES).join('\n') + (lines.length > CARD_LINES ? '\n…' : ''));
            const tools = el('div', 'filter_codelab-class-tools');
            const large = button(s.classworkfull, 'classfull');
            large.addEventListener('click', () => showLarge(copy, s, large));
            const open = button(s.classworkuse, 'classuse');
            open.addEventListener('click', () => use(copy.code));
            tools.append(large, open);
            card.append(top, code, tools);
            grid.append(card);
        });
        panel.append(grid);
    };

    const load = async() => {
        try {
            const result = await Ajax.call([{methodname: 'filter_codelab_get_class_work',
                args: {contextid: work.contextid, workkey}}])[0];
            if (!panel.hidden) {
                draw(result.copies);
            }
        } catch (e) {
            panel.replaceChildren(el('p', 'text-danger mb-0', e?.message || String(e)));
        }
    };

    toggle.addEventListener('click', () => {
        const open = panel.hidden;
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        clearInterval(timer);
        if (open) {
            load();
            timer = setInterval(load, REFRESH_SECONDS * 1000);
        } else {
            panel.replaceChildren();
        }
    });
};
