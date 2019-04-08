<?php

//$url = 'https://qa.moodle.net/';
//$url = 'http://127.0.0.1:8102/';
//$url = 'http://127.0.0.1:8102/len';
$url = 'http://127.0.0.1:8102/reallylong';

$h = curl_init();

curl_setopt($h, CURLOPT_URL, $url);
curl_setopt($h, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($h, CURLOPT_MAXREDIRS, 5);
curl_setopt($h, CURLOPT_HEADER, true);
curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
curl_setopt($h, CURLOPT_NOBODY, true);

$data = curl_exec($h);

if ($data === FALSE) {
    // ERROR
    echo "ERROR\n";
} else {
    $httpcode = curl_getinfo($h, CURLINFO_RESPONSE_CODE);
    $methodnotallowed = ($httpcode == 405);

    $contenttype = curl_getinfo($h, CURLINFO_CONTENT_TYPE);
    $ishtml = (strpos($contenttype, 'text/html') === 0);

    $contentlength = curl_getinfo($h, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $lengthknown = (is_double($contentlength) && $contentlength > -1);

    if (!$methodnotallowed && !$ishtml && $lengthknown) {
        // store len in db, do not GET document
        echo "HTTP/ code: $httpcode\n";
    } else {
        // GET the document
        echo "need to GET\n";
        curl_setopt($h, CURLOPT_HTTPGET, true);

        curl_setopt($h, CURLOPT_NOPROGRESS, false);
        curl_setopt($h, CURLOPT_BUFFERSIZE, 1024);
        // Need progress function for cancelling the download.
        // Do not need header function (or could wait for blank line delimiting the headers, and then evaluate content type).
        // Seems we need write function (for storing the downloaded document in a string – need to parse title from it later; when
        // we abort the transfer, the received content seems to be flushed).
        curl_setopt($h, CURLOPT_HEADERFUNCTION, function($hdl, $header) {
            echo 'HEADR: ' . preg_replace('/[\r\n]+$/', '', $header) . "\n";
            echo "HEADR-current state of the Content-Type: >" . curl_getinfo($hdl, CURLINFO_CONTENT_TYPE) . "<\n";
            return strlen($header);
        });
        $chunks = array();
        curl_setopt($h, CURLOPT_WRITEFUNCTION, function($hdl, $content) use (&$chunks) {
            echo 'WRITE: ' . preg_replace('/\r\n$/', '', $content) . "\n";
            echo "WRITE-state of the Content-Type in write function: >" . curl_getinfo($hdl, CURLINFO_CONTENT_TYPE) . "<\n";
            $chunks[] = $content;
            return strlen($content);
        });
        curl_setopt($h, CURLOPT_PROGRESSFUNCTION, function($hdl, $totaldown, $down, $totalup, $up) {
            echo "PRGRS: len=$totaldown, so far=$down, Content-Type: >" . curl_getinfo($hdl, CURLINFO_CONTENT_TYPE) . "<\n";
            echo "PRGRS-returning " . ($down > 4096 ? "abort" : "continue") . "\n";
            return ($down > 4096) ? 1 : 0;
        });

        $data = curl_exec($h);
    }
}













if ($data === FALSE) {
    if (curl_errno($h) === CURLE_ABORTED_BY_CALLBACK) {
        // we have stopped the transfer
        echo "(transfer stopped)\n";

        $httpcode = curl_getinfo($h, CURLINFO_RESPONSE_CODE);
        if ($httpcode >= 300 && $httpcode < 400) { // Redirect.
            // The stored chunks contain the _redirecting_ resource, so they are of no use.
            $data = '';
        } else {
            $data = implode($chunks);
        }
    } else {
        // real ERROR
        echo "done, ERROR\n";
    }
}

if ($data !== FALSE) {
    echo "done:\n>>$data<<\n";
}

$len = curl_getinfo($h, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

if ($len < 0) $len = 'unknown';
echo "\nlen:$len";

curl_close($h);
