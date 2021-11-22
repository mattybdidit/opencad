<?php
session_start();
require_once("./oc-config.php");
require_once("./actions/generalActions.php");
require_once("./plugins/plugin_api/plugin_api.php");
$PluginAPI = new PluginApi();

if (empty($_SESSION['logged_in'])) {
    header('Location: ./index.php');
    die("Not logged in");
}
setDispatcher("1");


$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$link) {
    die('Could not connect to database.');
}

$id = $_SESSION['id'];
$sql = "SELECT * from oc_user_departments WHERE user_id = \"$id\"";
$getAdminPriv = "SELECT `admin_privilege` from oc_users WHERE id = \"$id\"";

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
        $_SESSION['civilian'] = 'YES';
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
                <div>Tow <a href="./civ/tow.php" class="secondary-content">
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
            <div class="animate__animated animate__fadeInDown center" style="width: 44vh !important;">
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
                <?php
                $curVersion = $PluginAPI->get_oc_version_build();
                $newVersion = file_get_contents("https://raw.githubusercontent.com/Matt4499/opencad/master/version.txt");
                if ($newVersion > $curVersion) {
                    echo "<br> <h4><a href='https://github.com/Matt4499/opencad'>OpenCAD has new updates!</a></h4>" . "<br>" . "Current version: $curVersion" . "<br>" . "New version: $newVersion";
                }
                if ($newVersion == $curVersion) {
                    echo "<br> <h4>OpenCAD is up to date! (MASTER)</h4>";
                }
                if ($curVersion > $newVersion) {
                    echo "<br> <h4>OpenCAD is up to date! (DEV)</h4>";
                }
                ?>
            </div>
        </div>
    </div>

    <div class="container center animate__animated animate__fadeInDown">
        <h3>OpenCAD Changelog (this version)</h3>
        <p> OpenCAD is open source. This version is maintained by <a href="https://github.com/Matt4499/opencad">Matt4499</a></p>

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