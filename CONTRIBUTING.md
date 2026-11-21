# Contributing to filter_codelab

Thank you for helping. This plugin is part of the CodeLab family:
[tiny_codelab](https://github.com/blagojevicboban/moodle-tiny_codelab) (core: editor, runners, web services),
[filter_codelab](https://github.com/blagojevicboban/moodle-filter_codelab) (examples in pages) and
[qtype_codelab](https://github.com/blagojevicboban/moodle-qtype_codelab) (graded questions), with
[codelab-runner](https://github.com/blagojevicboban/codelab-runner) as the optional interactive server.

## Workflow

1. Take an open issue (or open one first for a larger change) and say in it that you are working on it.
2. Create a branch from `main` in this repository: `fix/<short-name>` or `feature/<short-name>`.
3. Keep a pull request to one topic. Describe what changes for teachers or students and how you tested it.
4. `main` is protected: a pull request needs a green CI and the maintainer's approval. Release tags `v*` are created by
   the maintainer only.
5. **Do not change `version.php`** (`version`, `release`, dependencies) or `CHANGES`-style release notes: the maintainer
   bumps versions when a release is made. If your change needs a database upgrade or a new capability, say so in the
   pull request; the maintainer adds the version bump with the `db/upgrade.php` step.

## Development setup

- Moodle 4.5 to 5.3 (`$plugin->supported = [405, 503]`), PHP 8.2 or 8.3, MariaDB or PostgreSQL.
- Install `tiny_codelab` first (the other two depend on it), then this plugin into `filter/codelab`.
- JavaScript is in `amd/src`; build it with `npx grunt amd` from the Moodle root and **commit `amd/build` too**.
- Running programs needs a Jobe server or a codelab-runner (settings of tiny_codelab). Python in examples runs in the
  browser and needs neither.

## Checks (the same as CI)

Run them before you push; CI runs them on five Moodle versions:

```sh
moodle-plugin-ci phplint
moodle-plugin-ci phpcs --max-warnings 0
moodle-plugin-ci phpdoc --max-warnings 0
moodle-plugin-ci validate
moodle-plugin-ci mustache
moodle-plugin-ci grunt --max-lint-warnings 0
moodle-plugin-ci phpunit --fail-on-warning
```

Locally `phpcs` also works through `local_codechecker`:
`vendor/bin/phpcs --standard=moodle --extensions=php <plugin folder>`.

## Rules the Moodle plugin reviewers check

- Every PHP and JavaScript file starts with the GPL boilerplate header, as the existing files do.
- No hard-coded user-facing text: every string goes into `lang/en/filter_codelab.php` **and** into `lang/sr_lt` and
  `lang/sr_cr` with the same keys (an English text is fine there if you cannot translate it; say so in the pull request).
- No larger HTML built in JavaScript: use Mustache templates in `templates/` and `core/templates`.
- New personal data or a new place where code or input is sent: update `classes/privacy/provider.php` and its strings.
- New database fields: `db/install.xml` (edit with the XMLDB editor), and for question data also backup and restore.
- No emoji in code, texts, templates or examples.
- Tests: PHPUnit for PHP logic in `tests/`, `node --test` for pure JavaScript modules where they exist.

## Commits

English, one line that says what changes for the user (for example `Class view: filter by status`), and more lines
when the reason is not obvious. Never commit keys, passwords, Jobe API keys or runner secrets.

## Security

Do not open public issues for security problems: write to the maintainer through the contact on the GitHub profile.

## Licence

By contributing you agree that your contribution is released under the GNU GPL v3 or later, like the plugin.
