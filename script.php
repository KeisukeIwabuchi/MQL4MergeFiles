<?php

$file_name = $argv[1];
$directory_pos = GetDirectoryPos($argv[1]);
$contents = GetContents($file_name);

while (strpos($contents, '#include') !== false) {
  $line = explode("\n", $contents);
  $line_count = count($line);
  $contents = '';
  for ($i = 0; $i < $line_count; $i++) {
    if (strpos($line[$i], '#include') === false) {
      $contents .= $line[$i] . "\n";
      continue;
    }

    $start = strpos($line[$i], '<') + 1;
    $end = strpos($line[$i], '>');
    $length = $end - $start;
    $file_name = substr($line[$i], $start, $length);
    $file_name = $directory_pos . $file_name;

    $contents .= GetContents($file_name) . "\n";
  }
}



echo $contents;


function GetDirectoryPos(string $path): string
{
  $pos = strpos($path, 'Experts');
  if ($pos !== false) {
    return substr($path, 0, $pos) . 'Include\\';
  }

  $pos = strpos($path, 'Indicators');
  if ($pos !== false) {
    return substr($path, 0, $pos) . 'Include\\';
  }

  $pos = strpos($path, 'Scripts');
  if ($pos !== false) {
    return substr($path, 0, $pos) . 'Include\\';
  }

  $pos = strpos($path, 'Include');
  if ($pos !== false) {
    return substr($path, 0, $pos) . 'Include\\';
  }

  return '';
}


function GetContents(string $file_name): string
{
  $contents = '';
  $fh = fopen($file_name, 'r');

  while ($line = fgets($fh)) {
    $line = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line);
    $contents .= $line . "\n";

    // if (strpos($line, '#include') !== false) {
    //   $start = strpos($line, '<') + 1;
    //   $end = strpos($line, '>');
    //   $length = $end - $start;
    //   $file_name = substr($line, $start, $length);

    //   echo $file_name . "\n";
    // }
  }

  fclose($fh);

  return $contents;
}