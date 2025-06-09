<?php
header('Content-Type: application/json');

$channelID = "2964228";
$readAPIKey = "";
$results = 1;

$url = "https://api.thingspeak.com/channels/2964228/feeds.json?results=1";
if (!empty($readAPIKey)) {
    $url .= "&api_key=$readAPIKey";
}

$response = file_get_contents($url);

if ($response === FALSE) {
    echo json_encode(["error" => "Errore nella richiesta a ThingSpeak."]);
    exit;
}

$data = json_decode($response, true);
$feed = $data["feeds"][0] ?? null;

if ($feed) {
    echo json_encode([
        "timestamp" => $feed["created_at"],
        "field2" => floatval($feed["field2"]),
        "field4" => floatval($feed["field4"])
    ]);
} else {
    echo json_encode(["error" => "Nessun dato disponibile."]);
}
