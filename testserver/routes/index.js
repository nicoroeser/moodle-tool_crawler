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

module.exports = router;
