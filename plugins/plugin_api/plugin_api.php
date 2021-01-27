<?php 

class PluginApi {
    
    public $PluginInformation = array();

    //function __construct() {
    //    include_once("oc-config.php");
    //}
     
    function getplugininformation() {
        $this->pluginInformation['plugin_name'] = "PluginAPI (built-in)";
        $this->pluginInformation['version'] = "1.0.0";
        $this->pluginInformation['authors'] = "Matt4499";
        $this->pluginInformation['description'] = "Adds a plugin API for other plugins to use, and some built-in OpenCAD features.";
        $this->pluginInformation['icon'] = "fas fa-shield-alt";
        return $this->pluginInformation;
    }

    function uninstall_plugin() {
        die("You cannot uninstall PluginApi as it is built into OpenCAD.");
        //To-Do: Delete entire directory, disable/remove stuff from db, that the plugin uses, audit log the uninstall and who did it
    }

    function install_plugin() {
        //To-Do: make changes to DB as plugin needs, audit log the install, and who installed it
    }

    function audit_log($text) {
        if(is_null($text)) { error_log("[pluginapi/audit_log] text was null!"); return; }
        $date = date("F j, Y, g:i a T");
        $id = null;

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

    function get_oc_version_name() {
        echo 'Azazel';
    }
    
    function get_oc_version_build() {
        echo '0.1';
    }
}


?>