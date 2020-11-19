<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'opencad');
define("DB_PREFIX", 'oc_');

function getBooleanSetting($name) {
  try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
  }
  catch(PDOException $ex) {
    $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
    $_SESSION['error_blob'] = $ex;
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  if(!isset($name)) die("Attempt to getBooleanSetting with no name");
  $stmt = $pdo->prepare("SELECT svalue from ".DB_PREFIX."config WHERE skey = :key");
  $stmt->bindValue(":key", $name);
  $result = $stmt->execute();

  if (!$result)
  {
      $_SESSION['error'] = print_r($stmt->errorInfo());
      $_SESSION['error_blob'] = $pdo->errorInfo();
      header('Location: '.BASE_URL.'/plugins/error/index.php');
      die();
  }
  $value = $stmt->fetchColumn();
  $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  return $boolValue;
}

function getStringSetting($name) {
  try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
  }
  catch(PDOException $ex) {
    $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
    $_SESSION['error_blob'] = $ex;
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  if(!isset($name)) die("Attempt to getStringSetting with no name");
  $stmt = $pdo->prepare("SELECT svalue from ".DB_PREFIX."config WHERE skey = :key");
  $stmt->bindValue(":key", $name);
  $result = $stmt->execute();

  if (!$result)
  {
      $_SESSION['error'] = print_r($stmt->errorInfo());
      $_SESSION['error_blob'] = $pdo->errorInfo();
      header('Location: '.BASE_URL.'/plugins/error/index.php');
      die();
  }
  $value = $stmt->fetchColumn();
  return $value;
}

define('COMMUNITY_NAME', 'OpenCAD DEV');
define('DEFAULT_LANGUAGE', 'en');
define('DEFAULT_LANGUAGE_DIRECTION', 'ltr');
define('BASE_URL', '//localhost/opencad-dev');
define('ENABLE_API_SECURITY', true);
define('CAD_FROM_EMAIL', 'cad@example.com');
define('CAD_FROM_NAME', COMMUNITY_NAME . ' CAD');
define('CAD_TO_EMAIL', 'admins@example.com');
define('CAD_TO_NAME', COMMUNITY_NAME . ' Administrators');
define('AUTH_KEY', 'anWOquAYHU#yxt9xMSDR9OZfC5AraDPkoOYriRhUGYdPYoW0fRH4NT0uSBp2u1VB');
define('SECURE_AUTH_KEY', 'T8rA1jpmxALcPOILOpURwaMzGVV9n7nH0c1jQc2WY20vZO18zDX@BzCEQfSI8i@H');
define('LOGGED_IN_KEY', 'Kdsv3DDhBOTrnYfNch1SQ1goEJ5aIFiYxo7wqedn#LLVkMKjfxrus3708nb4XRlR');
define('NONCE_KEY', 'XpXZR4I10wnOHiA1@tF#c2WKYJ9ilkhcMx3nj#tJdhh4e9ERQDNJKEK3evQd80e2');
define('AUTH_SALT', 'anWOquAYHU#yxt9xMSDR9OZfC5AraDPkoOYriRhUGYdPYoW0fRH4NT0uSBp2u1VB');
define('SECURE_AUTH_SALT', 'SZJE8icNyEQi#hm29U9IUPDB1F#7n#hziTtQWf3E7jkLB1g54S9EVt7642yE1yEh');
define('LOGGED_IN_SALT', 'uR9QXmtA!fqbwdXFbsn8W1FrJUvQ8FZiJ9yItd@xF6tSviuDESfvDSHbuIFxYhwP');
define('NONCE_SALT', '#nnbpqwAES3zBgIQn#0n0DKv3iorV4sEocD!UtmRyDNulGukhgcw8s!LdyZEi202');
define('COOKIE_NAME', 'imoboibnia9t99');

define('POLICE_NCIC', true);

define('POLICE_CALL_SELFASSIGN', true);

define('FIRE_PANIC', true);
define('FIRE_BOLO', true);
define('FIRE_NCIC_NAME', true);
define('FIRE_NCIC_PLATE', true);
define('FIRE_CALL_SELFASSIGN', true);

define('EMS_PANIC', true);
define('EMS_BOLO', true);
define('EMS_NCIC_NAME', true);
define('EMS_NCIC_PLATE', true);
define('EMS_CALL_SELFASSIGN', true);

define('ROADSIDE_PANIC', true);
define('ROADSIDE_BOLO', true);
define('ROADSIDE_NCIC_NAME', true);
define('ROADSIDE_NCIC_PLATE', true);
define('ROADSIDE_CALL_SELFASSIGN', true);
define('CIV_WARRANT', true);

define('CIV_REG', true);
define('CIV_LIMIT_MAX_IDENTITIES', '0');
define('CIV_LIMIT_MAX_VEHICLES', '0');

define('CIV_LIMIT_MAX_WEAPONS', '0');

define('MODERATOR_APPROVE_USER', true);
define('MODERATOR_EDIT_USER', true);
define('MODERATOR_SUSPEND_WITH_REASON', true);
define('MODERATOR_SUSPEND_WITHOUT_REASON', true);
define('MODERATOR_REACTIVATE_USER', true);
define('MODERATOR_REMOVE_GROUP', true);
define('MODERATOR_DELETE_USER', true);
define('MODERATOR_NCIC_EDITOR', true);
define('MODERATOR_DATA_MANAGER', true);
define('MODERATOR_DATAMAN_CITATIONTYPES', true);
define('MODERATOR_DATAMAN_DEPARTMENTS', true);
define('MODERATOR_DATAMAN_INCIDENTTYPES', true);
define('MODERATOR_DATAMAN_RADIOCODES', true);
define('MODERATOR_DATAMAN_STREETS', true);
define('MODERATOR_DATAMAN_VEHICLES', true);
define('MODERATOR_DATAMAN_WARNINGTYPES', true);
define('MODERATOR_DATAMAN_WARRANTTYPES', true);
define('MODERATOR_DATAMAN_WEAPONS', true);
define('MODERATOR_DATAMAN_IMPEXPRES', true);

define('DEMO_MODE', false);

define('USE_GRAVATAR', true);

define('OC_DEBUG', false);

/** That's all, stop editing! Happy roleplaying. **/
/**    Absolute path to the OpenCAD directory.   **/
if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');

if (isset($_NOLOAD)) {
				if ($_NOLOAD['oc-functions'] == 'true') {
				}
				else {
								include ABSPATH . "oc-functions.php";
				}
}
else include ABSPATH . "oc-functions.php";
?>
