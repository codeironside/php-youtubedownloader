<h3>YouTube Videos Adaptive Formats</h3>
<table class="striped">
    <tr>
        <th>Type</th>
        <th>Quality</th>
        <th>Download Video</th>
    </tr>
            <?php
            foreach ($adaptiveFormats as $videoFormat) {
                try {
                    $url = $videoFormat->url;
                } catch (Exception $e) {
                    $signature = $videoFormat->signatureCipher;
                    parse_str(parse_url($signature, PHP_URL_QUERY), $parse_signature);
                    $url = $parse_signature['url'];
                }
                ?>
                <tr>
        <td><?php if(@$videoFormat->mimeType) echo explode(";",explode("/",$videoFormat->mimeType)[1])[0]; else echo "Unknown";?></td>
        <td><?php if(@$videoFormat->qualityLabel) echo $videoFormat->qualityLabel; else echo "Unknown"; ?></td>
        <td><a
            href="video-downloader.php?link=<?php print urlencode($url)?>&title=<?php print urlencode($videoTitle)?>&type=<?php if($videoFormat->mimeType) echo explode(";",explode("/",$videoFormat->mimeType)[1])[0]; else echo "mp4";?>">Download
                Video</a></td>
    </tr>
            <?php }?>
</table>