<?php
require 'vendor/autoload.php';
$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');

$apiKey = getenv('API');;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Access the 'username' input field
    $username = $_POST['url'];
    echo $username;
}
$videoUrl = $username;
preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match);
$youtubeVideoId = $match[1];
$videoMeta = json_decode(getYoutubeVideoMeta($youtubeVideoId, $apiKey));
$videoTitle = $videoMeta->videoDetails->title;
$videoFormats = $videoMeta->streamingData->formats;
foreach ($videoFormats as $videoFormat) {
    $url = $videoFormat->url;
    if ($videoFormat->mimeType)
        $mimeType = explode(";", explode("/", $videoFormat->mimeType)[1])[0];
    else
        $mimeType = "mp4";
    ?>
<a
    href="video-downloader.php?link=<?php echo urlencode($url)?>&title=<?php echo urlencode($videoTitle)?>&type=<?php echo $mimeType; ?>">
    Download Video</a>
<?php
}

function getYoutubeVideoMeta($videoId, $key)
{
    $ch = curl_init();
    $curlUrl = 'https://www.youtube.com/youtubei/v1/player?key=' . $key;
    curl_setopt($ch, CURLOPT_URL, $curlUrl);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $curlOptions = '{"context": {"client": {"hl": "en","clientName": "WEB",
        "clientVersion": "2.20210721.00.00","clientFormFactor": "UNKNOWN_FORM_FACTOR","clientScreen": "WATCH",
        "mainAppWebInfo": {"graftUrl": "/watch?v=' . $videoId . '",}},"user": {"lockedSafetyMode": false},
        "request": {"useSsl": true,"internalExperimentFlags": [],"consistencyTokenJars": []}},
        "videoId": "' . $videoId . '",  "playbackContext": {"contentPlaybackContext":
        {"vis": 0,"splay": false,"autoCaptionsDefaultOn": false,
        "autonavState": "STATE_NONE","html5Preference": "HTML5_PREF_WANTS","lactMilliseconds": "-1"}},
        "racyCheckOk": false,  "contentCheckOk": false}';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlOptions);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $curlResult = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $curlResult;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youtube Downloader</title>
</head>
<body>
    <header>
        
    </header>
    <h1></h1>
    <form action="" method="POST">

<h1>Youtube Downloader</h1>
    <input type="text" name="url" id="url">
    <input type="submit" name="submit
    " value="Submit">
    </form>
    <footer>

    </footer>
</body>
</html>