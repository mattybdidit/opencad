<?php
   /**
    Open source CAD system for RolePlaying Communities.
    Copyright (C) 2017 Shane Gill
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program comes with ABSOLUTELY NO WARRANTY; Use at your own risk.
    *
    */
   
   session_start();
   
   require_once(__DIR__ . '/../oc-config.php');
   require_once(__DIR__ . '/../oc-functions.php');
   
   if (empty($_SESSION['logged_in'])) {
       header('Location: ../index.php');
       die("Not logged in");
   } else {
       $name = $_SESSION['name'];
   }
   
   if ($_SESSION['admin_privilege'] == 3) {
       if ($_SESSION['admin_privilege'] == 'Administrator') {
           //Do nothing
   
       }
   } else if ($_SESSION['admin_privilege'] == 2) {
       if ($_SESSION['admin_privilege'] == 'Moderator') {
           // Do Nothing
   
       }
   } else {
       permissionDenied();
   }
   
   include("../actions/adminActions.php");
   
   $accessMessage = "";
   if (isset($_SESSION['accessMessage'])) {
       $accessMessage = $_SESSION['accessMessage'];
       unset($_SESSION['accessMessage']);
   }
   $adminMessage = "";
   if (isset($_SESSION['adminMessage'])) {
       $adminMessage = $_SESSION['adminMessage'];
       unset($_SESSION['adminMessage']);
   }
   
   $successMessage = "";
   if (isset($_SESSION['successMessage'])) {
       $successMessage = $_SESSION['successMessage'];
       unset($_SESSION['successMessage']);
   }
   
   if(LOGIN_CAPTCHA_ENABLED) {
       include("../plugins/captcha/simple-php-captcha.php");
       $SimpleCaptchaPlugin = new SimpleCaptchaPlugin();
   }

   function get_all_logs() {
    try{
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
    } catch(PDOException $ex)
    {
        die($ex->getMessage());
    }

    $result = $pdo->query("SELECT * FROM ".DB_PREFIX."logs");

    if (!$result)
    {
        $_SESSION['error'] = $pdo->errorInfo();
        header('Location: '.BASE_URL.'/plugins/error/index.php');
        die();
    }
    $num_rows = $result->rowCount();
    $pdo = null;

    if ($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are no logs to show.</span></div>";
        
    } else {
        foreach($result as $row)
        {
            echo '
            <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
            </tr>
            ';
        }
    }
   }
   
   ?>
<!DOCTYPE html>
<html lang="en">
   <link href="settings.css" rel="stylesheet">
   <?php include "../oc-includes/header.inc.php"; ?>
   <body class="nav-md">
      <div class="container body">
         <div class="main_container">
            <div class="col-md-3 left_col">
               <div class="left_col scroll-view">
                  <div class="navbar nav_title" style="border: 0;">
                     <a href="javascript:void(0)" class="site_title"><i class="fas fa-lock"></i>
                     <span>Administrator</span></a>
                  </div>
                  <div class="clearfix"></div>
                  <!-- menu profile quick info -->
                  <div class="profile clearfix">
                     <div class="profile_pic">
                        <img src="<?php echo get_avatar() ?>" alt="..." class="img-circle profile_img">
                     </div>
                     <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?php echo $name; ?></h2>
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <!-- /menu profile quick info -->
                  <br />
                  <?php include "oc-admin-includes/sidebarNav.inc.php"; ?>
                  <!-- /menu footer buttons -->
                  <div class="sidebar-footer hidden-small">
                     <a data-toggle="tooltip" data-placement="top" title="Go to Dashboard" href="<?php echo BASE_URL; ?>/dashboard.php">
                     <span class="fas fa-clipboard-list" aria-hidden="true"></span>
                     </a>
                     <a data-toggle="tooltip" data-placement="top" title="FullScreen" onClick="toggleFullScreen()">
                     <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                     </a>
                     <a data-toggle="tooltip" data-placement="top" title="Need Help?" href="https://discord.gg/es9mz6r">
                     <span class="fas fa-info-circle" aria-hidden="true"></span>
                     </a>
                     <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?php echo BASE_URL; ?>/actions/logout.php?responder=<?php echo $_SESSION['identifier']; ?>">
                     <span class="fas fa-sign-out-alt" aria-hidden="true"></span>
                     </a>
                  </div>
                  <!-- /menu footer buttons -->
               </div>
            </div>
            <!-- top navigation -->
            <div class="top_nav">
               <div class="nav_menu">
                  <nav>
                     <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                     </div>
                     <ul class="nav navbar-nav navbar-right">
                        <li class="">
                           <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                           <img src="<?php echo get_avatar() ?>" alt=""><?php echo $name; ?>
                           <span class="fas fa-angle-down"></span>
                           </a>
                           <ul class="dropdown-menu dropdown-usermenu pull-right">
                              <li><a href="<?php echo BASE_URL; ?>/profile.php"><i class="fas fa-user pull-right"></i>My Profile</a></li>
                              <li><a href="<?php echo BASE_URL; ?>/actions/logout.php"><i class="fas fa-sign-out-alt pull-right"></i> Log Out</a></li>
                           </ul>
                        </li>
                     </ul>
                  </nav>
               </div>
            </div>
            <!-- /top navigation -->
            <!-- page content -->
            <div class="right_col" role="main">
               <div class="">
                  <div class="page-title">
                  <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                           <div class="x_panel">
                              <div class="x_title">
                              <h2>OpenCAD Logs</h2>
                     <table class="table table-striped">
                        <thead>
                           <tr>
                              <th scope="col">ID</th>
                              <th scope="col">Log</th>
                              <th scope="col">Date</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php get_all_logs(); ?>
                        </tbody>
                     </table>
                     </div>
                              <!-- ./ x_content -->
                           </div>
                           <!-- ./ x_panel -->
                        </div>
                        <!-- ./ col-md-12 col-sm-12 col-xs-12 -->
                     </div>
                  </div>
               </div>
            </div>
            <!-- footer content -->
            <footer>
               <div class=" pull-right">
                  <?php echo COMMUNITY_NAME; ?> CAD System
               </div>
               <div class="clearfix"></div>
            </footer>
            <!-- /footer content -->
         </div>
      </div>
      <?php
         include(__DIR__ . "/oc-admin-includes/globalModals.inc.php");
         include(__DIR__ . "/../oc-includes/jquery-colsolidated.inc.php"); ?>
   </body>
</html>