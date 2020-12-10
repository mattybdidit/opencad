<?php
	if(file_exists(getcwd().'/oc-install/installer.php') && is_writable(getcwd())){
		header('Location://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'oc-install/installer.php');
	}
	if(!file_exists(getcwd().'/oc-install/installer.php')){
		 rmdir("oc-install");
	}

	require_once(__DIR__ . "/oc-config.php");
	require_once(__DIR__ . "/actions/register.php");
	require_once(__DIR__ . "/actions/publicFunctions.php");

	session_start();
	
	if ( (isset($_SESSION['logged_in'])) == "YES" )
	{
	  header ('Location: ./dashboard.php');
	}
	if (isset($_GET['loggedOut']))
	{
	  $loginMessage = '<div class="alert alert-success" style="text-align: center;" ><span>You\'ve successfully been logged out</span></div>';
   }
	if(isset($_SESSION['register_error']))
	{
	  $registerError = '<div class="alert alert-danger" style="text-align: center;"><span>'.$_SESSION['register_error'].'</span></div>';
		unset($_SESSION['register_error']);
	}
	if(isset($_SESSION['loginMessageDanger']))
	{
	  $loginMessage = '<div class="alert alert-danger" style="text-align: center;"><span>'.$_SESSION['loginMessageDanger'].'</span></div>';
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
               <input type="submit" class="btn red darken-4" value="Login">
               <a class="btn red darken-4" onclick="M.toast({html: 'Contact an admin to reset your password.', classes:'red darken-4'});" >Forgot Password?</a>
               <a class="btn red darken-4 modal-trigger" href="#register">New? Register</a>
            </div>
         </div>
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
						<input class="form-control" placeholder="Identifier (Code Number, Unit ID)" name="identifier" type="text" required>
					 </div>
					 <div class="input-field col s12">
						<input class="form-control" placeholder="Password" name="password" type="password" value="<?php if($testing){echo "password";}?>" required>
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

      ?>
   </body>
</html>