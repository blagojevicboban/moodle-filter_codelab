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
4. `main` is protected: a pull request needs a green CI; then merge it.
5. Feature and fix pull requests do not change `version.php`, `CHANGELOG.md` or `description.html`; these change in a
   release commit (see Releases below). Say in the pull request when your change needs a database upgrade or a new
   capability.

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

## Security

Do not open public issues for security problems: write to the maintainer through the contact on the GitHub profile.

## Licence

By contributing you agree that your contribution is released under the GNU GPL v3 or later, like the plugin.
