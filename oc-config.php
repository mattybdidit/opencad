<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'opencad');
define("DB_PREFIX", 'oc_');

function getBooleanSetting($name)
{
  try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
  } catch (PDOException $ex) {
    $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
    $_SESSION['error_blob'] = $ex;
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  if (!isset($name)) die("Attempt to getBooleanSetting with no name");
  $stmt = $pdo->prepare("SELECT svalue from " . DB_PREFIX . "config WHERE skey = :key");
  $stmt->bindValue(":key", $name);
  $result = $stmt->execute();

  if (!$result) {
    $_SESSION['error'] = print_r($stmt->errorInfo());
    $_SESSION['error_blob'] = $pdo->errorInfo();
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  $value = $stmt->fetchColumn();
  $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  return $boolValue;
}

function getStringSetting($name)
{
  try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
  } catch (PDOException $ex) {
    $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
    $_SESSION['error_blob'] = $ex;
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  if (!isset($name)) die("Attempt to getStringSetting with no name");
  $stmt = $pdo->prepare("SELECT svalue from " . DB_PREFIX . "config WHERE skey = :key");
  $stmt->bindValue(":key", $name);
  $result = $stmt->execute();

  if (!$result) {
    $_SESSION['error'] = print_r($stmt->errorInfo());
    $_SESSION['error_blob'] = $pdo->errorInfo();
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  $value = $stmt->fetchColumn();
  return $value;
}

function getIntSetting($name)
{
  try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
  } catch (PDOException $ex) {
    $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
    $_SESSION['error_blob'] = $ex;
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  if (!isset($name)) die("Attempt to getStringSetting with no name");
  $stmt = $pdo->prepare("SELECT svalue from " . DB_PREFIX . "config WHERE skey = :key");
  $stmt->bindValue(":key", $name);
  $result = $stmt->execute();

  if (!$result) {
    $_SESSION['error'] = print_r($stmt->errorInfo());
    $_SESSION['error_blob'] = $pdo->errorInfo();
    header('Location: ' . BASE_URL . '/plugins/error/index.php');
    die();
  }
  $value = $stmt->fetchColumn();
  $intValue = intval($value);
  return $intValue;
}
define('DISCORD_LOGS', getBooleanSetting('discord_logs'));
define('DISCORD_WEBHOOK_URL', getStringSetting('discord_webhook_url'));
define('COMMUNITY_NAME', getStringSetting('community_name'));
define('DEFAULT_LANGUAGE', getStringSetting('default_language'));
define('DEFAULT_LANGUAGE_DIRECTION', getStringSetting('default_language_direction'));
define('BASE_URL', getStringSetting('base_url'));
define('ENABLE_API_SECURITY', getBooleanSetting('enable_api_security'));
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

define('POLICE_NCIC', getBooleanSetting('police_ncic'));

define('POLICE_CALL_SELFASSIGN', getBooleanSetting('true'));

define('FIRE_PANIC', getBooleanSetting('fire_panic'));
define('FIRE_BOLO', getBooleanSetting('fire_bolo'));
define('FIRE_NCIC_NAME', getBooleanSetting('fire_ncic_name'));
define('FIRE_NCIC_PLATE', getBooleanSetting('fire_ncic_plate'));
define('FIRE_CALL_SELFASSIGN', getBooleanSetting('fire_call_selfassign'));

define('EMS_PANIC', getBooleanSetting('ems_panic'));
define('EMS_BOLO', getBooleanSetting('ems_bolo'));
define('EMS_NCIC_NAME', getBooleanSetting('ems_ncic_name'));
define('EMS_NCIC_PLATE', getBooleanSetting('ems_ncic_plate'));
define('EMS_CALL_SELFASSIGN', getBooleanSetting('ems_call_selfassign'));

define('ROADSIDE_PANIC', getBooleanSetting('roadside_panic'));
define('ROADSIDE_BOLO', getBooleanSetting('roadside_bolo'));
define('ROADSIDE_NCIC_NAME', getBooleanSetting('roadside_ncic_name'));
define('ROADSIDE_NCIC_PLATE', getBooleanSetting('roadside_ncic_plate'));
define('ROADSIDE_CALL_SELFASSIGN', getBooleanSetting('roadside_call_selfassign'));

define('CIV_WARRANT', getBooleanSetting('civ_warrant'));
define('CIV_REG', getBooleanSetting('civ_reg'));
define('CIV_LIMIT_MAX_IDENTITIES', getIntSetting('civ_limit_max_identities'));
define('CIV_LIMIT_MAX_VEHICLES', getIntSetting('civ_limit_max_vehicles'));
define('CIV_LIMIT_MAX_WEAPONS', getIntSetting('civ_limit_max_weapons'));

define('MODERATOR_APPROVE_USER', getBooleanSetting('moderator_approve_user'));
define('MODERATOR_EDIT_USER', getBooleanSetting('moderator_edit_user'));
define('MODERATOR_SUSPEND_WITH_REASON', getBooleanSetting('moderator_suspend_with_reason'));
define('MODERATOR_SUSPEND_WITHOUT_REASON', getBooleanSetting('moderator_suspend_without_reason'));
define('MODERATOR_REACTIVATE_USER', getBooleanSetting('moderator_reactivate_user'));
define('MODERATOR_REMOVE_GROUP', getBooleanSetting('moderator_remove_group'));
define('MODERATOR_DELETE_USER', getBooleanSetting('moderator_delete_user'));
define('MODERATOR_NCIC_EDITOR', getBooleanSetting('moderator_ncic_editor'));
define('MODERATOR_DATA_MANAGER', getBooleanSetting('moderator_data_manager'));
define('MODERATOR_DATAMAN_CITATIONTYPES', getBooleanSetting('moderator_dataman_citationtypes'));
define('MODERATOR_DATAMAN_DEPARTMENTS', getBooleanSetting('moderator_dataman_departments'));
define('MODERATOR_DATAMAN_INCIDENTTYPES', getBooleanSetting('moderator_dataman_incidenttypes'));
define('MODERATOR_DATAMAN_RADIOCODES', getBooleanSetting('moderator_dataman_radiocodes'));
define('MODERATOR_DATAMAN_STREETS', getBooleanSetting('moderator_dataman_streets'));
define('MODERATOR_DATAMAN_VEHICLES', getBooleanSetting('moderator_dataman_vehicles'));
define('MODERATOR_DATAMAN_WARNINGTYPES', getBooleanSetting('moderator_dataman_warningtypes'));
define('MODERATOR_DATAMAN_WARRANTTYPES', getBooleanSetting('moderator_dataman_warranttypes'));
define('MODERATOR_DATAMAN_WEAPONS', getBooleanSetting('moderator_dataman_weapons'));
define('MODERATOR_DATAMAN_IMPEXPRES', getBooleanSetting('moderator_dataman_impexpres'));

define('DEMO_MODE', getBooleanSetting('demo_mode'));
define('USE_GRAVATAR', getBooleanSetting('use_gravatar'));
define('OC_DEBUG', getBooleanSetting('oc_debug'));

/** That's all, stop editing! Happy roleplaying. **/
/**    Absolute path to the OpenCAD directory.   **/
if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');

if (isset($_NOLOAD)) {
  if ($_NOLOAD['oc-functions'] == 'true') {
  } else {
    include ABSPATH . "oc-functions.php";
  }
} else include ABSPATH . "oc-functions.php";
