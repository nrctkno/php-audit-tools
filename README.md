# PHP Audit tools


This is a toolset to audit PHP projects and repositories.

## Available tools

### GIT diff renderer

Use this script to `git diff` your repositories in HTML. Then you can copy the text keeping the format.

#### Purpose:
Have you ever tried to keep the git diff's format? Of course you can use tools like Github compare, diffy.org, GitKraken or whatever, but you end up taking snapshots of the code (not cool).

If we're auditing text, then we want to keep the text, right? This script that takes advantage of the inherent power of the native git diff command and, of course, HTML.

#### Usage

1. Clone the script in your public html folder.
2. Run it:
    - From your browser: Access to `http://localhost/git_diff_renderer/git_diff_renderer.php?path=path/to/your/git/repo/folder`. Replace `localhost` by a valid host.
    - From command line: `php -f git_diff_renderer.php path=path/to/your/git/repo/folder > output.html`

You can also process a diff file (a file with the output of a previous `git diff` execution) passing the file path instead of a directory.

![screenshot](screenshot.png)


### Show composer.lock requirements

Check your composer.lock dependencies and its required versions in a tree view.

#### Usage

2. From your console, run:
- `php show_composer_lock_reqs.php`
or
- `php show_composer_lock_reqs.php /my/base/dir/` (note the slash at the end)
