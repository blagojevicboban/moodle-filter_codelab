# CodeLab filter (filter_codelab)

[![Moodle Plugin CI](https://github.com/blagojevicboban/moodle-filter_codelab/actions/workflows/ci.yml/badge.svg)](https://github.com/blagojevicboban/moodle-filter_codelab/actions/workflows/ci.yml)
[![Moodle](https://img.shields.io/badge/Moodle-4.5%20to%205.3-orange.svg)](https://moodle.org)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](LICENSE)

This filter turns **CodeLab** program examples in Moodle texts into code editors that readers can edit and run.
The examples are inserted with [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab). The editor is the
code editor of VS Code ([Monaco](https://microsoft.github.io/monaco-editor/)).

Languages: **C**, **C++**, **Python**, **Java**, **C#**, **JavaScript** (Node.js), **Pascal** and **PHP**.

> Status: **stable** (1.5.0).

> 🌐 **Live demo:** [codelab.tsp.edu.rs](https://codelab.tsp.edu.rs) (look around as a guest, or use `teacher` / `student` with password `Demo-2026`; reset every night)

> **Important:** after installing, switch the filter on in *Site administration → Plugins → Filters → Manage filters*
> (**CodeLab → On**). Until then, readers see the examples as plain code: they cannot edit or run them.

## Features

- **Theme Toggle (🌓)**: Readers can switch between light and dark VS Code themes directly on the block.
- **Run**: the program is compiled and run on the [Jobe](https://github.com/trampgeek/jobe) server of tiny_codelab.
  The output appears under the editor. An input field holds the input of the program, prefilled with the example's
  default input.
- **Python in the browser** ([Pyodide](https://pyodide.org)):
  - `input()` asks in the console while the program runs;
  - **Step** marks the line that runs next and shows the variables, with changed values highlighted;
  - **Stop** also ends an endless loop;
  - typing help from Jedi: methods of the reader's own variables, parameters of calls and syntax errors.
- **Restore original** and **Copy**.
- **The reader's own copy**: the reader's changes are kept in their browser, so they are still there after a reload.
- **Fast pages**: an editor is built only when its example comes near the screen. Pages with dozens of examples stay quick.
- Examples without `codelab-run` are shown read-only, with syntax highlighting.
- **Class view**: a student's changed copy of an example and the result of its last run go to the server; a teacher
  clicks **Class** and sees the copies of the students as cards (name, *runs* / *error* / *not run*, the code),
  refreshed every 20 seconds, with **Show large** for the projector and **Open in my editor** to run one. Capabilities
  `filter/codelab:sharework` (students) and `filter/codelab:viewclasswork` (teachers); in separate groups a teacher
  sees only their own groups.
- **Complexity** (Python): lines run against n beside n, n log n and n², with a kept version for comparison.
- **Flowchart** (Python, when the site has the algorithm plugins, tiny_algorithm 2.13 or newer): the code of the editor
  drawn as a flowchart that runs step by step, with its variables, trace table and the code in other languages.

## Manual

The [CodeLab manual](https://github.com/blagojevicboban/moodle-tiny_codelab/blob/main/docs/MANUAL.md) covers the three plugins for teachers, students and administrators.

## Installation

1. Install **tiny_codelab** first, then **filter_codelab** and **qtype_codelab**.
2. In the settings of tiny_codelab, set the address and the API key of your Jobe server.
3. **Important: switch the filter on.** Go to *Site administration → Plugins → Filters → Manage filters* and set
   **CodeLab** to **On**. Until the filter is on, readers see the examples as plain code: they cannot edit or run them.

## Requirements

- Moodle 4.5 to 5.3
- PHP 8.2+
- [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab) 1.4.0 or newer, installed first

## The CodeLab plugins

| Plugin | What it does |
|---|---|
| [tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab) | Inserts examples in the TinyMCE editor; the core of the others |
| **filter_codelab** (this plugin) | Turns examples into code editors that readers can edit and run |
| [qtype_codelab](https://github.com/blagojevicboban/moodle-qtype_codelab) | Programming question: the student's program is graded on the teacher's tests |

## Privacy

For the class view the filter keeps the last changed copy of an example of each student, with the result of its last
run and the time (table `filter_codelab_work`); the original code removes it. The Privacy API exports and deletes these
copies. The reader's copy of an example also stays in their browser (localStorage).
Programs are run on the Jobe server of tiny_codelab. Python runs in the reader's browser; see the tiny_codelab README.

## License

GNU GPL v3 or later. © 2026 Boban Blagojević
