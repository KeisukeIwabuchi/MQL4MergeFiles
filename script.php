<?php

$data_types = [
  'char',
  'short',
  'int',
  'long',
  'uchar',
  'ushort',
  'uint',
  'ulong',
  'bool',
  'string',
  'double',
  'float',
  'color',
  'datetime',
];

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

$new_file_name = str_replace('.mq4', '_merge.mq4', $argv[1]);
file_put_contents($new_file_name, $contents);

echo $new_file_name;


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
  }

  fclose($fh);

  return $contents;
}


function GetFunction(string $line): mixed
{
  $pos = strpos($line, ' ');
  if ($pos === false) {
    return false;
  }

  $is_member_function = strpos($line, '::');
  if ($is_member_function !== 0) {
    $end = strpos($line, '(');
    return substr($line, $pos + 1, $end);
  }
}
