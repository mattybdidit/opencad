<?php

require_once(__DIR__ . "/../oc-config.php");
require_once(__DIR__ . "/../oc-functions.php");

if (isset($_GET['getCalls'])) {
    getCalls();
}
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'searchPlate':
            searchPlate();
            break;
        default:
            die("Unknown action: " . $_GET['action']);
    }
}

function getCalls()
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        die("Could not connect to MySQL. [new.php->getCalls()]");
    }

    $result = $pdo->query("SELECT * from oc_calls");

    if (!$result) {
        die("Query failed. [new.php->getCalls()]");
    }
    $pdo = null;

    $num_rows = $result->rowCount();

    if ($num_rows == 0) {
        echo '
        <li class="collection-item">
        <div class="flow-text"> No Active Calls. </div>
        </li>';
    } else {
        $counter = 0;
        foreach ($result as $row) {
            echo '
            <li class="collection-item">
                <div> Call Title
                    <div class="chip">
                        Unit ID
                    </div>
                    <a href="#!" class="btn">Assign</a>
                    <a href="#!" class="btn">Info</a>
                    <a href="#!" class="btn">Clear</a>
                </div>
            </li>';
            $counter++;
        }
    }
}

function searchPlate()
{
    $toSearch = $_POST['licensePlate'];
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        die("Could not connect to MySQL. [new.php->searchPlate()]" . $ex);
    }
    $stmt = $pdo->prepare("SELECT * FROM oc_ncic_plates WHERE veh_plate = ? LIMIT 1");
    $resStatus = $stmt->execute(array($toSearch));
    $result = $stmt;
    if (!$resStatus) {
        die("An error occured while search for that plate.");
    }
    $plate = $result->fetch(PDO::FETCH_ASSOC);
    if ($result->rowCount() == 0) {
        echo "PLATE NOT FOUND";
        die();
    }
    $driver = getVehicleOwner($plate);
    $licensePlate = $plate["veh_plate"];
    $driverName = $driver["name"];
    $vehMake = $plate["veh_make"];
    $vehModel = $plate["veh_model"];
    $vehColor = $plate["veh_pcolor"];
    $vehIns = $plate["veh_insurance"];
    echo "
    <table>
        <thead>
          <tr>
              <th>Plate</th>
              <th>Driver</th>
              <th>Make</th>
              <th>Model</th>
              <th>Color</th>
              <th>Insurance</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>$licensePlate</td>
            <td>$driverName</td>
            <td>$vehMake</td>
            <td>$vehModel</td>
            <td>$vehColor</td>
            <td>$vehIns</td>
          </tr>
        </tbody>
      </table>
    ";
}

function getVehicleOwner($plate)
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    } catch (PDOException $ex) {
        die("Could not connect to MySQL. [new.php->getVehicleOwner()]" . $ex);
    }
    $driverID = $plate["name_id"];
    $stmt = $pdo->prepare("SELECT * FROM oc_ncic_names WHERE id = ? LIMIT 1");
    $resStatus = $stmt->execute(array($driverID));
    $result = $stmt;
    if (!$resStatus) {
        die("An error occured while search for that plate owner.");
    }
    $driver = $result->fetch(PDO::FETCH_ASSOC);
    return $driver;
}