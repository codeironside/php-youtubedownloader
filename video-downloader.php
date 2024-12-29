<?php
// this PHP script reads and downloads the video from YouTube
$downloadURL = urldecode($_GET['link']);
echo 'here is '.$downloadURL.'  ';
$downloadFileName = urldecode($_GET['title']) . '.' . urldecode($_GET['type']);
if (! empty($downloadURL) && substr($downloadURL, 0, 8) === 'https://') {
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment;filename=\"$downloadFileName\"");
    header("Content-Transfer-Encoding: binary");
    readfile($downloadURL);
}
?>

