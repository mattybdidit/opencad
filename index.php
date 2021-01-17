<?php
	$DEV = false;
	if(!$DEV && file_exists(getcwd().'/oc-install/installer.php') && is_writable(getcwd())){
		if(session_status() === PHP_SESSION_NONE) session_start();
		session_unset();
		session_destroy();
		setcookie('aljksdz7', null, -1, "/");
		header('Location://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'oc-install/installer.php');
	}
	if(!file_exists(getcwd().'/oc-install/installer.php') && is_dir("oc-install") && !$DEV){
		 rmdir("oc-install");
	}

	require_once(__DIR__ . "/oc-config.php");
	require_once(__DIR__ . "/actions/register.php");
	require_once(__DIR__ . "/actions/publicFunctions.php");

	if(session_status() === PHP_SESSION_NONE) session_start();

	if ( (isset($_SESSION['logged_in'])) == "YES" )
	{
	  header ('Location: ./dashboard.php');
	}
	if(LOGIN_CAPTCHA_ENABLED) {
		include("plugins/captcha/simple-php-captcha.php");
		$_SESSION['captcha'] = simple_php_captcha();
	}
	if (isset($_GET['loggedOut']))
	{
	  $loginMessage = "<script> $(function(){ M.toast({html: 'You are now logged out!', classes: 'green accent-4'}); });</script>";
   }
	if(isset($_SESSION['register_error']))
	{
	  $registerError = "<script> $(function(){ M.toast({html: '${$_SESSION['register_error']}', classes: \'red darken-4\'}); });</script>";
		unset($_SESSION['register_error']);
	}
	if(isset($_SESSION['loginMessageDanger']))
	{
	  $loginMessage = sprintf("<script> $(function(){ M.toast({html: '%s', classes: 'red darken-4'}); });</script>", $_SESSION['loginMessageDanger']);
	  unset($_SESSION['loginMessageDanger']);
	}
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
      <link rel="stylesheet" href="./css/login.css">
   </head>
   <body>
      <div class="section">
         <h1 class="center-align"> OpenCAD | Login </h1>
		 <?php 
		 if ($DEV) {
		 ?>

		<h2 class="center-align error-text"> <em> !! DEV MODE !! </em> </h2>	

		 <?php 
		 }
		 ?>
         <form action="./actions/login.php" method="post">
         <div class="row">
            <div class="input-field col offset-s4 s4">
               <input name="email" id="email" type="email" class="validate" required>
               <label for="email">Email</label>
            </div>
         </div>
         <div class="row">
            <div class="input-field col offset-s4 s4">
               <input name="password" id="password" type="password" class="validate" required>
			   <label for="password">Password</label>
			   
			   <?php if(!LOGIN_CAPTCHA_ENABLED) { ?>
			   		<input type="submit" class="btn red darken-4" value="Login">
               		<a class="btn red darken-4" onclick="M.toast({html: 'Contact an admin to reset your password.', classes:'red darken-4'});" >Forgot Password?</a>
			   		<a class="btn red darken-4 modal-trigger" href="#register">New? Register</a>
			   <?php } ?>


            </div>
		 </div>
		 <?php if(LOGIN_CAPTCHA_ENABLED) { ?>
			<div class="row">
            <div class="input-field col offset-s4 s4">
			
			<?php 
				
				$captcha_img = sprintf("<img src='%s' style='padding-left: 25vh;'></img>", $_SESSION['captcha']['image_src']);
				echo $captcha_img;

				?>
			
            </div>
		 </div>
			<div class="row">
            <div class="input-field col offset-s4 s4">
               <input name="captcha" id="captcha" type="text" class="validate" required>
			   <label for="captcha">Required Captcha (case sensitive)</label>

			   <input type="submit" class="btn red darken-4" value="Login">
               <a class="btn red darken-4" onclick="M.toast({html: 'Contact an admin to reset your password.', classes:'red darken-4'});" >Forgot Password?</a>
               <a class="btn red darken-4 modal-trigger" href="#register">New? Register</a>
            </div>
		 </div>
		<?php } ?>
        </form>
      </div>
      <div id="register" class="modal">
    <div class="modal-content">
      <h4>OpenCAD | Register</h4>
      <form action="<?php echo BASE_URL; ?>/actions/register.php" method="post">
      <div class="input-field col s12">
						<input class="form-control" placeholder="Name" name="uname" type="text" required>
					 </div>
					 <div class="input-field col s12">
						<input class="form-control" placeholder="Email" name="email" type="email" required>
					 </div>
					 <div class="input-field col s12">
						<input class="form-control" placeholder="Identifier (1K24, 124, ETC.)" name="identifier" type="text" required>
					 </div>
					 <div class="input-field col s12">
						<input class="form-control" placeholder="Password" name="password" type="password" required>
					 </div>
					 <!-- ./ form-group -->
					 <div class="input-field col s12">
						<input class="form-control" placeholder="Confirm Password" name="password1" type="password" required>
					 </div>
					 <!-- ./ form-group -->
					 <div class="input-field col s12">
						<label>Division (Select all that apply)</label>
						<select id="division" name="division[]" multiple required>
							<?php getDataSetTable($dataSet = "departments", $column1 = "department_id", $column2 = "department_long_name", $leadTrim = 17, $followTrim = 11, $isRegistration = true, $isVehicle = false); ?>
						</select>
					 </div>
					 <div class="input-field col s12">
						<input name="register" type="submit" class="btn red darken-4" value="Request Access" />
					 </div>
      </form>
    </div>
  </div>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
      <script>
          $(document).ready(function(){
             $('.modal').modal();
             $('select').formSelect();
          });
      </script>
      <?php
      
      if(isset($_GET['register']))
	    {
            if($_GET['register'] == "success"){
                echo "<script> M.toast({html: 'Successfully registered! Please wait for approval.', classes: 'green accent-4'}); </script>";
			}
			if($_GET['register'] == "emailExists"){
				echo "<script> M.toast({html: 'That email is already taken!', classes: 'red darken-4'}); </script>";
			}
			if($_GET['register'] == "passwordMatch") {
				echo "<script> M.toast({html: 'Passwords did not match!', classes: 'red darken-4'}); </script>";
			}
		}

		if(isset($loginMessage)) { 
			echo $loginMessage;
		}
		if(isset($registerError)) { 
		 echo $registerError;
		}
		if(isset($registerError)) { 
		 echo $registerError;
		}
      ?>
   </body>
</html>