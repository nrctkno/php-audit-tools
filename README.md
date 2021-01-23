# git diff renderer

Use this script to `git diff` your repositories in HTML. Then you can copy the text keeping the format.


## Why

I had to audit some projects at work, checking the diff between the deployed versions and the codebase in GIT. After that, had to write a report for every project. 

Have you ever tried to keep the git diff's format? Of course you can use tools like Github compare, diffy.org, gitKraken or whatever, but you end up taking snapshots of the code (not cool).

If we're auditing text, then we want to keep the text, right?

That's why I wrote this simple script that takes advantage of the inherent power of the native git diff command and, of course, HTML.


## Usage

- Clone the script in your public html folder.
- Access to `http://[your_localhost_domain_or_ip]/git_diff_renderer.php?path=path/to/your/git/repo/folder` from your browser.
- That's all. Enjoy.


## Q&A

_Why don´t you use CSS?_
- Because most of the document editors doesn't understand CSS.

_What about Composer?_
- This library isn't conceived to be used as part of a project (please don't). So composer.. nope.

_...and classes, and a healthy code organization?_
- We're talking about less than 100 lines of code. Is it necessary to fall in a cargo-cult programming situation?

_May I use your code to...?_
- Do whatever you want. It's yours.


```
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@.....................@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@.............. @@@@................@@@@@@@@@@@@
@@@@@@@@...................@@@@......................@@@@@@@
@@@@@........@@@@@@@@@@ .. @@@@@@@@@ ...@@@@@@@@@ ......@@@@
@@...........@@@ ****@@@@ @@@@ ***@@@@ @@@ ****@@@@ ......@@
@...........@@@@......@@@ @@@ ... @@@..@@@..... @@@ ......@@
@.......... @@@ ... @@@@ @@@@.... @@@ @@@ ... @@@@ .......@@
@@.........@@@@@@@@@@....@@@ ... @@@..@@@@@@@@@..........@@@
@@@@...... @@@ ......................@@@ ...............@@@@
@@@@@@@...    ......................     ............@@@@@@@
@@@@@@@@@@@....................................@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@...................@@@@@@@@@@@@@@@@@@@@@@@
```
