<?php 
session_start();

include_once(__DIR__ . "/oc-config.php");
include_once(__DIR__ . "/oc-functions.php");
include(__DIR__ . "/actions/generalActions.php");
include(__DIR__ . "/actions/publicFunctions.php");
include(__DIR__ . "/actions/dispatchActions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>OpenCAD | CAD</title>
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
    <main>
        <ul class="sidenav sidenav-fixed">
            <li>
                <div class="user-view">
                    <img class="circle" src="<?php echo get_avatar(); ?>" draggable="false" />
                    <p>Welcome <?php echo $_SESSION['name']; ?></p>
                </div>
            </li>
            <li><a href="./dashboard.php" class="btn">DASHBOARD</a></li>
            <li><a href="./actions/signout.php" class="btn red darken-3">LOGOUT</a></li>
            <li><a href="#!" class="btn">Stopwatch/Timer</a></li>
            <li>
                <div class="divider"></div>
            </li>
            <li>
                <a class='dropdown-trigger btn' href='#' data-target='warnings'>Warnings</a>
                <ul id='warnings' class='dropdown-content'>
                    <li><a href="#!">Write Warning</a></li>
                    <li><a href="#!">View Warning</a></li>
                </ul>
            </li>
            <li>
                <a class='dropdown-trigger btn' href='#' data-target='citations'>Citations</a>
                <ul id='citations' class='dropdown-content'>
                    <li><a href="#!">Write Citation</a></li>
                    <li><a href="#!">View Citation</a></li>
                </ul>
            </li>

        </ul>
        <div class="container">

            <div class="row">
                <div class="col s12">
                    <div class="card horizontal">
                        <div class="card-stacked">
                            <div class="card-content">
                                <span class="card-title">Active Calls</span>
                                <ul class="collection">
                                    <li class="collection-item">
                                        <div>10-80 // PURSUIT

                                            <div class="chip">
                                                127
                                            </div>

                                            <div class="chip">
                                                181
                                            </div>

                                            <div class="chip">
                                                124
                                            </div>

                                            <a href="#!" class="btn">Assign</a>
                                            <a href="#!" class="btn">Info</a>
                                            <a href="#!" class="btn">Clear</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <script src="./js/cad.js"></script>
</body>

</html>