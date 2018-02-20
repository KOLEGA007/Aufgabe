#!/usr/bin/env php
<?php
echo "Guten Tag" . PHP_EOL;
define("THE_END", 200);
for($i=0; $i<THE_END; ++$i)
{
  if($i % 2 == 0)
  {
    echo "Zwei";
  }
  if($i % 5 == 0)
  {
    echo "Fünf";
  }
  if($i % 7 == 0)
  {
    echo "Sieben";
  }
  if($i % 2 && $i % 5 && $i % 7)
  {
    echo $i;
  }
  echo PHP_EOL;
}
unset($i);
?>
