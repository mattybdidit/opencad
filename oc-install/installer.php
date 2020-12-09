<?php
if (extension_loaded('curl') == 0 or extension_loaded('pdo') == 0) {
	die("Curl or PDO extension is not loaded, but is required.");
}
if (isset($_POST['username'])) {
	initCSS();
	testMySQL();
}
function initCSS() {
	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css" integrity="sha512-3EJR9sB4iaYgCrAwCZORkxD926jIG+AK+6xe8UGUf7DjQG+tujoYW4mbRm+nJ5D8xCobkMP/tiAWmQI0ahS3Bw==" crossorigin="anonymous" />';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js" integrity="sha512-9913Cd4sl7mfl9B7Pq0V+lgEMtJ/exBNmFToydOzkrQDFdQkjRRNGHn31crD9zFqJxWhcJwSvWNkz/a/7Qyaxw==" crossorigin="anonymous"></script>';
}
function testMySQL()
{
	echo "<script> $(function(){ M.toast({html: 'Starting Installation!', classes: 'green accent-4'}); });</script>";
	try {
		$db = new PDO('mysql:host=' . $_POST['host'], $_POST['username'], $_POST['password']);
		$db->exec("CREATE DATABASE IF NOT EXISTS ".$_POST['username'].";") or die(print_r($db->errorInfo(), true));
		$db = null;
	} catch (PDOException $e) {
		$error = $e->getMessage();
		$cleanerror = filter_var($error, FILTER_SANITIZE_STRING);
		$WrongPassword = "(using password: YES)";
		$PasswordRequire = "(using password: NO)";
		if (strpos($error, $WrongPassword)) {
			echo "<script> $(function(){ M.toast({html: 'You entered an incorrect MySQL password!', classes: 'red darken-4'}); });</script>";
			echo "<script> $(function(){ M.toast({html: '".$cleanerror."', classes: 'red darken-4'}); });</script>";
			// echo $error;
		}
		if(strpos($error, $PasswordRequire)){
			echo "<script> $(function(){ M.toast({html: 'A MySQL password is required but was not supplied.', classes: 'red darken-4'}); }); </script>";
			echo "<script> $(function(){ M.toast({html: '".$cleanerror."', classes: 'red darken-4'}); });</script>";
			// echo $error;
		}
	}
}
?>
<html>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css" integrity="sha512-3EJR9sB4iaYgCrAwCZORkxD926jIG+AK+6xe8UGUf7DjQG+tujoYW4mbRm+nJ5D8xCobkMP/tiAWmQI0ahS3Bw==" crossorigin="anonymous" />
<link rel="stylesheet" href="./css.css">
</head>

<body>
	<div class="container">
		<h1>OpenCAD Installer</h1>
		<p> This will install OpenCAD with default settings, that you can change later </p>
		<form id="testmysql" method="post" action="start.php">
			<input type="text" id="host" name="host" placeholder="MySQL Host/IP"><br>
			<input type="text" id="username" name="username" placeholder="MySQL Username"><br>
			<input type="password" id="password" name="password" placeholder="MySQL Password"><br>
			<p>Database Name (default opencad) (some hosts wont allow you to create a new database, so if you're using a host use your existing one here)</p><input type="text" id="database" name="database" placeholder="MySQL Database Name" value="opencad"><br>
			<input type="submit" class="btn red darken-4" value="Setup">
		</form>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js" integrity="sha512-9913Cd4sl7mfl9B7Pq0V+lgEMtJ/exBNmFToydOzkrQDFdQkjRRNGHn31crD9zFqJxWhcJwSvWNkz/a/7Qyaxw==" crossorigin="anonymous"></script>
</body>

</html>