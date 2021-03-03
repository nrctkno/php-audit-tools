# PHP Audit tools

This is a set of tools for auditing PHP projects and repositories.


## Available tools

### GIT diff renderer

This command allows you to visualize a git diff in formatted text, taking advantage of the inherent power of the native `git diff` command and HTML.

#### Usage

1. Clone the script in your public html folder.
2. Run it:
    - From your browser: Access to `http://localhost/git_diff_renderer/git_diff_renderer.php?path=path/to/your/git/repo/folder`. Replace `localhost` by a valid host.
    - From command line: `php -f git_diff_renderer.php path=path/to/your/git/repo/folder > output.html`

You can also process a diff file (a file with the output of a previous `git diff` execution) passing the file path instead of a directory.

![screenshot](doc/gdr.png)


### Show composer.lock requirements

Check your composer.lock dependencies and its required versions in a tree view.

#### Usage

2. From your console, run:

- `php show_composer_lock_reqs.php dev|prod` , use _dev_ or _prod_.

or

- `php show_composer_lock_reqs.php dev|prod /directory/of/composer_lock/` , note the slash at the end
