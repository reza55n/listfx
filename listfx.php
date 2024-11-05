<?php

/**
 * Alvandsoft listFX - version 1.0
 * Copyright (C) 2024 Reza Alizade - <github.com/reza55n/listfx>
 * 
 * @param string $dir Can be absolute or relative, without trailing slash
 * @param string[] $rules A string array of rule entries
 * @param string $mode `'exclude'` (default) or `'include'`
 * @param bool $reverse Set to `true` to use for recursive deleting
 * @return array An array of found items. Each member is an array with two
 * members, 0: type (`'d'` for directory, `'f'` for file, `'l'` for link and
 * `'u'` for unknown), and 1: path (absolute or relative based on $dir).
 */
function listFX(string $dir, array $rules = [],
    string $mode = 'exclude', bool $reverse = false) {
  
  if ($mode === 'exclude')
    $isExclude = true;
  elseif ($mode === 'include')
    $isExclude = false;
  else
    throw new Exception('Invalid $mode: `' . $mode . '`.');
  
  $entries = scandir($dir);
  
  $result = [];
  $rulesHereDir = [];
  $rulesHereFile = [];
  $rulesChildren = [];
  foreach ($rules as $item) {
    if ($item == '' || $item == '/' || substr_count($item, '//') > 0)
      throw new Exception('Invalid $rules item `' . $item . '` (prevent empty ' .
        'items or entering two consecutive slashes: `//`).');
    
    $itemLen = strlen($item);
    if ($item[$itemLen - 1] == '/') {
      $ruleIsDir = true;
      $item = substr($item, 0, $itemLen - 1);
    } else
      $ruleIsDir = false;
    
    if ($item[0] == '/') {
      // Related to $dir
      $item = substr($item, 1);
      $level = substr_count($item, '/');
      
      if ($level == 0) { // Such as `/tmp` (turned into `tmp`)
        if ($ruleIsDir)
          $rulesHereDir[] = $item;
        else
          $rulesHereFile[] = $item;
      
      } else {           // Such as `/usr/bin` (turned into `usr/bin`)
        $pos = strpos($item, '/');
        $rulesChildren[substr($item, 0, $pos)][] =
          substr($item, $pos) . ($ruleIsDir ? '/' : '');
      }
    
    } else {
      // Not related to $dir
      
      $level = substr_count($item, '/');
      
      if ($level != 0)
        throw new Exception('Invalid $rules item `' . $item . '` (to use `/`, ' .
          'the item should start with `/`).');
      if ($ruleIsDir)
        $rulesHereDir[] = $item;
      else
        $rulesHereFile[] = $item;
      $rulesChildren['*'][] = $item . ($ruleIsDir ? '/' : '');
    }
  }
  
  // echo "######### HereDir for $dir:              ";
  // var_dump($rulesHereDir);
  // echo "######### HereFile for $dir:              ";
  // var_dump($rulesHereFile);
  // echo "######### Children for $dir:                  ";
  // var_dump($rulesChildren);
  
  foreach ($entries as $entry) {
    if ($entry === '.' || $entry === '..')
      goto nextEntry;
    
    $full = "$dir/$entry";

    $entryIsDir = false;
    if (is_file($full))
      $type = 'f';
    elseif (is_dir($full)) {
      $type = 'd';
      $entryIsDir = true;
    } elseif (is_link($full))
      $type = 'l';
    else
      $type = 'u';
    
    if ($isExclude) {
      if ($entryIsDir) {
        foreach ($rulesHereDir as $rle) {
          if (wildcardMatches($rle, $entry))
            goto nextEntry; // It's from a nested loop and better to use `goto`
        }
      } else {
        foreach ($rulesHereFile as $rle) {
          if (wildcardMatches($rle, $entry))
            goto nextEntry; // It's from a nested loop and better to use `goto`
        }
      }
    } else {
      $haveToGo = true;
      if ($entryIsDir) {
        foreach ($rulesHereDir as $rle) {
          if (wildcardMatches($rle, $entry)) {
            $haveToGo = false;
            break;
          }
        }
      } else {
        foreach ($rulesHereFile as $rle) {
          if (wildcardMatches($rle, $entry)) {
            $haveToGo = false;
            break;
          }
        }
      }
      if ($haveToGo)
        goto nextEntry; // It's from a nested loop and better to use `goto`
    }
    
    $result[] = [$type, $full];
    
    if ($entryIsDir) {
      $rulesToSend = [];
      foreach ($rulesChildren as $key => $value) {
        if (wildcardMatches($key, $entry)) {
          $rulesToSend = array_merge($rulesToSend, $value);
        }
      }
      
      $result = array_merge($result, listFX($full, $rulesToSend, $mode));
    }
    
    nextEntry:
  }
  
  if ($reverse)
    $result = array_reverse($result);
  
  return $result;
}

function wildcardMatches($wildcard, $string) {
  $wildcard = preg_quote($wildcard, '/');
  $wildcard = str_replace(['\*', '\?'], ['.*', '.'], $wildcard);
  $pattern = '/^' . $wildcard . '$/';
  return preg_match($pattern, $string) === 1;
}