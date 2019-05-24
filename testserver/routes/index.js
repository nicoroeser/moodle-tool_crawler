var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res) {
    console.log("method: " + req.method);
    res.set('Allow', 'GET, POST');
    res.status(405, 'Method Not Allowed (you stupid client)');
    res.send('nope');
    res.end();
  //res.render('index', { title: 'Express' });
});

router.get('/len', function(req, res) {
    //res.set('Content-Length', '22');
    res.set('Content-Type', 'text/html');
    res.status(200, 'OK (Yeah)');
    res.write('<!DOCTYPE html>\n<html><head><title>This document is longer than 22 octets.</title></head><body>foo</body><html>\n');
    res.write('\n');
    res.write('\n');
    res.end();
});

router.get('/headgettest', function(req, res) {
    console.log("method: " + req.method);
    if (req.method == 'HEAD') {
        res.set('Content-Type', 'text/x-foobar');
        res.set('Content-Length', '42');
        res.status(200);
        res.statusMessage = 'All Right';
    } else {
        res.status(200);
        res.statusMessage = 'Here You Go';
        //res.set('Content-Length', '12');
        //res.set('Content-Type', 'definitely/broken');
        res.write(Buffer.from('Hello World\n'));
        res.write(Buffer.from('Hello2 World2\n'));
    }

    res.end();
});

router.get('/reallylong', function(req, res) {
    res.set('Content-Type', '             text/plain     ');
    //res.set('Location', '/reallylong1.5');
    for (i = 0; i < 10000; i++) {
        res.set('X-Foobar-Location-Blah-Filler-' + i, 'Useless header content (' + i + ') after sending Location header field');
    }
    res.status(302, 'Moved Temporarily');
    res.write('<!DOCTYPE html>\n<html><head><title>reallylong document has moved</title></head>\n');
    res.write('<body><p><a href="/reallylong1.5">really long document</a> has moved.</p>\n');
    for (i = 0; i < 100000; i++) {
        res.write('<p>This HTML document has moved.</p>\n');
        res.write('<p>Really! It has moved.</p>\n');
        res.write('<p>Only temporarily. But it has moved!</p>\n');
    }
    res.write('</body><html>\n');
    res.end();
});

router.get('/reallylong1.5', function(req, res) {
    res.set('Content-Type', 'text/html; charset=UTF-8');
    for (i = 0; i < 9; i++) {
        res.set('X-Intermediate-Redirect-Filler-' + ('000000' + i).slice(-7),
                'Very long header content which is really not necessary. xy');
                    // should produce a header line which is 100 octets in length (including CRLF)
    }
    res.set('Location', '/reallylong2');
    res.status(302, 'Moved Temporarily AGAIN');
    r = '<!DOCTYPE html>\n<html><head><title>reallylong document has moved (2nd)</title></head>\n';
    r += '<body><p><a href="/reallylong2">really long document</a> has moved.</p>\n';
    for (i = 0; i < 3000000; i++) {
        r += '<p>This fine HTML document has moved.</p>\n';
        r += '<p>Just temporarily.</p>\n';
    }
    r += '</body><html>\n';
    res.send(r);
    res.end();
});

router.get('/reallylong2', function(req, res) {
    //res.set('Content-Length', '22');
    res.set('Content-Type', 'text/html');
    for (i = 0; i < 2; i++) {
        res.set('X-Foobar-Filler-' + i, 'Useless header content (' + i + ')');
    }
    res.status(200, 'OK');
    doc = '';
    doc += '<!DOCTYPE html>\n<html><head><title>This is a really long document.</title></head><body><p>foo</p>\n';
    for (i = 0; i < 1; i++) {
        doc += '<p>This HTML document may actually be the longest one on earth. Who knows?</p>\n';
        doc += '<p>Here comes even more content. Blah, blah. You should stop downloading. Go outside, enjoy the sun!</p>\n';
    }
    doc += '</body><html>\n';
    //res.write(doc);
    //res.write(' \n');
    res.send(doc);
    res.end();
});

module.exports = router;
