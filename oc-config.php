<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'opencad');
define("DB_PREFIX", 'oc_');
try {
  $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
} catch (PDOException $ex) {
  $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
  $_SESSION['error_blob'] = $ex;
  header('Location: ' . BASE_URL . '/plugins/error/index.php');
  die();
}
$stmt = $pdo->prepare('SELECT skey, svalue from oc_config');
$stmt->execute();
$results = $stmt->fetchAll();

foreach ($results as list($key, $value)) {
  // echo "Key: $key, Value: $value <br>";
  if ($key == "db_host" || $key == "db_user" || $key == "db_password" || $key == "db_prefix") continue;
  define(strtoupper($key), $value);
}

if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');

if (isset($_NOLOAD)) {
  if ($_NOLOAD['oc-functions'] == 'true') {
  } else {
    include ABSPATH . "oc-functions.php";
  }
} else include ABSPATH . "oc-functions.php";
