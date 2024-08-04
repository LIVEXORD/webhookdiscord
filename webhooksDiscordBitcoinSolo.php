<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    $requireparameters = ['wallet','walletac','workerac'];
    foreach($requireparameters as $params) {
        if(!isset($params)) {
            http_response_code(400);
            echo json_encode(array("error" => "Parameter " . $params . " not found."));
            exit();
        }
    }

    $wallet = $_GET['wallet'];
    $walletac = $_GET['walletac'];
    $workerac = $_GET['workerac'];

    $webhook_url = "https://discord.com/api/webhooks/1269240670883942471/8c0dqAHtAy5aJeXOlGtLKtoj1MH8SBLwHxjacpaPEJxeo0OM8RZ5SMtP87URWcAU0JET";
    $api_url = "https://public-pool.io:40557/api/client/" . $wallet;

    $response = file_get_contents($api_url);
    if ($response === FALSE) {
        http_response_code(400);
        echo json_encode(array("error" => "Failed to retrieve data from the Public Pool server for the wallet"));
    }

    $data = json_decode($response, true);

    date_default_timezone_set('Asia/Jayapura');
    $date = date("Y-m-d H:i:s");

    function sendToWebhook($webhook_url, $fields, $color, $date, $title) {
        sleep(3);
        $embed = [
            "title" => $title,
            "color" => $color,
            "fields" => $fields,
            "thumbnail" => [
                "url" => "
                https://bitcoin.org/favicon.png?1721160482"
            ],
            "timestamp" => $date,
            "footer" => [
                "text" => "This Bot Was Created By Livexords || $date",
                "icon_url" => "
                https://bitcoin.org/favicon.png?1721160482"
            ],
        ];
    
        $json_data = json_encode([
            "embeds" => [$embed]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        $response = curl_exec($ch);
        curl_close($ch);
    
        echo $response;
    }
    
    $colors = [
        "FF5733", "33FF57", "3357FF", "FF33A1", "FF8C33",
        "33FFF7", "8C33FF", "FF3333", "33FFBD", "FFBD33",
        "3333FF", "A1FF33", "FF33FF", "FF5733", "FF3357",
        "33A1FF", "57FF33", "FF33A1", "5733FF", "33FF33",
        "F1C40F", "2ECC71", "3498DB", "9B59B6", "34495E",
        "16A085", "F39C12", "D35400", "E74C3C", "C0392B",
        "BDC3C7", "7F8C8D", "95A5A6", "E67E22", "ECF0F1",
        "1ABC9C", "27AE60", "2980B9", "8E44AD", "2C3E50",
        "F0E68C", "D2B48C", "DDA0DD", "FA8072", "FF6347",
        "4682B4", "5F9EA0", "DAA520", "ADFF2F", "20B2AA",
        "32CD32", "FF1493", "DB7093", "FF7F50", "FFD700",
        "FFA07A", "AFEEEE", "CD5C5C", "808000", "6A5ACD",
        "BA55D3", "7B68EE", "B0E0E6", "FAEBD7", "FF4500",
        "006400", "FF69B4", "EE82EE", "98FB98", "DA70D6",
        "4B0082", "800080", "BDB76B", "FFF8DC", "E9967A",
        "FF00FF", "BC8F8F", "7FFF00", "FF6347", "8B4513",
        "B22222", "DC143C", "00FF7F", "00CED1", "00BFFF",
        "FF00FF", "FFD700", "00FF00", "FF0000", "8A2BE2",
        "FF4500", "ADFF2F", "FF1493", "FF8C00", "00FA9A",
        "8B008B", "1E90FF", "D2691E", "FF7F50", "FF69B4",
        "556B2F", "FFDAB9", "FFA07A", "7FFFD4", "FFE4B5"
    ];
    
    $random_color = hexdec($colors[array_rand($colors)]);

    $workersCount = $data['workersCount'];

    $profileFields = [];
    
    // Start of Selection
    $profileFields = [
        [
            "name" => "🏆 Best Difficulty",
            "value" => $data['bestDifficulty'],
            "inline" => true
        ],
        [
            "name" => "👷 Workers Count",
            "value" => $workersCount,
            "inline" => true
        ],
    ];
    
    function sendWorkerToWebhook($webhook_url,$data,$color,$date) {
        $array_ch = array_chunk($data['workers'],9);
        if ($data['workersCount'] != '0') {
        foreach($array_ch as $index => $array_ch) {
            $workers = [];
            foreach($array_ch as $worker) {
                $workers = [
                    [
                        "name" => "👤 ". $data['workers'][0]['name'],
                        "value" => "🆔 Session ID: " . $data['workers'][0]['sessionId'] . "\n" .
                        "🏆 Best Difficulty: " . number_format($data['workers'][0]['bestDifficulty'],2,',','.') . "\n" .
                        "⚙️ Hash Rate: " . number_format($data['workers'][0]['hashRate'], 8, ',', '.') . "\n" .
                        "🕒 Start Time: " . $data['workers'][0]['startTime'] . "\n" .
                        "👀 Last Seen: " . $data['workers'][0]['lastSeen'],
                        "inline" => true
                    ],
                ];
            }
            sendToWebhook($webhook_url, $workers, $color, $date, "WORKERS TIM " . ($index + 1));
            sleep(5); 
        }
    }
}

    if($walletac == "true") {
        sendToWebhook($webhook_url,$profileFields,$random_color,$date,"PROFILE");
    }
    if($workersCount != "0") {
        if($workerac == "true") {
            sendWorkerToWebhook($webhook_url,$data,$random_color,$date);
        }
    }

    http_response_code(200);
    echo json_encode(array("status" => "succes"));
?>