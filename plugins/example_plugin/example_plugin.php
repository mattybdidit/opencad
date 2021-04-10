<?php 

// Require any additional plugins/libraries you need here
require_once("../plugin_api/plugin_api.php");

/**
 * An example plugin created & provided with OpenCAD
 * @since Azazel 0.2
 */
class ExamplePlugin { // Declare your plugin name, must not already be taken by another plugin.
    
    public $PluginInformation = array(); // Required for the below information to be set/get
    public $PluginAPI;

    function __construct() { // Ran once a new ExamplePlugin class is created, not required unless needed
       $this->PluginAPI = new PluginApi();
    }
     
    function getplugininformation() { // Required
        $this->pluginInformation['plugin_name'] = "Example Plugin (built-in)"; // your plugin name
        $this->pluginInformation['version'] = "1.0"; // your plugin version
        $this->pluginInformation['authors'] = "Matt4499"; // your plugin author(s)
        $this->pluginInformation['description'] = "An example plugin provided for beginners."; // short one line description of what it is/does
        $this->pluginInformation['icon'] = "fas fa-shield-alt"; // font awesome icon  https://fontawesome.com/icons
        $this->PluginInformation['supportURL'] = "https://discord.gg/eS9mz6R"; // Where can a user contact you with questions/problems about the plugin?
        return $this->pluginInformation;
    }

    function uninstall_plugin($uninstaller) { // Required
        // Audit log who uninstalled it, and remove the file
        // Remove any changes to the DB etc. you've made when installing
        $this->PluginAPI->audit_log($uninstaller . " removed " . $this->PluginInformation['plugin_name']); // Use PluginAPI to audit log who uninstalled it
        unlink(__FILE__);
    }

    function install_plugin($installer) { // Required
        // Audit log who installed it and make any changes neccesary.
        $this->PluginAPI->audit_log($installer . " installed " . $this->PluginInformation['plugin_name']); // Use PluginAPI to audit log who installed it
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