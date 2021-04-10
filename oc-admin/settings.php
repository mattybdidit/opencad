<?php 
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
   include("../plugins/plugin_api/plugin_api.php");

   $PluginAPI = new PluginApi();
   
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

   if(isset($_GET['change_setting'], $_GET['setting_name'], $_GET['setting_value'])) {
      change_setting($_GET["setting_name"], $_GET["setting_value"]);
   }

   function change_setting($name, $status) { 
      $pdo = $PluginAPI->get_db();
      if(!is_null($pdo)) {
         $stmt = $pdo->prepare("UPDATE ".DB_PREFIX."config SET	svalue = ? WHERE skey = ?");
         if ($stmt->execute(array($name, $status))) {
            $pdo = null;
            $_SESSION["successMessage"] = "changed the setting!";
            header("Refresh:0");
         } else {
             die($stmt->errorInfo());
         }
      $pdo = null;
      }
   }

   if(isset($_SESSION['successMessage']))
	{
	  $successMessage = sprintf("<script> $(function(){ M.toast({html: '%s', classes: 'green accent-4'}); });</script>", $_SESSION['successMessage']);
	  unset($_SESSION['successMessage']);
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
                              <h2>OpenCAD Settings (LIST INCOMPLETE. SETTINGS LIST/CHANGING IN DEVELOPMENT) </h2>
                              <ul class="nav navbar-right panel_toolbox">
                                    <button disabled class="btn disabled" style="background-color: #2A3F54; color: white;" onclick="refreshSettings()">Apply</button>
                              </ul>
                     <table class="table table-striped">
                        <thead>
                           <tr>
                              <th scope="col">Setting</th>
                              <th scope="col">Value</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>Debug Mode</td>
                              <td>
                                 <label class="switch">
                                 <input type="checkbox" <?php if (OC_DEBUG) echo 'checked'; ?>>
                                 <span class="slider round"></span>
                                 </label>
                              </td>
                           </tr>
                           <tr>
                              <td>Community Name</td>
                              <td>
                                 <input type="text" value="<?php echo COMMUNITY_NAME; ?>"">
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     </div>
                              <!-- ./ x_content -->
                           </div>
                           <!-- ./ x_panel -->
                        </div>
                        <!-- ./ col-md-12 col-sm-12 col-xs-12 -->
                     </div>
                     <!-- ./ row -->

                     <div class="clearfix"></div>

                     <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                           <div class="x_panel">
                              <div class="x_title">
                                 <h2>Currently Enabled Plugins</h2>
                                 <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fas fa-chevron-up"></i></a>
                                    </li>
                                    <li><a class="close-link"><i class="fas fa-close"></i></a>
                                    </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>
                              <!-- ./ x_title -->
                              <div class="x_content">
                              <table class="table table-striped">
                        <thead>
                           <tr>
                              <th scope="col">Name</th>
                              <th scope="col">Version</th>
                              <th scope="col">Authors</th>
                              <th scope="col">Description</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td scope="col"> <span class="<?php echo $PluginAPI->getplugininformation()['icon']; ?>"></span> <?php echo $PluginAPI->getplugininformation()['plugin_name']; ?></td>
                              <td><?php echo $PluginAPI->getplugininformation()['version']; ?></td>
                              <td><?php echo $PluginAPI->getplugininformation()['authors']; ?></td>
                              <td><?php echo $PluginAPI->getplugininformation()['description']; ?></td>
                           </tr>
                        <?php if(LOGIN_CAPTCHA_ENABLED) { ?>
                           <tr>
                              <td scope="col"> <span class="<?php echo $SimpleCaptchaPlugin->getplugininformation()['icon']; ?>"></span> <?php echo $SimpleCaptchaPlugin->getplugininformation()['plugin_name']; ?></td>
                              <td><?php echo $SimpleCaptchaPlugin->getplugininformation()['version']; ?></td>
                              <td><?php echo $SimpleCaptchaPlugin->getplugininformation()['authors']; ?></td>
                              <td><?php echo $SimpleCaptchaPlugin->getplugininformation()['description']; ?></td>
                           </tr>
                        <?php } ?>
                        </tbody>
                     </table>
                              </div>
                              <!-- ./ x_content -->
                           </div>
                           <!-- ./ x_panel -->
                        </div>
                        <!-- ./ col-md-12 col-sm-12 col-xs-12 -->
                     </div>
                     <!-- ./ row -->
                     <div class="clearfix"></div>
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
         include(__DIR__ . "/../oc-includes/jquery-colsolidated.inc.php"); 

         if(!is_null($successMessage)){
            echo $successMessage;
         }

      ?>
   </body>
</html>