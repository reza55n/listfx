
**listFX – list Files with eXclude/include rules!**


Basic Bash example to exclude `.git` folder(s) while listing all files/folders from the current path:

`listfx . ".git/"`

Example output format:
```
- ./index.html
d ./src
- ./src/App.jsx
- ./src/index.css
- ./vite.config.js
```


**More complete examples with expected outputs can be found in `example` directory.**





## Use cases
**listFX** can be simply used in any app that handles multiple user files but doesn't plan to use (and re-implement) git's complex gitignore for every user directory:

- File and document search tools
- Bundlers
- Compilers/decompilers
- CI/CD utilities
- Batch video/audio/photo convertors/processors
- Intrusion detection systems
- Obfuscators/deobfuscators

..and so on





## Each rule entry
(Rules can be sent in two styles: each rule separated and sent by newline (as seen in Bash version) or each rule as a separate array member (PHP version)).

- Directory separator is always `/`, even in Microsoft Windows.

- If the entry ends with `/`, it will match a directory, otherwise will match a file.

- If the entry starts with `/`, it will be related to `$dir`

- If the entry doesn't start with `/`, it can be anywhere and also can't contain `/` in the middle.

- `*` and `?` wildcards can be used and they're always limited to the current file/folder and not parent or children, i.e limited to the separator (`/`).





## Output
Depending on the language, output can be in 2 styles, e.g Bash version is baesd on the first style and PHP version on the second one:

1. A string which each file/folder path is on a separate line and directories are prepended by `"d "` (without quotations), and other structures (files, etc) are prepended by `"- "` or other characters.

2. A 2D array which each file/folder is a separate member and itself is an array whose first member is a character representing the type (`"d"` for directories and `"-"` or other characters for non-directories) and the second member is the path.





## Example rules
### In `exclude` mode
`/vendor/` excludes the `vendor` folder placed right under `$dir`, and all its contents.

`node_modules.*` excludes `node_modules.tar`, `node_modules.zip`, etc files, wherever they are, and `node_modules/` excludes only `node_modules` folder, wherever it is, and all its contents.

`/test-?/*.php` excludes all `PHP` files that are directly inside `test-1`, `test-a`, etc folders under `$dir`.

To exclude a folder's contents and keep itself, instead of `/parent/exclude-children/` you can end the phrase with `/*` such as `/parent/exclude-children/*`



### In `include` mode
(In `include` mode, entering rules `*` and `*/` together causes listing all files and folders, regardless of other rules.)

All directories, nested: `*/`

All file right under `$dir`: `/*`

All JavaScript files right under `$dir`: `/*.js`

All files and folders with depth of 2: `/*` + `/*/` + `/*/*` + `/*/*/` (second rule is necessary to allow to move into subdirectories)

All Python files, wherever they are: `*/` + `*.py` (directories should be manually excluded from the final results)





## Compare to git's `.gitignore`
**listFX**'s rules structure is similar to `.gitignore`'s, with some simplifications:

(Firstly no need to a separate `.gitignore` file, as everything is sent as arguments.. BTW naturally they can be read from a file.)

- No pattern `[]` (not implemented yet!)

- **Main reason of simplicity:** There's no `!` (and no need to escape it) and include/exclude is set only once per request or once for each app, therefore **the order of rules don't matter**.

- If you want to match related to root, it's mandatory to start the rule with `/` (makes the rules more clear and standard)

- No worries about nested `.gitignore` files and their behavior and priority related to the parent(s)

- No commenting and therefore no need to escape `#` (and also no need to escape space)

- No double asterisks and other complex rules





## Compare to POSIX tools (find and grep)
**listFX** is mainly a function that can be re-implemented in every programming language with a simple list of rules and an `include/exclude` option as inputs, in contrast to POSIX tools that each of them has multiple switches and options for very different use cases may occur.


**find** can be incredibly complex for the use cases that **listFX** can simply handle and even excluding a single directory requires multiple switches, let alone excluding multiple file/folders with wildcard and related to the root or anywhere they are and also with simple switch between exlude and include modes, that all can be simply done in **listFX**.


**grep** is generally for looking into (files') contents and it doesn't give us directories and empty files. Also piping from find to grep (`find . | grep ...`) has a very low performance, as instead of the intended paths/directories, it will first list ALL files without any blacklists (including contents of `dist`, `.git`, `cache`, `node_modules`, `vendor`, etc) and only after that, starts filtering the unwanted files/folders.


**Weakness:** listFX is not implemented in any compiled language (yet) and Bash version is slower than find/grep, BTW its simplicity and also excluding/including system can overcome its slowness.

