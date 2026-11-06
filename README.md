# CodeLab filter (filter_codelab)

[![Moodle Plugin CI](https://github.com/blagojevicboban/moodle-filter_codelab/actions/workflows/ci.yml/badge.svg)](https://github.com/blagojevicboban/moodle-filter_codelab/actions/workflows/ci.yml)
[![Moodle](https://img.shields.io/badge/Moodle-4.5%20to%205.3-orange.svg)](https://moodle.org)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](LICENSE)

This filter turns **CodeLab** program examples in Moodle texts into code editors that readers can edit and run.
The examples are inserted with [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab). The editor is the
code editor of VS Code ([Monaco](https://microsoft.github.io/monaco-editor/)).

Languages: **C**, **C++**, **Python**, **Java**, **C#**, **JavaScript** (Node.js), **Pascal** and **PHP**.

> Status: **alpha** (0.4.0), under development.

## Features

- **Run**: the program is compiled and run on the [Jobe](https://github.com/trampgeek/jobe) server of tiny_codelab.
  The output appears under the editor. An input field holds the input of the program, prefilled with the example's
  default input.
- **Python in the browser** ([Pyodide](https://pyodide.org)):
  - `input()` asks in the console while the program runs;
  - **Step** marks the line that runs next and shows the variables, with changed values highlighted;
  - **Stop** also ends an endless loop.
- **Restore original** and **Copy**.
- **The reader's own copy**: the reader's changes are kept in their browser, so they are still there after a reload.
- **Fast pages**: an editor is built only when its example comes near the screen. Pages with dozens of examples stay quick.
- Examples without `codelab-run` are shown read-only, with syntax highlighting.

## Requirements

- Moodle 4.5 to 5.3
- PHP 8.2+
- [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab) 0.4.0 or newer, installed first

Enable the filter in *Site administration → Plugins → Filters → Manage filters*.

## The CodeLab plugins

| Plugin | What it does |
|---|---|
| [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab) | Inserts examples in the TinyMCE editor; the core of the others |
| **filter_codelab** (this plugin) | Turns examples into code editors that readers can edit and run |
| [qtype_codelab](https://github.com/blagojevicboban/moodle-qtype_codelab) | Programming question: the student's program is graded on the teacher's tests |

## Privacy

The filter stores no personal data in Moodle. The reader's copy of an example stays in their browser (localStorage).
Programs are run on the Jobe server of tiny_codelab. Python runs in the reader's browser; see the tiny_codelab README.

## License

GNU GPL v3 or later. © 2026 Boban Blagojević
