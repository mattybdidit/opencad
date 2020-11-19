<?php
require_once (__DIR__ . "/../oc-config.php");
if (extension_loaded('curl') == 0)
{
    die("Discord Webhook requires the CURL extension to be loaded.");
}
function sendWebhook($message, $type)
{
    if ($message && $type)
    {
        $message = htmlspecialchars($message);
        $type = htmlspecialchars($type);
        $embed = json_encode(["content" => "Text: $message, Type: $type"], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $ch = curl_init();

        curl_setopt_array($ch, [CURLOPT_URL => DISCORD_WEBHOOK_URL, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $embed, CURLOPT_HTTPHEADER => ["Content-Type: application/json"]]);

        $response = curl_exec($ch);
        curl_close($ch);
        if ($response == 0)
        {
            $_SESSION['error'] = "An internal error occured. Please contact an admin.";
            $_SESSION['error_blob'] = "discord webhook error while trying to send it";
            header('Location: ' . BASE_URL . '/plugins/error/index.php');
            die();
        }
    } else
    {
        $_SESSION['error'] = "An internal error occured. Please contact an admin.";
        $_SESSION['error_blob'] = "Message & Type are null in discord webhook";
        header('Location: ' . BASE_URL . '/plugins/error/index.php');
        die();
    }
}
?>
