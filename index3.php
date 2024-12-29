<form method="post" action="">
    <h1>PHP YouTube Video Downloader Script</h1>
    <div class="row">
        <input type="text" class="inline-block" name="youtube-video-url">
        <button type="submit" name="submit" id="submit">Download Video</button>
    </div>
</form>
<?php
$key = "AIzaSyB0vrE7kz13WoJC1qLI7ifPclhAoDeFUI";
if (isset($_POST['youtube-video-url'])) {
    $videoUrl = $_POST['youtube-video-url'];
    ?>
<p>
    URL: <a href="<?php echo $videoUrl;?>"><?php echo $videoUrl;?></a>
</p>
<?php
}
if (isset($_POST['submit'])) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match);
    $youtubeVideoId = $match[1];
    require './youtube-video-meta.php';
    $videoMeta = json_decode(getYoutubeVideoMeta($youtubeVideoId, $key));
    $videoThumbnails = $videoMeta->videoDetails->thumbnail->thumbnails;
    $thumbnail = end($videoThumbnails)->url;
    ?>
<p>
    <img src="<?php echo $thumbnail; ?>">
</p>
<?php $videoTitle = $videoMeta->videoDetails->title; ?>
<h2>Video title: <?php echo $videoTitle; ?></h2>
<?php
    $shortDescription = $videoMeta->videoDetails->shortDescription;
    ?>
<p><?php echo str_split($shortDescription, 100)[0];?></p>
<?php
    $videoFormats = $videoMeta->streamingData->formats;
    if (! empty($videoFormats)) {
        if (@$videoFormats[0]->url == "") {
            ?>
<p>
    <strong>This YouTube video cannot be downloaded by the downloader!</strong><?php
            $signature = "https://example.com?" . $videoFormats[0]->signatureCipher;
            parse_str(parse_url($signature, PHP_URL_QUERY), $parse_signature);
            $url = $parse_signature['url'] . "&sig=" . $parse_signature['s'];
            ?>
        </p>
<?php
            die();
        }
        ?>
<h3>With Video & Sound</h3>
<table class="striped">
    <tr>
        <th>Video URL</th>
        <th>Type</th>
        <th>Quality</th>
        <th>Download Video</th>
    </tr>
                    <?php
        foreach ($videoFormats as $videoFormat) {
            if (@$videoFormat->url == "") {
                $signature = "https://example.com?" . $videoFormat->signatureCipher;
                parse_str(parse_url($signature, PHP_URL_QUERY), $parse_signature);
                $url = $parse_signature['url'] . "&sig=" . $parse_signature['s'];
            } else {
                $url = $videoFormat->url;
            }
            ?>
            <tr>
        <td><a href="<?php echo $url; ?>">View Video</a></td>
        <td><?php if($videoFormat->mimeType) echo explode(";",explode("/",$videoFormat->mimeType)[1])[0]; else echo "Unknown";?></td>
        <td><?php if($videoFormat->qualityLabel) echo $videoFormat->qualityLabel; else echo "Unknown"; ?></td>
        <td><a
            href="video-downloader.php?link=<?php echo urlencode($url)?>&title=<?php echo urlencode($videoTitle)?>&type=<?php if($videoFormat->mimeType) echo explode(";",explode("/",$videoFormat->mimeType)[1])[0]; else echo "mp4";?>">
                Download Video</a></td>
    </tr>
                    <?php } ?>
                </table>
<?php
        // if you wish to provide formats based on different formats
        // then keep the below two lines
        $adaptiveFormats = $videoMeta->streamingData->adaptiveFormats;
        include 'adaptive-formats.php';
        ?>
    <?php
    }
}
?>