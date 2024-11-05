<?php
require("../listfx.php");

chdir("tree");

$files = listfx(".", [
  "/.git/",
  "assets/",
  "*.zip",
]);

print_r($files);

////// Expected output (simplified):
// Array (
//    [0] => ( [0] => d [1] => ./dist )
//    [1] => ( [0] => f [1] => ./dist/index.html )
//    [2] => ( [0] => f [1] => ./dist/version.txt )
//    [3] => ( [0] => f [1] => ./index.html )
//    [4] => ( [0] => d [1] => ./src )
//    [5] => ( [0] => f [1] => ./src/App.jsx )
//    [6] => ( [0] => f [1] => ./src/index.css )
//    [7] => ( [0] => f [1] => ./vite.config.js )
// )