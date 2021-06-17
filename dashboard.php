<?php

/**
Open source CAD system for RolePlaying Communities.
Copyright (C) 2017 Shane Gill

This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

This program comes with ABSOLUTELY NO WARRANTY; Use at your own risk.
 **/

session_start();
include("./oc-config.php");
require("./actions/generalActions.php");
require_once("./plugins/plugin_api/plugin_api.php");
$PluginAPI = new PluginApi();

if (empty($_SESSION['logged_in'])) {
    header('Location: ./index.php');
    die("Not logged in");
}
setDispatcher("1");


$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$link) {
    die('Could not connect: ' . mysqli_errno($link));
}

$id = $_SESSION['id'];
$sql = "SELECT * from " . DB_PREFIX . "user_departments WHERE user_id = \"$id\"";
$getAdminPriv = "SELECT `admin_privilege` from " . DB_PREFIX . "users WHERE id = \"$id\"";

$result = mysqli_query($link, $sql);
$adminPriv = mysqli_query($link, $getAdminPriv);


$adminButton = "";
$dispatchButton = "";
$highwayButton = "";
$stateButton = "";
$fireButton = "";
$emsButton = "";
$sheriffButton = "";
$policeButton = "";
$civilianButton = "";
$roadsideAssistButton = "";

$num_rows = $result->num_rows;


while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
    if ($row[1] == "1") {
        $_SESSION['dispatch'] = 'YES';
        $dispatchButton = '
            <li class="collection-item">
                <div>Dispatch <a href="./cad.php" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            <li class="collection-item">
                <div>Dispatch (New, Unfinished CAD) <a href="./newcad.php" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "7") {
        $_SESSION['ems'] = 'YES';
        $emsButton = '
            <li class="collection-item">
                <div>EMS Paramedic<a href="./mdt.php?dep=ems" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "6") {
        $_SESSION['fire'] = 'YES';
        $fireButton = '
        <li class="collection-item">
            <div>Fire Department <a href="./mdt.php?dep=fire" class="secondary-content">
            <i class="material-icons red-text">send</i></a>
            </div>
        </li>
        ';
    } else if ($row[1] == "3") {
        $_SESSION['highway'] = 'YES';
        $highwayButton = '
            <li class="collection-item">
                <div>Highway Patrol <a href="./mdt.php?dep=highway" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "5") {
        $_SESSION['police'] = 'YES';
        $policeButton = '
            <li class="collection-item">
                <div>Police <a href="./mdt.php?dep=police" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "4") {
        $_SESSION['sheriff'] = 'YES';
        $sheriffButton =
            '
            <li class="collection-item">
                <div>Sheriff <a href="./mdt.php?dep=sheriff" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "2") {
        $_SESSION['state'] = 'YES';
        $stateButton =
            '
            <li class="collection-item">
                <div>State Police <a href="./mdt.php?dep=state" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "8") {
        $_SESSION['civillian'] = 'YES';
        $civilianButton = '
            <li class="collection-item">
                <div>Civilian <a href="./civilian.php" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    } else if ($row[1] == "9") {
        $_SESSION['roadsideAssist'] = 'YES';
        $roadsideAssistButton =
            '
            <li class="collection-item">
                <div>Tow <a href="./mdt.php?dep=roadsideAssist" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
    }
}
$adminRows = $adminPriv->num_rows;
if ($adminRows < 2) {
    while ($adminRow = mysqli_fetch_array($adminPriv, MYSQLI_BOTH)) {
        if ($adminRow[0] == "3" || $adminRow[0] == "2") {
            $adminButton =
                '
            <li class="collection-item">
                <div>Staff Panel <a href="./oc-admin/staff.php" class="secondary-content">
                <i class="material-icons red-text">send</i></a>
                </div>
            </li>
            ';
        }
    }
}

mysqli_close($link);
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="./css/dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OpenCAD | Dashboard</title>
</head>

<body>
    <div class="row center">
        <h2 class="animate__animated animate__fadeInDown">OpenCAD Dashboard</h2>
        <h4 class="animate__animated animate__fadeInDown"> Welcome, <?php echo $_SESSION['name']; ?> </h4>
        <a class="btn red darken-3 animate__animated animate__fadeInDown" href="./actions/logout.php">LOGOUT</a>
    </div>
    <div id="container">
        <div class="row" style="margin-left: 50rem;">
            <div class="animate__animated animate__fadeInDown" style="width: 44vh !important;">
                <ul class="collection with-header">
                    <li class="collection-header">
                        <h4>Clock In</h4>
                    </li>
                    <?php echo $adminButton; ?>
                    <?php echo $roadsideAssistButton; ?>
                    <?php echo $civilianButton; ?>
                    <?php echo $sheriffButton; ?>
                    <?php echo $stateButton; ?>
                    <?php echo $dispatchButton; ?>
                    <?php echo $fireButton; ?>
                    <?php echo $emsButton; ?>
                    <?php echo $highwayButton; ?>
                    <?php echo $policeButton; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="container center animate__animated animate__fadeInDown">
        <h3>OpenCAD Changelog (this version)</h3>
        <p> OpenCAD is open source. This version is maintained by <a
                href="https://github.com/Matt4499/opencad">Matt4499</a></p>

        <ul class="collection">
            <?php
            foreach ($PluginAPI->get_oc_version_changes() as $value) {
                echo "<li class=\"collection-item\">$value</li>";
            }
            ?>
        </ul>
    </div>
</body>

</html>