# Changelog

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
