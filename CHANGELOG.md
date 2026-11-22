# Changelog

## 1.16.0 (2026-11-23)

- Files of an example for readers: examples with attached files (tiny_codelab 1.22.0) show them in a collapsible
  Files (n) section under the input where readers can view and edit them, Reset restores them, and runs in Python in
  the browser, on the CodeLab runner (0.10.0) and on Jobe receive them in the working folder.
- Requires tiny_codelab 1.22.0.

## 1.15.0 (2026-11-22)

- The interface in 12 more languages: German, French, Spanish, Italian, Portuguese, Polish, Croatian, Bosnian,
  Slovenian, Macedonian, Slovak and Czech (15 with English and Serbian). Needs tiny_codelab 1.21.0.

## 1.14.1 (2026-11-22)

- Accessibility: the console of an example is a region that can be scrolled with the keyboard; screen readers hear
  the end of a run with the start of its output instead of every line while it is written.
- Requires tiny_codelab 1.20.0.

## 1.14.0 (2026-11-22)

- Class view: buttons above the copies filter them by status (all, works, error, not run yet), each with its count.
- A daily scheduled task deletes copies whose activity or course no longer exists, and copies not changed for longer
  than the new setting Keep copies for (days, 365 by default, 0 keeps them).
- Class view in separate groups: the teacher's own students are chosen before the limit of 200 copies is applied.
- A shared copy has the same size limit as a run in tiny_codelab.

## 1.13.0 (2026-11-22)

- The same copy on every device: a student's changed copy of an example, which already went to the server for the
  class view, now also comes back on another computer or phone (web service filter_codelab_get_my_work). The newer
  of the copy in the browser and the copy on the server opens.
- Stable keys: examples with an id (class codelab-id-..., added by the dialog of tiny_codelab 1.18) keep their copies
  when other examples are added, removed or moved on the page. A copy saved before the example had an id moves to
  the id once, in the browser and from the server.
- Copies are deleted with their activity, course or user (event observers).
- Requires tiny_codelab 1.18.0.

## 1.12.0 (2026-11-22)

- AI explanations of errors: with tiny_codelab 1.17 (required) and the setting AI explanations of errors, a button under
  a compiler message or an error in the console asks the AI of the site to explain it.

## 1.11.2 (2026-11-21)

- The language files define pluginname too, besides filtername (Moodle Plugins review, #2).

## 1.11.1 (2026-11-20)

- Dark theme: the output panel below the editor is dark too, with light text and readable colours for the status,
  errors and an empty output (before, it stayed light grey and could show white text on it).
- No emoji in the block labels and the README.

## 1.11.0 (2026-11-12)

- Flowchart: with the algorithm plugins on the site (tiny_algorithm 2.13 or newer), Python examples get a Flowchart
  button that draws the code of the editor as a flowchart with the algorithm runner (Run, Step, Back, the variables,
  the trace table, the complexity chart and the code in other languages). Lines that are not drawn as blocks are
  named. The modules of the algorithm plugins are loaded only when the button is used. Needs tiny_codelab 1.11.0.

## 1.10.1 (2026-11-12)

- The status field of the class view table has no empty default, which the database layer refuses on a new install
  (1.10.0 failed to install cleanly in CI).

## 1.10.0 (2026-11-12)

- Class view, as in the algorithm plugins: a student's changed copy of an example and the result of its last run
  (runs, error) are kept on the server (table filter_codelab_work, web service filter_codelab_save_work); the original
  code removes the copy. Teachers get a Class button: cards of the students' copies with their name, result and code,
  refreshed every 20 seconds while open, Show large for the projector, and Open in my editor to run one.
- Capabilities filter/codelab:sharework (students) and filter/codelab:viewclasswork (teachers; in separate groups only
  their own groups unless they see all groups), web service filter_codelab_get_class_work, a Privacy API provider
  (export and delete of the copies), PHPUnit tests.

## 1.8.0 (2026-11-12)

- Complexity button for Python examples that run in the browser: lines run against n beside n, n log n and n², the
  growth named, a second version kept for comparison (tiny_codelab/complexity). Needs tiny_codelab 1.8.0.
- With tiny_codelab 1.6 and 1.7 the Step panel of the viewer also has Step back, the timeline and the memory view.

## 1.5.1 (2026-11-08)

- Toolbar in one row on phones: the language, Run, Step and Stop on the left; restore original, copy, theme and full
  screen as icon buttons on the right (their names are the titles).
- Input on phones with tiny_codelab 1.5.1: a check button and the Go key of the phone keyboard send the input line.
- Needs tiny_codelab 1.5.1.

## 1.5.0 (2026-11-08)

- Support for VS Code themes: reader theme toggle button (`🌓`) on the toolbar to switch between light and dark themes on any code example, saved in `localStorage`.
- Automatic detection of site dark mode (Moove, Boost, and system `prefers-color-scheme`).
- Dedicated dark mode container and toolbar styling matching the VS Code dark theme.
- Needs tiny_codelab 1.5.0.

## 1.4.0 (2026-11-07)

- Debugger of Step, as in the algorithm plugins, for Python in the browser and C and C++ on the CodeLab runner
  0.5.0: breakpoints (a click left of the line number; Run stops there), Step over, Step out, automatic steps with a
  speed, the call stack (a frame shows its variables) and a trace table of the values after each step. Needs tiny_codelab 1.4.0.

## 1.3.0 (2026-11-06)

- SQL examples (MySQL / MariaDB) on the CodeLab runner; their setup is never shown to readers.
- Full screen button. Needs tiny_codelab 1.3.0.

## 1.2.0 (2026-11-06)

- Step for C and C++ examples when the site has a CodeLab runner: line by line with the variables. Needs
  tiny_codelab 1.2.0.

## 1.1.0 (2026-11-06)

- Interactive programs in examples: with a CodeLab runner set in tiny_codelab, the reader types the input of C, C++,
  Java and C# programs while they run, and can stop them. Needs tiny_codelab 1.1.0.

## 1.0.0 (2026-11-06)

- First stable release. Needs tiny_codelab 1.0.0.

## 0.5.0 (2026-11-06)

- Python typing help from Jedi in the examples that readers can run. Needs tiny_codelab 0.5.0.

## 0.4.0 (2026-11-06)

- JavaScript (Node.js), Pascal and PHP examples. Needs tiny_codelab 0.4.0.

## 0.3.0 (2026-11-06)

- Python examples run in the reader's browser: `input()` in the console, Step with the line marked and the variables,
  Stop.

## 0.2.0 (2026-11-06)

- Code editors for the examples, built when they come near the screen: Run on Jobe, input field with the example's
  default input, Restore original, Copy, and the reader's own copy kept in the browser.

## 0.1.0 (2026-11-05)

- First skeleton of the plugin.
