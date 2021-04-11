<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'opencad');
define('DB_PREFIX', 'oc_');
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
} catch (PDOException $ex) {
    die($ex->getMessage());
}
$stmt = $pdo->prepare('SELECT skey, svalue from oc_config');
$stmt->execute();
$results = $stmt->fetchAll();
foreach ($results as list($key, $value)) {
    if ($key == "db_host" || $key == "db_user" || $key == "db_password" || $key == "db_prefix") continue;
    // echo $key . " " . $value. " <br>";
    switch ($key) {
        case 'fire_panic':
            define(strtoupper('fire_panic'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'fire_bolo':
            define(strtoupper('fire_bolo'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'fire_ncic_name':
            define(strtoupper('fire_ncic_name'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'fire_ncic_plate':
            define(strtoupper('fire_ncic_plate'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'fire_call_selfassign':
            define(strtoupper('fire_call_selfassign'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'ems_ncic_name':
            define(strtoupper('ems_ncic_name'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'ems_call_selfassign':
            define(strtoupper('ems_call_selfassign'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'ems_panic':
            define(strtoupper('ems_panic'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'ems_bolo':
            define(strtoupper('ems_bolo'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'roadside_panic':
            define(strtoupper('roadside_panic'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'roadside_ncic_name':
            define(strtoupper('roadside_ncic_name'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'roadside_ncic_plate':
            define(strtoupper('roadside_ncic_plate'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'discord_webhooks':
            define(strtoupper('discord_webhooks'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'enable_api_security':
            define(strtoupper('enable_api_security'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'police_ncic':
            define(strtoupper('police_ncic'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'police_call_selfassign':
            define(strtoupper('police_call_selfassign'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'roadside_bolo':
            define(strtoupper('roadside_bolo'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'roadside_call_selfassign':
            define(strtoupper('roadside_call_selfassign'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'civ_warrant':
            define(strtoupper('civ_warrant'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'civ_reg':
            define(strtoupper('civ_reg'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_approve_user':
            define(strtoupper('moderator_approve_user'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_suspend_with_reason':
            define(strtoupper('moderator_suspend_with_reason'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_edit_user':
            define(strtoupper('moderator_edit_user'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_suspend_without_reason':
            define(strtoupper('moderator_suspend_without_reason'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_reactivate_user':
            define(strtoupper('moderator_reactivate_user'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_remove_group':
            define(strtoupper('moderator_remove_group'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_delete_user':
            define(strtoupper('moderator_delete_user'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_ncic_editor':
            define(strtoupper('moderator_ncic_editor'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_data_manager':
            define(strtoupper('moderator_data_manager'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_citationtypes':
            define(strtoupper('moderator_dataman_citationtypes'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_departments':
            define(strtoupper('moderator_dataman_departments'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_incidenttypes':
            define(strtoupper('moderator_dataman_incidenttypes'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_radiocodes':
            define(strtoupper('moderator_dataman_radiocodes'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_streets':
            define(strtoupper('moderator_dataman_streets'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_vehicles':
            define(strtoupper('moderator_dataman_vehicles'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_warningtypes':
            define(strtoupper('moderator_dataman_warningtypes'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_warranttypes':
            define(strtoupper('moderator_dataman_warranttypes'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_weapons':
            define(strtoupper('moderator_dataman_weapons'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'moderator_dataman_impexpres':
            define(strtoupper('moderator_dataman_impexpres'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'demo_mode':
            define(strtoupper('demo_mode'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'use_gravatar':
            define(strtoupper('use_gravatar'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'oc_debug':
            define(strtoupper('oc_debug'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'ems_ncic_plate':
            define(strtoupper('ems_ncic_plate'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'login_captcha_enabled':
            define(strtoupper('login_captcha_enabled'), filter_var($value, FILTER_VALIDATE_BOOLEAN));
            break;
        case 'civ_limit_max_identities':
            define(strtoupper('civ_limit_max_identities'), filter_var($value, FILTER_VALIDATE_INT));
            break;
        case 'civ_limit_max_vehicles':
            define(strtoupper('civ_limit_max_vehicles'), filter_var($value, FILTER_VALIDATE_INT));
            break;
        case 'civ_limit_max_weapons':
            define(strtoupper('civ_limit_max_weapons'), filter_var($value, FILTER_VALIDATE_INT));
            break;

        default:
            define(strtoupper($key), $value);
    }
}
if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');
if (isset($_NOLOAD)) {
    if ($_NOLOAD['oc-functions'] == 'true') {
    } else {
        include ABSPATH . "oc-functions.php";
    }
} else include ABSPATH . "oc-functions.php";
