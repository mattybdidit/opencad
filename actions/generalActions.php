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
/*
    This file handles all actions for admin.php script
*/

require_once(__DIR__ . "/../oc-config.php");
require_once(__DIR__ . "/../oc-functions.php");

/**
 * Patch notes:
 * Adding the `else` to make a `else if` prevents the execution
 * of multiple functions at the same time by the same client
 *
 * Running multiple functions at the same time doesnt seem to
 * be a needed feature.
 */
if (isset($_GET['getCalls'])) {
    getActiveCalls();
} else if (isset($_GET['getMyCall'])) {
    getMyCall();
} else if (isset($_GET['getCallDetails'])) {
    getCallDetails();
} else if (isset($_GET['getAvailableUnits'])) {
    getAvailableUnits();
} else if (isset($_GET['getUnAvailableUnits'])) {
    getUnAvailableUnits();
} else if (isset($_POST['changeStatus'])) {
    changeStatus();
} else if (isset($_GET['getActiveUnits'])) {
    getActiveUnits();
} else if (isset($_GET['getActiveUnitsModal'])) {
    getActiveUnitsModal();
} else if (isset($_POST['logoutUser'])) {
    logoutUser();
} else if (isset($_POST['setTone'])) {
    setTone();
} else if (isset($_GET['checkTones'])) {
    checkTones();
} else if (isset($_GET['getDispatchers'])) {
    getDispatchers();
} else if (isset($_GET['getDispatchersMDT'])) {
    getDispatchersMDT();
} else if (isset($_POST['quickStatus'])) {
    quickStatus();
} else if (isset($_GET['getAOP'])) {
    getAOP();
}

function quickStatus()
{
    $event = htmlspecialchars($_POST['event']);
    $callId = htmlspecialchars($_POST['callId']);
    session_start();
    $callsign = $_SESSION['callsign'];

    switch ($event) {
        case "enroute":
            $narrativeAdd = date("Y-m-d H:i:s") . ': ' . $callsign . ': En-Route<br/>';

            try {
                $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
            } catch (PDOException $ex) {
                $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
                $_SESSION['error_blob'] = $ex;
                header('Location: ' . BASE_URL . '/plugins/error/index.php');
                die();
            }

            $stmt = $pdo->prepare("UPDATE oc_calls SET call_narrative = concat(call_narrative, ?) WHERE call_id = ?");
            $result = $stmt->execute(array($narrativeAdd, $callId));

            if (!$result) {
                $_SESSION['error'] = $stmt->errorInfo();
                header('Location: ' . BASE_URL . '/plugins/error/index.php');
                die();
            }
            $pdo = null;

            break;

        case "onscene":

            break;
    }
}

function getMyCall()
{
    session_start();
    //First, check to see if they're on a call
    $uid = $_SESSION['id'];

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT * from oc_active_users WHERE oc_active_users.id = ? AND oc_active_users.status = '0' AND oc_active_users.status_detail = '3'");
    $result = $stmt->execute(array($uid));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $num_rows = $stmt->rowCount();

    if ($num_rows == 0) {
        echo '<div class="alert alert-info"><span>Not currently on a call </span></div> ';
    } else {
        //Figure out what call the user is on
        $stmt = $pdo->prepare("SELECT call_id from oc_calls_users WHERE id = ?");
        $resStatus = $stmt->execute(array($uid));
        $call_id = $stmt->fetchColumn();

        if (!$resStatus) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }

        $stmt = $pdo->prepare("SELECT * from oc_calls WHERE call_id = ?");
        $resStatus = $stmt->execute(array($call_id));
        $result = $stmt;

        if (!$resStatus) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }

        $num_rows = $result->rowCount();

        if ($num_rows == 0) {
            echo '<div class="alert alert-info"><span>Not currently on a call</span></div>';
        } else {
            echo '<table id="activeCalls" class="table table-striped table-bordered">
                <thead>
                    <tr>
                    <th>Type</th>
                    <th>Call Type</th>
                    <th>Units</th>
                    <th>Location</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            ';


            $counter = 0;
            foreach ($result as $row) {
                echo '
                <tr id="' . $counter . '">
                    <td>' . $row[0] . '</td>';

                //Issue #28. Check if $row[1] == bolo. If so, change text color to orange
                if ($row[1] == "BOLO") {
                    echo '<td style="color:orange;">' . $row[1] . '</td>';
                    echo '<td><!--Leave blank--></td>';
                } else {
                    echo '<td>' . $row[1] . '</td>';
                    echo '
                            <td>';
                    getUnitsOnCall($row[0]);
                    echo '</td>';
                }


                echo '<td>' . $row[3] . '/' . $row[4] . '/' . $row[5] . '</td>';

                if (isset($_GET['type']) && $_GET['type'] == "responder") {
                    echo '
                        <td>
                            <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                        </td>';
                } else {
                    echo '
                    <td>
                        <button id="' . $row[0] . '" class="btn-link" style="color: red;" value="' . $row[0] . '" onclick="clearCall(' . $row[0] . ')">Clear</button>
                        <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                        <input name="uid" name="uid" type="hidden" value="' . $row[0] . '"/>
                    </td>';
                }

                echo '
                </tr>
                ';
                $counter++;
            }

            echo '
                </tbody>
                </table>
            ';
        }
    }
    $pdo = null;
}

//Checks to see if there are any active tones. Certain tones will add a session variable
function checkTones()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_tones");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $encode = array();
    foreach ($result as $row) {
        // If the tone is set to active
        if ($row[2] == "1") {
            $encode[$row[1]] = "ACTIVE";
        } else if ($row[2] == "0") {
            $encode[$row[1]] = "INACTIVE";
        }
    }
    echo json_encode($encode);
}

function setTone()
{
    $tone = htmlspecialchars($_POST['tone']);
    $action = htmlspecialchars($_POST['action']);

    $status;
    switch ($action) {
        case "start":
            $status = '1';
            break;
        case "stop":
            $status = '0';
            break;
    }

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("UPDATE oc_tones SET active = ? WHERE name = ?");
    $result = $stmt->execute(array($status, $tone));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    if ($action == "start") {
        echo "SUCCESS START";
    } else {
        echo "SUCCESS STOP";
    }
}

function logoutUser()
{
    $identifier = htmlspecialchars($_POST['unit']);

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("DELETE FROM oc_active_users WHERE identifier = ?");
    $result = $stmt->execute(array($identifier));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    echo "SUCCESS";
}

function changeStatus()
{
    $unit = htmlspecialchars($_POST['unit']);
    $status = htmlspecialchars($_POST['status']);
    $statusId;
    $statusDet;
    $onCall = false;

    switch ($status) {
        case "statusMeal":
            $statusId = '0';
            $statusDet = '4';
            break;
        case "statusOther":
            $statusId = '0';
            $statusDet = '2';
            break;
        case "statusAvailBusy":
            $statusId = '1';
            $statusDet = '1';
            $onCall = true;
            break;
        case "statusUnavailBusy":
            $statusId = '6';
            $statusDet = '6';
            $onCall = true;
            break;
        case "statusSig11":
            $statusId = '1';
            $statusDet = '5';
            break;
        case "statusArrivedOC":
            $statusId = '7';
            $statusDet = '7';
            $onCall = true;
            break;
        case "statusTransporting":
            $statusId = '8';
            $statusDet = '8';
            $onCall = true;
            break;

        case "10-52":
            $statusId = '8';
            $statusDet = '8';
            $onCall = true;
            break;
        case "10-23":
            $statusId = '7';
            $statusDet = '7';
            $onCall = true;
            break;
        case "10-65":
            $statusId = '8';
            $statusDet = '8';
            break;
        case "10-8":
            $statusId = '1';
            $statusDet = '1';
            $onCall = true;
            break;
        case "10-7":
            $statusId = '6';
            $statusDet = '6';
            $onCall = false;
            break;
        case "10-6":
            $statusId = '0';
            $statusDet = '2';
            break;
        case "10-5":
            $statusId = '0';
            $statusDet = '4';
            break;
        case "sig11":
            $statusId = '1';
            $statusDet = '5';
            break;
    }

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("UPDATE oc_active_users SET status = ?, status_detail = ? WHERE identifier = ?");
    $result = $stmt->execute(array($statusId, $statusDet, $unit));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    if ($onCall) {
        $stmt = $pdo->prepare("SELECT call_id FROM oc_calls_users WHERE identifier = ?");
        $resStatus = $stmt->execute(array($unit));
        $result = $stmt;

        if (!$resStatus) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }

        $callId = "";
        foreach ($result as $row) {
            $callId = $row[0];
        }

        $stmt = $pdo->prepare("SELECT callsign FROM oc_active_users WHERE identifier = ?");
        $resStatus = $stmt->execute(array($unit));
        $result = $stmt;

        if (!$resStatus) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }

        foreach ($result as $row) {
            $callsign = $row[0];
        }

        //Update the call_narrative to say they were cleared
        $narrativeAdd = date("Y-m-d H:i:s") . ': Unit Cleared: ' . $callsign . '<br/>';

        $stmt = $pdo->prepare("UPDATE oc_calls SET call_narrative = concat(call_narrative, ?) WHERE call_id = ?");
        $result = $stmt->execute(array($narrativeAdd, $callId));

        if (!$result) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }

        $stmt = $pdo->prepare("DELETE FROM oc_calls_users WHERE identifier = ?");
        $result = $stmt->execute(array($unit));

        if (!$result) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }
    }

    $pdo = null;
    echo "SUCCESS";
}

function deleteDispatcher()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("DELETE FROM oc_dispatchers WHERE identifier = ?");
    $result = $stmt->execute(array($_SESSION['identifier']));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;
}

function setDispatcher($dep)
{
    $status;
    switch ($dep) {
        case "1":
            $status = "0";
            break;
        case "2":
            $status = "1";
            break;
    }

    deleteDispatcher();

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("INSERT INTO oc_dispatchers (identifier, callsign, status) VALUES (?, ?, ?)");
    $result = $stmt->execute(array($_SESSION['identifier'], $_SESSION['identifier'], $status));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;
}

function getAOP()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT svalue from oc_config WHERE skey = 'aop'");
    $result = $stmt->execute();

    if (!$result) {
        $_SESSION['error'] = print_r($stmt->errorInfo());
        $_SESSION['error_blob'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $aop = $stmt->fetchColumn();
    echo 'AOP: ' . $aop . ' ';
    $pdo = null;
}

function getDispatchers()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_dispatchers WHERE status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-danger\"><span>No available units</span></div>";
    } else {

        echo '
            <table id="dispatchersTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Identifier</th>
                </tr>
            </thead>
            <tbody>
        ';
        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function getDispatchersMDT()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_dispatchers WHERE status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        $dispatcher = "false";
    } else {
        $dispatcher = "true";
    }
}

function setUnitActive($dep)
{
    $identifier = $_SESSION['identifier'];
    $uid = $_SESSION['id'];
    $status;
    switch ($dep) {
        case "1":
            $status = "1";
            break;
        case "2":
            $status = "2";
            break;
    }

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("REPLACE INTO oc_active_users (identifier, callsign, status, status_detail, id) VALUES (?, ?, ?, '6', ?)");
    $result = $stmt->execute(array($identifier, $identifier, $status, $uid));

    if (!$result) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;
}

function getAvailableUnits()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_active_users WHERE status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-danger\"><span>No available units</span></div>";
    } else {

        echo '
            <table id="activeUsers" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Identifier</th>
                <th>Callsign</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';


        $counter = 0;
        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td>
                <div class="dropdown"><button class="btn btn-link dropdown-toggle nopadding" type="button" data-toggle="dropdown">Status <span class="caret"></span></button><ul class="dropdown-menu">
                    <li><a id="statusMeal' . $counter . '" class="statusMeal ' . $row[0] . '" onclick="testFunction(this);">10-5/Meal Break</a></li>
                    <li><a id="statusOther' . $counter . '" class="statusOther ' . $row[0] . '" onclick="testFunction(this);">10-6/Other</a></li>
                    <li><a id="statusSig11' . $counter . '" class="statusSig11 ' . $row[0] . '" onclick="testFunction(this);">Signal 11</a></li>
                </ul></div>

                </td>
                <input name="uid" type="hidden" value=' . $row[0] . ' />
            </tr>
            ';
            $counter++;
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function getUnAvailableUnits()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_active_users WHERE status = '0'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-info\"><span>No unavailable units</span></div>";
    } else {
        echo '
                <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                    <th>Identifier</th>
                    <th>Callsign</th>
                    <th>Status</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            ';

        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td>';

            getIndividualStatus($row[1]);

            echo '</td>

                <td>
                <a id="logoutUser" class="nopadding logoutUser ' . $row[0] . '" onclick="logoutUser(this);" style="color:red; cursor:pointer;">Logout</a>&nbsp;&nbsp;&nbsp;
                <div class="dropdown"><button class="btn btn-link dropdown-toggle nopadding" style="display: inline-block; vertical-align:top;" type="button" data-toggle="dropdown">Status <span class="caret"></span></button><ul class="dropdown-menu">
                    <li><a id="statusAvail" class="statusAvailBusy ' . $row[0] . '" onclick="testFunction(this);">10-8/Available</a></li>
                </ul></div>
                </td>
                <input name="uid" type="hidden" value=' . $row[0] . ' />
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function getIndividualStatus($callsign)
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT status_detail FROM oc_active_users WHERE callsign = ?");
    $resStatus = $stmt->execute(array(htmlspecialchars($callsign)));
    $result = $stmt;

    if (!$resStatus) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $statusDetail = "";
    foreach ($result as $row) {
        $statusDetail = $row[0];
    }

    $stmt = $pdo->prepare("SELECT status_text FROM oc_statuses WHERE status_id = ?");
    $resStatus = $stmt->execute(array($statusDetail));
    $result = $stmt;

    if (!$resStatus) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $statusText = "";
    foreach ($result as $row) {
        $statusText = $row[0];
    }

    $pdo = null;
    echo $statusText;
}

function getIncidentType()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT code_name FROM oc_incident_type");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
    }
}


function getStreet()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT name FROM oc_streets");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
    }
}

function getActiveUnits()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT callsign FROM oc_active_users WHERE status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $encode = array();
    foreach ($result as $row) {
        $encode[$row[0]] = $row[0];
    }

    echo json_encode($encode);
}

function getActiveUnitsModal()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT callsign, identifier FROM oc_active_users WHERE status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $encode = array();
    foreach ($result as $row) {
        $encode[$row[1]] = $row[0];
    }

    echo json_encode($encode);
}

function getActiveCalls()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_calls");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo '<div class="alert alert-info"><span>No active calls</span></div>';
    } else {
        echo '<table id="activeCalls" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Call ID</th>
                <th>Call Type</th>
                <th>Units</th>
                <th>Location</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';


        $counter = 0;
        foreach ($result as $row) {
            echo '
            <tr id="' . $counter . '">
                <td>' . $row[0] . '</td>';

            //Issue #28. Check if $row[1] == bolo. If so, change text color to orange
            if ($row[1] == "BOLO") {
                echo '<td style="color:orange;">' . $row[1] . '</td>';
                echo '<td><!--Leave blank--></td>';
            } else {
                echo '<td>' . $row[1] . '</td>';
                echo '
                        <td>';
                getUnitsOnCall($row[0]);
                echo '</td>';
            }


            echo '<td>' . $row[3] . '/' . $row[4] . '/' . $row[5] . '</td>';

            if (isset($_GET['type']) && $_GET['type'] == "responder") {
                echo '
                    <td>
                        <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                    </td>';
            } else {
                echo '
                <td>
                    <button id="' . $row[0] . '" class="btn-link" style="color: red;" value="' . $row[0] . '" onclick="clearCall(' . $row[0] . ')">Clear</button>
                    <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                ';
                if (isset($_GET['officer']) && $_GET['officer'] == "yes") {
                    echo '<button id="' . $row[0] . '" class="btn-link" value="' . $row[0] . '" onclick="selfAssign(' . $row[0] . ')">Self Assign</button>';
                } else {
                    echo '<input id="' . $row[0] . '" type="submit" name="assign_unit" data-toggle="modal" data-target="#assign" class="btn-link ' . $row[0] . '" value="Assign"/>';
                }
            }
            echo '
                    <input name="uid" name="uid" type="hidden" value="' . $row[0] . '"/>
                </td>';

            echo '
            </tr>
            ';
            $counter++;
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function getActivePersonBOLO()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * from oc_bolos_persons");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo '<div class="alert alert-info"><span>No active calls</span></div>';
    } else {
        echo '<table id="activeCalls" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Type</th>
                <th>Call Type</th>
                <th>Units</th>
                <th>Location</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';


        $counter = 0;
        foreach ($result as $row) {
            echo '
            <tr id="' . $counter . '">
                <td>' . $row[0] . '</td>';

            //Issue #28. Check if $row[1] == bolo. If so, change text color to orange
            if ($row[1] == "BOLO") {
                echo '<td style="color:orange;">' . $row[1] . '</td>';
                echo '<td><!--Leave blank--></td>';
            } else {
                echo '<td>' . $row[1] . '</td>';
                echo '
                        <td>';
                getUnitsOnCall($row[0]);
                echo '</td>';
            }


            echo '<td>' . $row[2] . '/' . $row[3] . '/' . $row[4] . '</td>';

            if (isset($_GET['type']) && $_GET['type'] == "responder") {
                echo '
                    <td>
                        <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                    </td>';
            } else {
                echo '
                <td>
                    <button id="' . $row[0] . '" class="btn-link" style="color: red;" value="' . $row[0] . '" onclick="clearCall(' . $row[0] . ')">Clear</button>
                    <button id="' . $row[0] . '" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                    <input id="' . $row[0] . '" type="submit" name="assign_unit" data-toggle="modal" data-target="#assign" class="btn-link ' . $row[0] . '" value="Assign"/>
                    <input name="uid" name="uid" type="hidden" value="' . $row[0] . '"/>
                </td>';
            }

            echo '
            </tr>
            ';
            $counter++;
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function getUnitsOnCall($callId)
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT * FROM oc_calls_users WHERE call_id = ?");
    $resStatus = $stmt->execute(array(htmlspecialchars($callId)));
    $result = $stmt;

    if (!$resStatus) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    $units = "";
    if ($num_rows == 0) {
        $units = '<span style="color: red;">Unassigned</span>';
    } else {
        foreach ($result as $row) {
            $units = $units . '' . $row[2] . ', ';
        }
    }

    echo $units;
}

function getCallDetails()
{
    $callId = htmlspecialchars($_GET['callId']);

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT * FROM oc_calls WHERE call_id = ?");
    $resStatus = $stmt->execute(array($callId));
    $result = $stmt;

    if (!$resStatus) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $encode = array();
    foreach ($result as $row) {
        $encode["call_id"] = $row[0];
        $encode["call_type"] = $row[1];
        $encode["call_street1"] = $row[3];
        $encode["call_street2"] = $row[4];
        $encode["call_street3"] = $row[5];
        $encode["narrative"] = $row[6];
    }

    echo json_encode($encode);
}

function getCivilianNamesOption()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT id, name FROM oc_ncic_names");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    foreach ($result as $row) {
        echo "<option value=" . $row[0] . ">" . $row[1] . "</option>";
    }
}

function getCitations()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT citation_name FROM oc_citations");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
    }
}

/**#@+
 * function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
function getVehicleMakes()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT DISTINCT oc_vehicles.Make FROM oc_vehicles");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
    }
}

/**#@+
 * function getVehicleModels()
 *
 * Querys database to retrieve all vehicle models.
 *
 * @since 1.0a RC2
 */
function getVehicleModels()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT DISTINCT oc_vehicles.Model FROM oc_vehicles");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
    }
}

/**#@+
 * function getVehicle()
 *
 * Querys database to retrieve all vehicle models.
 *
 * @since 1.0a RC2
 */
function getVehicle()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * FROM oc_vehicles");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo '<option value="' . $row[1] . ' ' . $row[2] . '">' . $row[1] . '-' . $row[2] . '</option>';
    }
}

/**#@+
 * function getGenders()
 *
 * Querys database to retrieve genders.
 *
 * @since 1.0a RC2
 *
function getGenders()
{
    try{
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
    } catch(PDOException $ex)
    {
        die($ex->getMessage());
    }

    $result = $pdo->query("SELECT DISTINCT ".DB_PREFIX."genders.genders FROM ".DB_PREFIX."genders");

    if (!$result)
    {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: '.BASE_URL.'/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach($result as $row)
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}

/**#@+
 * function getColors()
 *
 * Querys database to retrieve genders.
 *
 * @since 1.0a RC2
 */
function getColors()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT color_group, color_name FROM oc_colors");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo '<option value="' . $row[0] . '-' . $row[1] . '">' . $row[0] . '-' . $row[1] . '</option>';
    }
}

function getCivilianNames()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT oc_ncic_names.id, oc_ncic_names.name FROM oc_ncic_names");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo "<option value=\"$row[0]\">$row[1]</option>";
    }
}

function callCheck()
{
    $uid = $_SESSION['id'];
    $identifier = $_SESSION['identifier'];

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $stmt = $pdo->prepare("SELECT * FROM oc_calls_users WHERE id = ?");
    $resStatus = $stmt->execute(array($uid));
    $result = $stmt;

    if (!$resStatus) {
        $_SESSION['error'] = $stmt->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        $stmt = $pdo->prepare("REPLACE INTO oc_active_users (identifier, callsign, status, status_detail, id) VALUES (?, ?, '0', '6', ?)");
        $result = $stmt->execute(array($identifier, $identifier, $uid));

        if (!$result) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }
    } else {
        $stmt = $pdo->prepare("REPLACE INTO oc_active_users (identifier, callsign, status, status_detail, id) VALUES (?, ?, '0', '3', ?)");
        $result = $stmt->execute(array($identifier, $identifier, $uid));

        if (!$result) {
            $_SESSION['error'] = $stmt->errorInfo();
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }
    }

    $pdo = null;
}

function getWeapons()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT * FROM oc_weapons");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    foreach ($result as $row) {
        echo '<option value="' . $row[1] . ' ' . $row[2] . '">' . $row[1] . '&#8212;' . $row[2] . '</option>';
    }
}

function rms_warnings()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT oc_ncic_names.name, oc_ncic_warnings.id, oc_ncic_warnings.warning_name, oc_ncic_warnings.issued_date, oc_ncic_warnings.issued_by FROM oc_ncic_warnings INNER JOIN oc_ncic_names ON oc_ncic_warnings.name_id=oc_ncic_names.id WHERE oc_ncic_warnings.status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-info\"><span>There are currently no warnings in the NCIC Database</span></div>";
    } else {
        echo '
            <table id="rms_warnings" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Name</th>
                <th>Warning Name</th>
                <th>Issued On</th>
                <th>Issued By</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function rms_citations()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT oc_ncic_names.name, oc_ncic_citations.id, oc_ncic_citations.citation_name, oc_ncic_citations.citation_fine, oc_ncic_citations.issued_date, oc_ncic_citations.issued_by FROM oc_ncic_citations INNER JOIN oc_ncic_names ON oc_ncic_citations.name_id=oc_ncic_names.id WHERE oc_ncic_citations.status = '1'");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-info\"><span>There are currently no citations in the NCIC Database</span></div>";
    } else {
        echo '
            <table id="rms_citations" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Name</th>
                <th>Citation Name</th>
				<th>Citation Amount</th>
                <th>Issued On</th>
                <th>Issued By</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
                <td>' . $row[5] . '</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function rms_arrests()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $result = $pdo->query("SELECT oc_ncic_names.name, oc_ncic_arrests.id, oc_ncic_arrests.arrest_reason, oc_ncic_arrests.arrest_fine, oc_ncic_arrests.issued_date, oc_ncic_arrests.issued_by FROM oc_ncic_arrests INNER JOIN oc_ncic_names ON oc_ncic_arrests.name_id=oc_ncic_names.id");

    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-info\"><span>There are currently no arrests in the NCIC Database</span></div>";
    } else {
        echo '
            <table id="rms_arrests" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Name</th>
                <th>Arrest Reason</th>
				<th>Arrest Amount</th>
                <th>Issued On</th>
                <th>Issued By</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
                <td>' . $row[5] . '</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

function rms_warrants()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        $_SESSION['error'] = "Could not connect -> " . $ex->getMessage();
        $_SESSION['error_blob'] = $ex;
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }

    $$result = $pdo->query("SELECT " . DB_PREFIX . "ncic_warrants.*, " . DB_PREFIX . "ncic_names.name FROM " . DB_PREFIX . "ncic_warrants INNER JOIN " . DB_PREFIX . "ncic_names ON " . DB_PREFIX . "ncic_names.id=" . DB_PREFIX . "ncic_warrants.name_id");


    if (!$result) {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo "<div class=\"alert alert-info\"><span>There are currently no warrants in the NCIC Database</span></div>";
    } else {
        echo '
            <table id="rms_warrants" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Status</th>
                <th>Name</th>
                <th>Warrant Name</th>
                <th>Issued On</th>
                <th>Expires On</th>
                <th>Issuing Agency</th>

                </tr>
            </thead>
            <tbody>
        ';

        foreach ($result as $row) {
            echo '
            <tr>
                <td>' . $row[6] . '</td>
                <td>' . $row[7] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[5] . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[3] . '</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}