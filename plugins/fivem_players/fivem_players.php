<?php

require_once("../plugin_api/plugin_api.php");
$PluginAPI = new PluginApi();

if (isset($_POST["PlayerJoin"])) {
    $id = htmlspecialchars($_POST["id"]);
    $name = htmlspecialchars($_POST["name"]);
    if (!isset($id) || !isset($name)) {
        http_response_code(418);
        die("no data was input");
    }
    $toAdd = array(
        'name' => $name,
        'id' => $id,
    );

    $json = file_get_contents('players.json');
    $data = json_decode($json, TRUE);

    array_push($toAdd, $data);
    $fp = fopen('players.json', 'w+');
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    fclose($fp);
    echo "player added to player.json!";
    http_response_code(200);
    die();
}
if (isset($_POST["PlayerLeave"])) {
}

class FiveMPlayerlist
{
    function getplugininformation()
    {
        $this->pluginInformation['plugin_name'] = "FiveM Playerlist";
        $this->pluginInformation['version'] = "1.0.0";
        $this->pluginInformation['authors'] = "Matt4499";
        $this->pluginInformation['description'] = "Adds a FiveM playerlist to your CAD.";
        $this->pluginInformation['icon'] = "fas fa-list-alt";
        return $this->pluginInformation;
    }
}