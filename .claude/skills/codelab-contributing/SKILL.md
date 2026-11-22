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
- `main` is protected: a pull request needs a green CI; merge it yourself then.
- In a feature or fix pull request do not change `version.php`, `CHANGELOG.md` or `description.html`; they change in a
  release commit (see Releases). Say in the pull request when the change needs an upgrade step, a new capability, cache
  or web service (all need a version bump in the next release).
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

## Releases

Releases are made by the release manager (the maintainer, or a collaborator he named), after the pull requests are
merged and CI of `main` is green. Order when several plugins change: tiny first, then filter, then qtype (dependencies
first).

1. **Version** in `version.php`: `version` = date and two digits, higher than the last one (`YYYYMMDDXX`), `release`
   (`1.19.1`: patch for fixes, minor for features), `maturity` stays `MATURITY_STABLE`. When the plugin needs the new
   version of tiny, raise `dependencies` to tiny's new `version` in the same release.
2. **CHANGELOG.md**: a new section on top, `## <release> (<date>)`, what changes for teachers and students.
3. **README.md**: describe new features; requirements with the version of tiny the plugin needs.
4. **description.html** (the Marketplace listing): `What's New in v<release>` lists the changes since the version that
   is on the Marketplace now (one line per earlier release, `From x.y.z: ...`); the Marketplace sanitizer keeps only
   `p`, `div`, `a`, `img`, `ul`, `ol`, `li`, `strong`, `em`, `code`, `br`, `hr` (titles as `<p><strong>...</strong></p>`)
   and the field allows **at most 7000 characters**: shorten older items first.
5. Commit `Release <component> <release>`, push, wait for CI of `main`, then the tag: `git tag v<release>` and
   `git push origin v<release>`. A pushed tag is never moved: a fix is a new patch release.
6. **ZIP** for the Marketplace: `git archive --format=zip --prefix=<plugin folder>/ -o <component>-<release>.zip v<release>`
   (`codelab/` for the CodeLab plugins, `algorithm/` for the Algorithm plugins). `.claude/` is left out by
   `.gitattributes`.
7. **Release notes** for the upload form, plain sentences and `-` bullets only: no quotes, no parentheses, no `<`, `>`,
   `$`, `{}` and no code-like names (the upload page is behind Cloudflare and blocks such text).
8. Send the ZIPs and the notes to the maintainer, who uploads them to the Moodle Plugins directory with his account.
9. **Demo sites** (codelab, algorithm and aisim .tsp.edu.rs) take the plugins from `main` when their demo repository
   gets a push; the maintainer (or whoever has write on the demo repositories) refreshes them after a release.
