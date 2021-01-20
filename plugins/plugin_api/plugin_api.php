<?php 

class PluginApi {

    function audit_log($text) {
        $date = date("Y:m:d H:i:s");
        $id = null;
        include_once("oc-config.php");

        try{
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
        } catch(PDOException $ex)
        {
            die($ex->getMessage());
        }

        $stmt = $pdo->prepare("INSERT INTO ".DB_PREFIX."logs (id, text, time) VALUES (?, ?, ?)");
        $result = $stmt->execute(array($id, $text, $date));

        if (!$result) {
            die($stmt->errorInfo());
        }
    
        
    }

    function get_db() {
        try{
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
        } catch(PDOException $ex)
        {
            die($ex->getMessage());
        }
        return $pdo;
    }

}


?>