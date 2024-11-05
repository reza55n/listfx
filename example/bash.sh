#!/bin/bash
# First add listfx file's containig directory to PATH or
# copy it to an already-added folder.
# Also you can call it with a relative/absolute path (current example).
# Also change its permission to executable, if needed.

cd tree

../../listfx . "/.git/
assets/
*.zip"

read

##### Expected output:
# d ./dist
# - ./dist/index.html
# - ./dist/version.txt
# - ./index.html
# d ./src
# - ./src/App.jsx
# - ./src/index.css
# - ./vite.config.js