<?php
include_once("passwords.php5");

$DB_SERVER = "mysql-be-yes-I-really-mean-prod.shodor.org";

$CSERD_DB_NAME = "db_cserd";
$CSERD_DB_USER = "adm_cserd";
$SDR_DB_NAME = "db_sdr";
$SDR_DB_USER = "adm_sdr";

$cserdDbConn = new mysqli(
  $DB_SERVER,
  $CSERD_DB_USER,
  $CSERD_DB_PASS,
  $CSERD_DB_NAME
);
if ($cserdDbConn->connect_error) {
  die("CSERD database connection failed: " . $cserdDbConn->connect_error);
}

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
