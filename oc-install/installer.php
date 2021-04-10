<?php
if (extension_loaded('curl') == 0 or extension_loaded('pdo') == 0) {
	die("Curl or PDO extension is not loaded, but is required.");
}
if (isset($_POST['username'])) {
	initCSS();
	install();
}
function initCSS() {
	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css" integrity="sha512-3EJR9sB4iaYgCrAwCZORkxD926jIG+AK+6xe8UGUf7DjQG+tujoYW4mbRm+nJ5D8xCobkMP/tiAWmQI0ahS3Bw==" crossorigin="anonymous" />';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js" integrity="sha512-9913Cd4sl7mfl9B7Pq0V+lgEMtJ/exBNmFToydOzkrQDFdQkjRRNGHn31crD9zFqJxWhcJwSvWNkz/a/7Qyaxw==" crossorigin="anonymous"></script>';
}
function install()
{
	$thisURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$BaseURL = substr($thisURL, 0, -25);
	try {
		$dbhost = $_POST['host'];
		$dbname = $_POST['database'];
		$dbuser = $_POST['username'];
		$dbpassword = $_POST['password'];
		$db = new PDO('mysql:host=' . $dbhost, $dbuser, $dbpassword);
		$db->query("CREATE DATABASE IF NOT EXISTS $dbname");
		$db->query("use $dbname");
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
		$sql = file_get_contents("installer.sql");
		if($db->exec($sql) === false){
			echo "<script> $(function(){ M.toast({html: 'An error occured while installing!', classes: 'red darken-4'}); });</script>";
			die();
		} else {
			$stmt = $db->prepare("UPDATE oc_config SET svalue = :yes WHERE skey = 'base_url'");
			$stmt->bindValue(':yes', $BaseURL);
			$result = $stmt->execute();
			if ($result == false) {
				echo "<script> $(function(){ M.toast({html: 'An error occured while setting the new base URL', classes: 'red darken-4'}); });</script>";
				die();
			} else {
				$file_contents = file_get_contents("../oc_config.php");
				$input = "define('DB_HOST', '$dbhost');
				define('DB_USER', '$dbuser');
				define('DB_PASSWORD', '$dbpassword');
				define('DB_NAME', '$dbname');
				define('DB_PREFIX', 'oc_');";
				str_replace("%", $input, $file_contents);
				file_put_contents("../oc-config.php", $file_contents);
				header("Location: $BaseURL");
				unlink("css.css");
				unlink("installer.sql");
				unlink(__FILE__);
			}
		}
	} catch (PDOException $e) {
		$error = $e->getMessage();
		$cleanerror = filter_var($error, FILTER_SANITIZE_STRING);
		$WrongPassword = "(using password: YES)";
		$PasswordRequire = "(using password: NO)";
		if (strpos($error, $WrongPassword)) {
			echo "<script> $(function(){ M.toast({html: 'You entered an incorrect MySQL password!', classes: 'red darken-4'}); });</script>";
			echo "<script> $(function(){ M.toast({html: '".$cleanerror."', classes: 'red darken-4'}); });</script>";
			exit();
		}
		if(strpos($error, $PasswordRequire)){
			echo "<script> $(function(){ M.toast({html: 'A MySQL password is required but was not supplied.', classes: 'red darken-4'}); }); </script>";
			echo "<script> $(function(){ M.toast({html: '".$cleanerror."', classes: 'red darken-4'}); });</script>";
			exit();
		}
		echo "<script> $(function(){ M.toast({html: '".$cleanerror."', classes: 'red darken-4'}); });</script>";
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
		<form id="testmysql" method="post" action="installer.php">
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