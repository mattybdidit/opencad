<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (version_compare(PHP_VERSION, '7.1', '<')) {
    $_SESSION['error'] = "An incompatable version of PHP is active. OpenCAD requires PHP 7.2 at minimum. Currently PHP " . phpversion() . " is active. Please change the PHP version, or contact your webhost support.";
    die($_SESSION['error']);
}
if (OC_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ERROR);
}
function get_avatar()
{
    if (USE_GRAVATAR) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($_SESSION['email'])));
        $url .= "?size=256&default=https://i.imgur.com/VN4YCW7.png";
        return $url;
    } else {
        return "https://i.imgur.com/VN4YCW7.png";
    }
}
function getMySQLVersion()
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    printf($mysqli->server_info);
    $mysqli->close();
}
function pageLoadTime()
{
    $time = microtime(true);
    $time = explode(' ', $time);
    $time = $time[0];
    $finish = $time;
    $total_time = $finish / 60 / 60 / 60 / 60 / 60;
    $final_time = round(($total_time), 2);
    echo 'Page generated in ' . $final_time . ' seconds.';
}
function getApiKey($del_key = false)
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }

    $result = $pdo->query("SELECT svalue FROM oc_config WHERE `skey`='api_key'");

    if (!$result) {
        die($pdo->errorInfo());
    }

    if ($result->rowCount() >= 1 && $del_key) {
        // error_log("Do delete: $del_key");
        $key = generateRandomString(64);
        $result = $pdo->query("UPDATE oc_config SET `svalue`='$key' WHERE `skey`='api_key'");

        if (!$result) {
            die($pdo->errorInfo());
        }


        $key = $result->fetch(PDO::FETCH_ASSOC)['svalue'];
        $pdo = null;
        return $key;
    }
}
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function getOpenCADVersion()
{
    echo '0.3.3';
}
function permissionDenied()
{
    die("Sorry, you don't have permission to access this page.");
}