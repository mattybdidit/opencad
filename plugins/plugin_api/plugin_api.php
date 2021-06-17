<?php
class PluginApi
{

    public $PluginInformation = array();
    public $DB;

    function getplugininformation()
    {
        $this->pluginInformation['plugin_name'] = "PluginAPI (built-in)";
        $this->pluginInformation['version'] = "1.0.0";
        $this->pluginInformation['authors'] = "Matt4499";
        $this->pluginInformation['description'] = "Adds a plugin API for other plugins to use, and some built-in OpenCAD features.";
        $this->pluginInformation['icon'] = "fas fa-shield-alt";
        return $this->pluginInformation;
    }

    function audit_log($text)
    {
        if (is_null($text)) {
            error_log("[pluginapi/audit_log] text was null!");
            return;
        }
        $date = date("F j, Y, g:i a T");
        $id = null;

        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

        $stmt = $pdo->prepare("INSERT INTO " . DB_PREFIX . "logs (id, text, time) VALUES (?, ?, ?)");
        $result = $stmt->execute(array($id, $text, $date));

        if (!$result) {
            die($stmt->errorInfo());
        }
    }

    function get_db()
    {
        if ($this->DB instanceof PDO) {
            return $this->DB;
        }
        try {
            $this->DB = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        return $this->DB;
    }

    function get_oc_version_name()
    {
        echo 'Azazel';
    }

    function get_oc_version_build()
    {
        echo '0.3';
    }

    function get_oc_version_build_date()
    {
        echo 'June 17th 2021 @ 7:15AM CST';
    }

    function get_oc_version_changes()
    {
        return array(
            "Updated to Azazel 0.3",
            "\"Admin\" page redesigned & renamed to staff, will move more stuff there in future",
            "Centered the clock in panel on dashboard",
            "Made newer page redesigns match",
            "Re-enabled version being shown on the login page",
            "More soon! Thank you for supporting my fork of OpenCAD!",
        );
    }
}