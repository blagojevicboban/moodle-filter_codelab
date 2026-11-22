---
name: codelab-contributing
description: Rules and checks for contributing to the CodeLab Moodle plugins (tiny_codelab, filter_codelab, qtype_codelab). Use while writing or changing any PHP, JavaScript, Mustache, CSS or language string in these repositories, before opening a pull request, and when the user asks whether a change is ready. Covers what the Moodle Plugins reviewers flag, the language files, the checks CI runs, and what contributors must not change.
---

# Contributing to the CodeLab plugins

The three plugins are one family: `tiny_codelab` (core: Monaco editor, runners, web services), `filter_codelab`
(examples in pages) and `qtype_codelab` (graded questions). `filter_codelab` and `qtype_codelab` depend on
`tiny_codelab` and import its AMD modules (`tiny_codelab/...`). Read `CONTRIBUTING.md` of the repository first.

## Workflow

- One issue per pull request, from a branch `fix/<name>` or `feature/<name>`; put `Fixes #N` in the description.
- `main` is protected: a pull request needs a green CI and the maintainer's approval. Tags `v*` and releases are the
  maintainer's.
- Do **not** change `version.php` (`version`, `release`, `dependencies`), `CHANGELOG.md` or `description.html`. If the
  change needs a database upgrade, a new capability, cache or web service (all need a version bump), say so in the pull
  request and leave the bump to the maintainer.
- Commit messages in English: one line saying what changes for the user, more lines when the reason is not obvious.
- Never commit keys, passwords, Jobe API keys or runner secrets.

## What the Moodle Plugins reviewers flag (check while writing, not only at the end)

1. **Boilerplate header in every source file.** Every PHP, JS (`amd/src`), `.mjs` test and Mustache file starts with
   the full GPL block that contains the line `This file is part of Moodle`, then a docblock with `@copyright` and
   `@license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later` (AMD modules also `@module <component>/<name>`,
   Mustache files the banner in a `{{! }}` comment and `@template`). A docblock alone is not enough. Copy the header
   of an existing file. Quick check inside the repository (must print nothing):

   ```bash
   for f in $(git ls-files '*.php' '*.js' '*.mjs' '*.mustache' | grep -v amd/build); do
     grep -q 'This file is part of Moodle' "$f" && grep -q '@license' "$f" && grep -q '@copyright' "$f" || echo "MISSING: $f"; done
   ```

2. **No hard-coded user-facing text**, including `aria-label`, `title`, `placeholder`, alt text and error messages.
   PHP `get_string()`, AMD `core/str` `getString`/`getStrings`, Mustache `{{#str}}`. Placeholders `{$a}` or
   `{$a->name}`.
3. **Every string exists in all shipped languages**: add each key to `lang/en`, `lang/sr_lt` and `lang/sr_cr`, in
   alphabetical order, with the same keys (an English text there is acceptable if you cannot translate; say so in the
   pull request). Serbian is **ekavian**: rešenje, vrednost, posle, promenljiva, menjati, ocena (never
   rješenje, vrijednost, poslije, promjenljiva, mijenjati, ocjena). Every capability in `db/access.php` needs
   `$string['<plugin>:<capability>']`; filters also need `pluginname` besides `filtername`.
4. **No larger HTML built in JavaScript.** Panels, dialogs and layouts go into `templates/*.mustache` rendered with
   `core/templates`; small changing parts are built with `document.createElement` and `textContent`, never by putting
   strings into `innerHTML`. SVG with `createElementNS`. Every template documents its context and has an
   `Example context (json):`.
5. **No `curl_init`** in PHP: use Moodle's `\curl` or `\core\http_client`.
6. **Privacy API**: new personal data, or a new place where code or input is sent, needs
   `classes/privacy/provider.php` and its strings updated.
7. **Database**: new fields in `db/install.xml` (XMLDB editor) plus an upgrade step (the maintainer adds the savepoint
   with the version); question data also in backup, restore, import and export (`extra_question_fields` in
   `qtype_codelab/questiontype.php` covers these).
8. **No emoji** in code, strings, templates, CSS or examples. No `console.log` left in code. Capability checks and
   `require_sesskey()` for actions; `validate_context` and `require_capability` in web services.

## Checks before a pull request (the same as CI)

CI runs on Moodle 4.5, 5.0, 5.1, 5.2 and 5.3 (PHP 8.2 and 8.3, MariaDB and PostgreSQL):

```sh
moodle-plugin-ci phplint
moodle-plugin-ci phpcs --max-warnings 0
moodle-plugin-ci phpdoc --max-warnings 0
moodle-plugin-ci validate
moodle-plugin-ci mustache
moodle-plugin-ci grunt --max-lint-warnings 0
moodle-plugin-ci phpunit --fail-on-warning
```

- JavaScript: edit `amd/src`, build with `npx grunt amd` in the Moodle root and **commit `amd/build` too**. ESLint warnings
  fail CI: lines up to 132 characters, function complexity up to 20, no nested ternaries, a promise chain must end with
  `catch` or be awaited in `try`/`finally`.
- CSS: stylelint with zero warnings (`npx grunt stylelint --root=<plugin path> --max-lint-warnings=0`): short hex colours
  (`#fff`, not `#ffffff`), no `!important`.
- Node unit tests: tiny `node --import ./tests/register.mjs --test "tests/*.test.mjs"`, qtype
  `node --test tests/js/*.test.mjs`. New pure logic gets a test there; PHP logic gets a PHPUnit test in `tests/`.
- PHPUnit must work on all CI Moodle versions: an API missing in Moodle 4.5 or 5.0 needs a `method_exists` check (or the
  test is skipped there with a reason), and deprecated functions need their replacement when it exists.
- Files written by scripts on Windows must keep LF line endings.
