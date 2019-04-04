<?php

//$url = 'https://moodle-ng.uni-ulm.de/';  // issues HTTP 303 See Other, Location: https://moodle.uni-ulm.de/login/index.php
//$url = 'https://moodle-ng.uni-ulm.de/login/index.php';  // does not issue redirect, but 200 OK
$url = 'http://127.0.0.1:8102/headgettest';  // local Express server for testing
$cookiefilelocation = 'cookiejar.txt';

$handle = curl_init();

if ($handle === FALSE) {
    echo "curl_init error\n";
    exit(1);
}

if (!curl_setopt_array($handle, array(
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'CurlTest/0.9',
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_HEADER => true,
        CURLOPT_COOKIEJAR => $cookiefilelocation,
        CURLOPT_COOKIEFILE => $cookiefilelocation,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_NOBODY => true,
))) {
    echo "curl_setopt_array error\n";
    exit(1);
}

$data = curl_exec($handle);

if ($data === FALSE) {
    echo "curl_exec error\n";
    exit(1);
}

curl_setopt($handle, CURLOPT_HTTPGET, true); // switch to GET
//curl_reset($handle);
//curl_close($handle);
//$handle = curl_init();
//if (!curl_setopt_array($handle, array(
//        CURLOPT_URL => $url,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_USERAGENT => 'CurlTest/0.9',
//        CURLOPT_MAXREDIRS => 5,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_FOLLOWLOCATION => true,
//        CURLOPT_FRESH_CONNECT => true,
//        CURLOPT_HEADER => true,
//        CURLOPT_COOKIEJAR => $cookiefilelocation,
//        CURLOPT_COOKIEFILE => $cookiefilelocation,
//        CURLOPT_SSL_VERIFYHOST => 0,
//        CURLOPT_SSL_VERIFYPEER => 0,
//        CURLOPT_FORBID_REUSE => true,
//))) {
//    echo "curl_setopt_array error\n";
//    exit(1);
//}

$data = curl_exec($handle);






$file_size         = curl_getinfo($handle, CURLINFO_SIZE_DOWNLOAD);
$content_type      = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
$download_duration = curl_getinfo($handle, CURLINFO_TOTAL_TIME);
$effective_url     = curl_getinfo($handle, CURLINFO_EFFECTIVE_URL);
$http_code         = curl_getinfo($handle, CURLINFO_HTTP_CODE);
$header_size       = curl_getinfo($handle, CURLINFO_HEADER_SIZE);

$contentlength = curl_getinfo($handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$lengthknown = (is_double($contentlength) && $contentlength > -1);

$cu_errno = curl_errno($handle);
$cu_error = curl_error($handle);

$headers = substr($data, 0, $header_size - 1);

echo "Result:\nHeaders:\n$headers\n";
echo "Body:\n" . substr($data, $header_size, 1000) . "\n";

//$header = strtok($headers, "\n");
//$httpmsg = explode(' ', $header, 3)[2];
if (preg_match_all('@(^|[\r\n])(HTTP/[^ ]+) ([0-9]+) ([^\r\n]+|$)@', $headers, $matches, PREG_SET_ORDER)) {
    $httpmsg = array_pop($matches)[4];
} else {
    $httpmsg = '';
}

echo "content_type:      $content_type\n";
echo "download_duration: $download_duration\n";
echo "effective_url:     $effective_url\n";
echo "header_size:       $header_size\n";
echo "http_code:         $http_code\n";
echo "http status msg:   $httpmsg\n";
echo "content length:    " . ($lengthknown ? $contentlength : "unknown") . "\n";
