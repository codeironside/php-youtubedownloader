<?php

// function fetchToken() {
//     $url = "https://www.y2mate.com/youtube/";

//     // Initialize cURL session
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HEADER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36"
//     ]);

//     $response = curl_exec($ch);
//     curl_close($ch);
//     echo $response;
//     // Extract `cf_token` from response (modify regex based on actual token format)
//     preg_match('/cf_token=([a-zA-Z0-9._-]+)/', $response, $matches);
//     return $matches[1] ?? null;
// }


function fetchToken() {
    $url = "https://www.y2mate.com/en846/youtube/";
    $cookieFile = __DIR__ . '/cookies.txt'; // Path to store cookies

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); // Save cookies
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); // Read cookies
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36"
    ]);

    $response = curl_exec($ch);
    echo $response;
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "Failed to retrieve cf_token. HTTP Status: $httpCode\n";
        return null;
    }

    // Extract `cf_token` from response
    preg_match('/cf_token=([a-zA-Z0-9._-]+)/', $response, $matches);
    return $matches[1] ?? null;
}

function fetchVideoFormats($cf_token, $video_url) {
    $url = "https://www.y2mate.com/mates/en846/analyzeV2/ajax";

    $payload = http_build_query([
        "cf_token" => $cf_token,
        "k_query" => $video_url,
        "k_page" => "Youtube",
        "hl" => "en",
        "q_auto" => "0"
    ]);

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "X-Requested-With: XMLHttpRequest",
        "Referer: https://www.y2mate.com/youtube/search"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function displayFormats($formats) {
    echo "<table border='1'>";
    echo "<tr><th>Type</th><th>Quality</th><th>Size</th><th>Download Link</th></tr>";

    foreach ($formats as $type => $qualityList) {
        foreach ($qualityList as $quality) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($type) . "</td>";
            echo "<td>" . htmlspecialchars($quality['q_text'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($quality['size'] ?? '') . "</td>";
            echo "<td><a href='https://www.y2mate.com/download/" . urlencode($quality['k']) . "'>Download</a></td>";
            echo "</tr>";
        }
    }

    echo "</table>";
}

// Main logic
$video_url = "https://www.youtube.com/watch?v=NRi9jFLuG5o";
$cf_token = fetchToken();

if ($cf_token) {
    $response = fetchVideoFormats($cf_token, $video_url);
    if (!empty($response['links'])) {
        displayFormats($response['links']);
    } else {
        echo "No formats found or an error occurred.";
    }
} else {
    echo "Failed to retrieve cf_token.";
}

?>
