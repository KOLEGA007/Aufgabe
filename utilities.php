<?php
defined("APP") or die("NO DIRECT ACCESS");

//Just copied from stackoverflow
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
 ?>
