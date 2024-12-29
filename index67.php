<?php
$Qurl= $_POST['url'];
$url = "https://www.y2mate.com/mates/en949/analyzeV2/ajax";

echo $Qurl;
// Define the payload (form data)
$formData = [

    "k_query" => $Qurl,
    "k_page" => "home",
    "hl" => "en",
    "q_auto" => "0"
];

// // Initialize cURL
// $ch = curl_init($url);

// // Configure cURL options
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData)); // Encode form data
// curl_setopt($ch, CURLOPT_ENCODING, ""); // Enable automatic decoding 
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//     'X-Requested-With: XMLHttpRequest',
//     'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)...',
//     'Referer: https://www.y2mate.com/en949',
//     'Accept-Encoding: gzip, deflate, br',
//     'Accept: */*'
// ]);

// // Execute the request
// $response = curl_exec($ch);

// Step 1: Fetch homepage to get cookies and tokens
$homepageUrl = "https://www.y2mate.com/en949/youtube
";
$ch = curl_init($homepageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Get headers for cookies
$homepageResponse = curl_exec($ch);
curl_close($ch);
// var_dump($homepageResponse);

// Extract cookies or tokens (parse response)
preg_match_all('/Set-Cookie: (.*?);/i', $homepageResponse, $matches);
$cookies = implode('; ', $matches[1]);
// echo $cookies;
// Step 2: Use cookies in the API request
echo "Extracted Cookies: " . $cookies . "\n";
$apiUrl = "https://www.y2mate.com/en949/analyzeV2/ajax";
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
$headers = [
    "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
    "Cookie: $cookies",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
    "Referer: https://www.y2mate.com/en949",
    "X-Requested-With: XMLHttpRequest",
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);


// Check for errors
if (curl_errno($ch)) {
    echo 'Request Error: ' . curl_error($ch);
    exit;
}

// echo $response;

// Close cURL
curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);


// Process the response
if ($data['status'] === 'ok') {
    echo "Video Title: " . $data['title'] . "\n";
    echo "Video ID: " . $data['vid'] . "\n";
    print_r($data['mess']);
    // List available formats
    foreach ($data['links']['mp4'] as $key => $format) {
        echo "Quality: " . $format['q'] . ", Size: " . $format['size'] . "\n";
        echo "Download Link Key: " . $format['k'] . "\n";
    }
} else {
    echo "Error: " . $data['mess'] . "\n" . $data;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Video Downloader</title>
</head>
<body>
    <h1>YouTube Video Downloader</h1>
    
    <!-- Form to fetch available formats -->
    <form method="POST">
        <label for="url">Enter YouTube URL: </label>
        <input type="text" name="url" required>
        <button type="submit" name="fetch_formats">Fetch Formats</button>
    </form>

    <?php if (isset($formats)): ?>
        <!-- Display available video and audio formats -->
        <h2>Select Video and Audio Formats</h2>
        <form method="POST">
            <input type="hidden" name="url" value="<?= $url ?>">

            <label for="video_format">Select Video Format: </label>
            <select name="video_format" id="video_format">
                <?php foreach ($formats['video'] as $video): ?>
                    <option value="<?= substr($video, 0, strpos($video, ' ')) ?>"><?= $video ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="audio_format">Select Audio Format: </label>
            <select name="audio_format" id="audio_format">
                <?php foreach ($formats['audio'] as $audio): ?>
                    <option value="<?= substr($audio, 0, strpos($audio, ' ')) ?>"><?= $audio ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <button type="submit" name="download">Download & Merge</button>
        </form>
    <?php endif; ?>
</body>
</html>
