<?php
// Test for Curl timeout

//$url = 'https://qa.moodle.net/';
//$url = 'http://127.0.0.1:8102/';
//$url = 'http://127.0.0.1:8102/len';
$url = 'http://127.0.0.1:8102/reallylong';

$h = curl_init();

curl_setopt($h, CURLOPT_URL, $url);
curl_setopt($h, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($h, CURLOPT_MAXREDIRS, 5);
curl_setopt($h, CURLOPT_RETURNTRANSFER, false);
curl_setopt($h, CURLOPT_HEADER, true);
curl_setopt($h, CURLOPT_HTTPGET, true);
curl_setopt($h, CURLOPT_BUFFERSIZE, 1024);
curl_setopt($h, CURLOPT_TIMEOUT, 10);
//curl_setopt($h, CURLOPT_TIMEOUT_MS, 2);

$active = false;
curl_setopt($h, CURLOPT_NOPROGRESS, false);
curl_setopt($h, CURLOPT_PROGRESSFUNCTION,
        function($resource, $expecteddownbytes, $downbytes, $expectedupbytes, $upbytes) use (&$active) {
    //$sizelimit = 4096;
    //return ($downbytes > $sizelimit) ? 1 : 0;
    return $active ? 1 : 0;
});

$responsenumber = 0;
curl_setopt($h, CURLOPT_HEADERFUNCTION,
        function($hdl, $header) use (&$active, &$responsenumber) {
    if (preg_match('@^HTTP/[^ ]+ ([0-9]+) ([^\r\n]*)@', $header)) {
        $responsenumber++;
        echo "===== RESPONSE $responsenumber STARTS =====\n";
    }
    if (preg_match('@^X-@', $header)) {
        if ($responsenumber == 2) {
            echo "***** ACTIVATING\n";
            $active = true;
        }
    }
    return strlen($header);
});

$chunks = array();
curl_setopt($h, CURLOPT_WRITEFUNCTION, function($hdl, $content) use (&$chunks) {
    $chunks[] = $content;
    return strlen($content);
});

$data = curl_exec($h);

if ($data === FALSE) {
    $err = curl_errno($h);
    $aborted = $err === CURLE_ABORTED_BY_CALLBACK || $err == CURLE_OPERATION_TIMEDOUT;
    if (!$aborted) {
        // ERROR
        echo "ERROR\n";
    }
}
if ($data !== FALSE || $aborted) {
    $data = implode($chunks);
    unset($chunks);
    echo "done:\n>>$data<<\n";
}

$httpcode = curl_getinfo($h, CURLINFO_RESPONSE_CODE);
$methodnotallowed = ($httpcode == 405);

$contenttype = curl_getinfo($h, CURLINFO_CONTENT_TYPE);
$ishtml = (strpos($contenttype, 'text/html') === 0);

$len = curl_getinfo($h, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$lengthknown = (is_double($len) && $len > -1);

// split into header and body
$hdrlen = curl_getinfo($h, CURLINFO_HEADER_SIZE);
if ($data !== FALSE) {
    $hdr = substr($data, 0, $hdrlen);
    $bdy = substr($data, $hdrlen);
} else {
    $hdr = $bdy = null;
}

curl_close($h);

if ($len < 0) $len = 'unknown';
echo "The HTTP/ code: $httpcode\n";
echo "Content-Type  : $contenttype\n";
echo "Is HTML       : $ishtml\n";
echo "Header length : $hdrlen\n";
echo "Content-Length: $len\n";
echo "Length known  : " . ($lengthknown ? 'true' : 'false') . "\n";
echo "\n";
echo "Header: >$hdr<\n";
echo "Body: >$bdy<\n";
