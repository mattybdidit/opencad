<?php
if (extension_loaded('curl') == 0 or extension_loaded('pdo') == 0) {
	die("Curl or PDO extension is not loaded, but is required.");
}
if (isset($_POST['username'])) {
	testMySQL();
}
function testMySQL()
{
	try {
		$db = new PDO('mysql:host=' . $_POST['host'], $_POST['username'], $_POST['password']);
		echo "<h1> Beginning Installation... </h1>";
		$db->exec("CREATE DATABASE IF NOT EXISTS `opencad`;") or die(print_r($db->errorInfo(), true));
		$db = null;
	} catch (PDOException $e) {
		$error = $e->getMessage();
		$WrongPassword = "(using password: YES)";
		if (strpos($error, $WrongPassword)) {
			echo 'You entered the wrong MySQL password! No access to database. <br>';
			echo $error;
			die();
		}
		echo $error;
		die();
	}
}
?>
<html>

<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>

<body>
	<div class="container">
		<h1>OpenCAD Installer</h1>
		<p> This will install OpenCAD with default settnigs, that you can change later </p>
		<form id="testmysql" method="post" action="start.php">
			<input type="text" id="host" name="host" placeholder="MySQL Host/IP"><br>
			<input type="text" id="username" name="username" placeholder="MySQL Username"><br>
			<input type="password" id="password" name="password" placeholder="MySQL Password"><br>
			<p>Database Name (default opencad)</p><input type="text" id="database" name="database" placeholder="MySQL Database Name" value="opencad"><br>
			<input type="submit" value="Setup">
		</form>
	</div>
</body>

</html>