<?php 
if(extension_loaded('curl') == 0 or extension_loaded('pdo') == 0){
	die("Curl or PDO extension is not loaded, but is required.");
}
if(isset($_POST['username'])){
	testMySQL();
}
function testMySQL() {
	try {
		$db = new PDO('mysql:host='.$_POST['host'].';dbname='.$_POST['database'], $_POST['username'], $_POST['password']);
		echo "<h1> MySQL is good! </h1>";
		$db = null;
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}
?>
<html>
<body>
<form id="testmysql" method="post" action="start.php">
  <input type="text" id="host" name="host" placeholder="MySQL Host/IP"><br> 
  <input type="text" id="username" name="username" placeholder="MySQL Username"><br>
  <input type="password" id="password" name="password" placeholder="MySQL Password"><br>
  <input type="text" id="database" name="database" placeholder="MySQL Database Name"><br>
  <input type="submit" value="Setup">
</form>
</body>
</html>