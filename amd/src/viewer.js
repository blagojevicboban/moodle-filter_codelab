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
 * Turns CodeLab blocks into code editors. Blocks marked runnable get Run, Restore original, an input field and an
 * output panel; the reader's changes are kept in the browser (localStorage). Other blocks are shown read-only.
 *
 * @module     filter_codelab/viewer
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {languages, setupLanguages, defaultTheme} from 'tiny_codelab/common';
import {readBlock, stdinOf, setupOf} from 'tiny_codelab/embed';
import {createEditor, resolveTheme} from 'tiny_codelab/monaco';
import {run} from 'tiny_codelab/runner';
import {showRunning, showResult, showError} from 'tiny_codelab/console';
import {available as pythonAvailable} from 'tiny_codelab/python';
import {PythonSession} from 'tiny_codelab/session';
import {fullscreenButton} from 'tiny_codelab/fullscreen';
import {setIcon} from 'tiny_codelab/icons';
import {RemoteSession, runsInteractively, stepsInteractively} from 'tiny_codelab/remote';
import {openComplexity} from 'tiny_codelab/complexity';
import {classPanel, loadClassStrings, sharer, statusOf} from 'filter_codelab/classwork';
import {getStrings} from 'core/str';
import {eventTypes} from 'core_filters/events';

const SELECTOR = 'pre.codelab';
// Editors are built when they come near the screen, so a page with many examples opens quickly.
const MARGIN = '300px';
// Code that reads input: the input field is open from the start.
const READS_INPUT = /\bscanf\s*\(|\bgetchar\s*\(|\bfgets\s*\(|\bcin\s*>>|\binput\s*\(|ReadLine\s*\(|\bScanner\b/;
const STORE = 'filter_codelab';

const seen = new WeakSet();
let initialised = false;
let observer = null;
let strings = null;
let counter = 0;
// Settings of the in-browser runtimes (tiny_codelab\local\runtime::config()).
let config = {pyodide: '', interactive: []};
// The class view: the context of the page, whether the user sends their copies and whether they see the class.
let work = {contextid: 0, share: false, view: false};

/**
 * Strings of the viewer, loaded once.
 *
 * @returns {Promise<Object<string, string>>}
 */
const loadStrings = async() => {
    if (!strings) {
        const keys = ['run', 'reset', 'copy', 'copied', 'input', 'output', 'editedcopy', 'step', 'stop', 'fullscreen',
            'exitfullscreen', 'toggletheme', 'complexity']
            .concat(Object.keys(languages).map((lang) => `lang_${lang}`));
        const values = await getStrings(keys.map((key) => ({key, component: 'tiny_codelab'})));
        strings = Object.fromEntries(keys.map((key, i) => [key, values[i]]));
    }
    return strings;
};

/**
 * A short hash of the original code, so a saved copy is dropped when the teacher changes the example.
 *
 * @param {string} text
 * @returns {string}
 */
const hash = (text) => {
    let h = 5381;
    for (let i = 0; i < text.length; i++) {
        h = ((h * 33) ^ text.charCodeAt(i)) % 4294967296; // eslint-disable-line no-bitwise
    }
    return h.toString(36);
};

/**
 * Storage of the reader's copy; localStorage may be unavailable (private mode), then nothing is kept.
 *
 * @param {string} key
 * @returns {{get: function(): ?string, set: function(string): void, remove: function(): void}}
 */
const storage = (key) => ({
    get: () => {
        try {
            return window.localStorage.getItem(key);
        } catch (e) {
            return null;
        }
    },
    set: (value) => {
        try {
            window.localStorage.setItem(key, value);
        } catch (e) {
            // Full or not allowed: the copy is simply not kept.
        }
    },
    remove: () => {
        try {
            window.localStorage.removeItem(key);
        } catch (e) {
            // Not allowed.
        }
    },
});

/**
 * A button of the toolbar.
 *
 * @param {string} text
 * @param {string} action
 * @param {string} style btn-primary or btn-secondary
 * @returns {HTMLButtonElement}
 */
const button = (text, action, style = 'btn-secondary') => {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = `btn ${style} btn-sm`;
    b.dataset.action = action;
    b.textContent = text;
    return b;
};

/**
 * How an example runs: Python in the browser, or on the interactive server (C and C++ also step by step), or on Jobe.
 *
 * @param {Object} block
 * @returns {{browser: boolean, remote: boolean, step: boolean}}
 */
const runModes = (block) => {
    // Python runs in the browser when it can: input() asks while the program runs, Step goes line by line.
    const browser = block.run && block.lang === 'python' && config.pyodide !== '' && pythonAvailable();
    // C, C++, Java and C# run on the interactive server when the site has one: the input is typed while they run.
    const remote = block.run && runsInteractively(config.interactive, block.lang);
    return {browser, remote, step: browser || (remote && stepsInteractively(config.interactive, block.lang))};
};

/**
 * The session of an example that does not run on Jobe, with its buttons wired: Step, Stop and their states.
 *
 * @param {Object} modes of runModes()
 * @param {Object} options
 * @param {Object} options.block
 * @param {import('tiny_codelab/monaco').CodeEditor} options.editor
 * @param {HTMLElement} options.panel
 * @param {HTMLElement} options.vars
 * @param {HTMLTextAreaElement} options.stdin
 * @param {Object<string, HTMLButtonElement|null>} options.buttons
 * @returns {PythonSession|RemoteSession|null}
 */
const sessionFor = (modes, {block, editor, panel, vars, stdin, buttons}) => {
    if (!modes.browser && !modes.remote) {
        return null;
    }
    const {runButton, stepButton, stopButton, resetButton} = buttons;
    const onState = (state) => {
        runButton.disabled = state === 'running';
        if (stepButton) {
            stepButton.disabled = state === 'running';
        }
        stopButton.hidden = state === 'idle';
        resetButton.disabled = state !== 'idle';
    };
    const session = modes.browser
        ? new PythonSession({base: config.pyodide, editor, panel, vars, onState})
        : new RemoteSession({lang: block.lang, program: () => editor.getValue(), panel, onState, editor, vars,
            setup: () => block.setup});
    stepButton?.addEventListener('click', () => {
        if (session.state === 'paused') {
            session.step();
        } else if (session.state === 'idle') {
            session.start(true, stdin.value);
        }
    });
    stopButton.addEventListener('click', () => session.stop());
    return session;
};

/**
 * The complexity panel under the variables, opened and closed by its button.
 *
 * @param {?HTMLButtonElement} toggle none when the example has no complexity (not Python in the browser)
 * @param {HTMLElement} after the panel is put after this element
 * @param {Object} editor
 */
const complexityPanel = (toggle, after, editor) => {
    if (!toggle) {
        return;
    }
    const panel = document.createElement('div');
    panel.className = 'tiny_codelab-cx';
    panel.hidden = true;
    after.after(panel);
    toggle.setAttribute('aria-expanded', 'false');
    toggle.addEventListener('click', () => {
        if (!panel.hidden) {
            panel.replaceChildren();
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            return;
        }
        toggle.setAttribute('aria-expanded', 'true');
        openComplexity(panel, {base: config.pyodide, code: () => editor.getValue(),
            onClose: () => toggle.setAttribute('aria-expanded', 'false')});
    });
};

/**
 * The class view of an example: a student sends the changed copy and the result of each run; a teacher gets the Class
 * button and panel (filter_codelab/classwork).
 *
 * @param {Object} block
 * @param {number} index place of the example on the page
 * @param {Object} parts
 * @param {Object} parts.editor
 * @param {?HTMLElement} parts.panel console
 * @param {HTMLElement} parts.toolbar
 * @param {HTMLElement} parts.before the toolbar element the Class button goes before
 * @param {HTMLElement} parts.host the editor's element; the panel goes after it
 */
const classWork = async(block, index, {editor, panel, toolbar, before, host}) => {
    if (!block.run || !work.contextid || (!work.share && !work.view)) {
        return;
    }
    const workkey = `w${index}_${hash(block.code)}`;
    if (work.share) {
        const send = sharer(work, workkey, block.code);
        editor.onChange((value) => send(value));
        // The result of each run, when the console shows its end.
        new MutationObserver(() => {
            const status = statusOf(panel);
            if (status) {
                send(editor.getValue(), status);
            }
        }).observe(panel, {childList: true, subtree: true, attributes: true, attributeFilter: ['class']});
    }
    if (work.view) {
        const cs = await loadClassStrings();
        const toggle = button(cs.classwork, 'classwork');
        toolbar.insertBefore(toggle, before);
        await classPanel(toggle, host, work, workkey, (code) => editor.setValue(code));
    }
};

/**
 * The buttons of a viewer's toolbar: Run and Restore original for examples that run, Step where it runs step by step,
 * Stop for Python in the browser and the interactive server, and Copy. Restore original and Copy show icons.
 *
 * @param {Object} s strings
 * @param {boolean} runs
 * @param {Object} modes of runModes()
 * @returns {Object<string, HTMLButtonElement|null>}
 */
const toolbarButtons = (s, runs, modes) => {
    const stopButton = modes.browser || modes.remote ? button(s.stop, 'stop', 'btn-danger') : null;
    if (stopButton) {
        stopButton.hidden = true;
    }
    return {
        runButton: runs ? button(s.run, 'run', 'btn-primary') : null,
        stepButton: modes.step ? button(s.step, 'step') : null,
        // Lines run against n (tiny_codelab/complexity): Python in the browser counts them.
        complexityButton: modes.browser ? button(s.complexity, 'complexity') : null,
        stopButton,
        resetButton: runs ? setIcon(button('', 'reset'), 'reset', s.reset) : null,
        copyButton: setIcon(button('', 'copy'), 'copy', s.copy),
    };
};

/**
 * Build the viewer of one block in place of the pre element.
 *
 * @param {HTMLElement} pre
 * @param {number} index position of the block on the page
 */
const build = async(pre, index) => {
    const s = await loadStrings();
    const block = readBlock(pre);
    const id = `filter_codelab_${++counter}`;
    const langName = s[`lang_${block.lang}`];
    const store = storage(`${STORE}:${window.location.pathname}${window.location.search}:${index}:${hash(block.code)}`);
    const saved = block.run ? store.get() : null;

    const userTheme = storage('codelab_theme').get();
    let initialTheme = 'vs';
    if (block.theme && block.theme !== defaultTheme) {
        initialTheme = resolveTheme(block.theme);
    } else if (userTheme && (userTheme === 'vs' || userTheme === 'vs-dark' || userTheme === 'hc-black')) {
        initialTheme = userTheme;
    } else {
        initialTheme = resolveTheme(config.theme || 'auto');
    }

    const viewer = document.createElement('div');
    viewer.className = 'filter_codelab-viewer';
    if (initialTheme === 'vs-dark' || initialTheme === 'hc-black') {
        viewer.classList.add('filter_codelab-dark');
    }
    viewer.setAttribute('role', 'group');
    viewer.setAttribute('aria-label', langName);

    const toolbar = document.createElement('div');
    toolbar.className = 'filter_codelab-toolbar';
    const badge = document.createElement('span');
    badge.className = 'filter_codelab-lang';
    badge.textContent = langName;
    toolbar.append(badge);
    const modes = runModes(block);
    const buttons = toolbarButtons(s, block.run, modes);
    const {runButton, stepButton, complexityButton, stopButton, resetButton, copyButton} = buttons;
    // Run, Step and Stop on the left; the tools (restore, copy, theme, full screen) as icons on the right, so the bar
    // fits one row on a phone.
    const spacer = document.createElement('span');
    spacer.className = 'filter_codelab-spacer';
    [runButton, stepButton, complexityButton, stopButton, spacer, resetButton, copyButton].filter(Boolean)
        .forEach((b) => toolbar.append(b));
    const note = document.createElement('span');
    note.className = 'filter_codelab-note';
    note.textContent = s.editedcopy;
    note.hidden = saved === null;
    toolbar.append(note);

    const host = document.createElement('div');
    host.className = 'filter_codelab-editor';
    viewer.append(toolbar, host);
    const vars = document.createElement('div');
    vars.className = 'tiny_codelab-vars';
    vars.hidden = true;
    if (modes.step) {
        viewer.append(vars);
    }

    let stdin = null;
    let panel = null;
    if (block.run) {
        const details = document.createElement('details');
        details.className = 'filter_codelab-stdin';
        details.open = block.stdin !== '' || READS_INPUT.test(block.code);
        details.hidden = setupLanguages.includes(block.lang);
        const summary = document.createElement('summary');
        summary.textContent = s.input;
        stdin = document.createElement('textarea');
        stdin.id = `${id}_stdin`;
        stdin.className = 'form-control font-monospace';
        stdin.rows = 3;
        stdin.spellcheck = false;
        stdin.setAttribute('aria-label', s.input);
        stdin.value = block.stdin;
        details.append(summary, stdin);
        panel = document.createElement('div');
        panel.className = 'tiny_codelab-console filter_codelab-console';
        panel.setAttribute('role', 'status');
        panel.setAttribute('aria-label', s.output);
        panel.hidden = true;
        viewer.append(details, panel);
    }

    pre.hidden = true;
    if (stdinOf(pre)) {
        stdinOf(pre).hidden = true;
    }
    if (setupOf(pre)) {
        // The tables and data of SQL are never shown to readers.
        setupOf(pre).hidden = true;
    }
    pre.after(viewer);

    let editor = null;
    let session = null;
    const doRun = async() => {
        if (!editor || runButton.disabled) {
            return;
        }
        if (session) {
            if (session.state === 'paused') {
                session.resume();
            } else if (session.state === 'idle') {
                await session.start(false, stdin.value);
            }
            return;
        }
        runButton.disabled = true;
        await showRunning(panel);
        try {
            await showResult(panel, await run(block.lang, editor.getValue(), stdin.value));
        } catch (e) {
            await showError(panel, e);
        } finally {
            runButton.disabled = false;
        }
    };
    editor = await createEditor(host, {
        value: saved === null ? block.code : saved,
        lang: block.lang,
        theme: initialTheme,
        readOnly: !block.run,
        noassist: block.noassist,
        minLines: 3,
        maxLines: 30,
        label: langName,
        onRun: block.run ? doRun : null,
        pyodide: block.run ? config.pyodide : '',
        breakpoints: modes.step,
    });

    session = sessionFor(modes, {block, editor, panel, vars, stdin, buttons});
    complexityPanel(complexityButton, vars, editor);
    classWork(block, index, {editor, panel, toolbar, before: spacer, host});
    const themeButton = setIcon(button('', 'theme'), 'theme', s.toggletheme);
    let currentTheme = initialTheme;
    themeButton.addEventListener('click', () => {
        currentTheme = currentTheme === 'vs-dark' ? 'vs' : 'vs-dark';
        editor.setTheme(currentTheme);
        if (currentTheme === 'vs-dark' || currentTheme === 'hc-black') {
            viewer.classList.add('filter_codelab-dark');
        } else {
            viewer.classList.remove('filter_codelab-dark');
        }
        storage('codelab_theme').set(currentTheme);
    });
    toolbar.insertBefore(themeButton, note);
    toolbar.insertBefore(fullscreenButton({root: viewer, editor, label: s.fullscreen, exitLabel: s.exitfullscreen}), note);
    if (block.run) {
        runButton.addEventListener('click', doRun);
        resetButton.addEventListener('click', () => {
            editor.setValue(block.code);
            stdin.value = block.stdin;
            store.remove();
            note.hidden = true;
        });
        let timer = null;
        editor.onChange((value) => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                if (value === block.code) {
                    store.remove();
                    note.hidden = true;
                } else {
                    store.set(value);
                    note.hidden = false;
                }
            }, 500);
        });
    }
    copyButton.addEventListener('click', async() => {
        try {
            await navigator.clipboard.writeText(editor.getValue());
            setIcon(copyButton, 'copied', s.copied);
            setTimeout(() => setIcon(copyButton, 'copy', s.copy), 1500);
        } catch (e) {
            // No clipboard access (insecure page or denied): the reader can still select the code.
        }
    });
};

/**
 * Build the viewer of a block once it comes near the screen.
 *
 * @param {IntersectionObserverEntry[]} entries
 */
const onVisible = (entries) => {
    entries.filter((e) => e.isIntersecting).forEach((e) => {
        observer.unobserve(e.target);
        const all = [...document.querySelectorAll(SELECTOR)];
        build(e.target, all.indexOf(e.target)).catch(() => {
            // The editor could not be loaded: show the code as it was.
            e.target.hidden = false;
            if (stdinOf(e.target)) {
                stdinOf(e.target).hidden = false;
            }
        });
    });
};

/**
 * Find the blocks inside a part of the page.
 *
 * @param {Element|Document} root
 */
const prepare = (root) => {
    root.querySelectorAll(SELECTOR).forEach((pre) => {
        if (seen.has(pre) || pre.closest('.filter_codelab-viewer, [contenteditable="true"]')) {
            return;
        }
        seen.add(pre);
        observer.observe(pre);
    });
};

/**
 * Turn the CodeLab blocks of the page into editors.
 *
 * @param {Object} [settings] settings of the in-browser runtimes: {pyodide: address of Pyodide, or ''}
 * @param {?{contextid: number, share: boolean, view: boolean}} [classwork] the class view: the page's context, whether the
 *     user sends their copies (students) and whether they see the class (teachers)
 */
export const init = (settings, classwork = null) => {
    if (initialised) {
        return;
    }
    initialised = true;
    config = Object.assign(config, settings || {});
    work = Object.assign(work, classwork || {});
    observer = new IntersectionObserver(onVisible, {rootMargin: MARGIN});
    prepare(document);
    // Content added later (for example a forum post or a question in a modal) is filtered again.
    document.addEventListener(eventTypes.filterContentUpdated, (e) => {
        (e.detail.nodes || []).forEach((node) => node.querySelectorAll && prepare(node));
    });
};
