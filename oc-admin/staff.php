<?php
session_start();

include_once("../oc-config.php");
include_once("../oc-functions.php");
include_once("../actions/adminActions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo COMMUNITY_NAME; ?> | ADMIN</title>
    <link rel="shortcut icon" type="image/jpg" href="./favicon.ico" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="./css/cad.css" />
</head>

<body>
    <ul class="sidenav sidenav-fixed">
        <li>
            <div class="user-view">
                <img class="circle" src="<?php echo get_avatar(); ?>" draggable="false" />
                <p>Welcome <?php echo $_SESSION['name']; ?></p>
            </div>
        </li>
        <li><a href="../dashboard.php" class="btn red darken-3">DASHBOARD</a></li>
        <li><a href="../actions/signout.php" class="btn red darken-3">LOGOUT</a></li>
        <li>
            <div class="divider"></div>
        </li>
    </ul>
    <div class="row">
        <div class="col s9 offset-s2">
            <h4> <?php echo COMMUNITY_NAME . " | Staff"; ?> </h4>
            <em>This page is super useless and is literally in the original OpenCAD. This will be changed and
                all staff related things will be moved here in the future.</em>
            <div class="card horizontal">
                <div class="card-stacked">
                    <div class="card-content">
                        <span class="card-title">User Count</span>
                        <ul class="collection">

                            <li class="collection-item">
                                All Users
                                <div class="chip">
                                    <?php echo getUserCount(); ?>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>