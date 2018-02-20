<?php
defined("APP") or die("NO DIRECT ACCESS");
define("BACKEND", "MYSQL");
define("DATE_FORMAT", "Y-m-d H:i");
define("TIMESTAMP_FORMAT", "Y-m-d H:i:s");
$database = 'mysql:dbname=test;host=127.0.0.1';
$user = 'root'; // WHOOOPS
$password = 'password';
try {
  $db = new PDO($database, $user, $password);
} catch (PDOException $e) {
    die ('No Database Connection: ' . $e->getMessage());
}
?>
