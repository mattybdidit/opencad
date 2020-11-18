<?php

include_once (__DIR__ . "/oc-config.php");
$sqlite3version = $sqlite3db->querySingle('SELECT SQLITE_VERSION()');

?>
<html>
<body>
<h1> OpenCAD SQLITE3 Debug Info </h1>
<p> Sqlite3 Verison: <?php echo $sqlite3version; ?> </p>
</body>
</html>