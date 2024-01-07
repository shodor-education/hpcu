<?php
include_once("passwords.php5");

$DB_SERVER = "mysql-be-yes-I-really-mean-prod.shodor.org";

$SDR_DB_NAME = "db_sdr";
$SDR_DB_USER = "adm_sdr";

$sdrDbConn = new mysqli(
  $DB_SERVER,
  $SDR_DB_USER,
  $SDR_DB_PASS,
  $SDR_DB_NAME
);
if ($sdrDbConn->connect_error) {
  die("SDR database connection failed: " . $sdrDbConn->connect_error);
}
?>
